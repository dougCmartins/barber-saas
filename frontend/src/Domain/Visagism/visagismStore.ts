import { ref } from 'vue'
import { defineStore } from 'pinia'
import type {
  AnalyzeFacePayload,
  ConsultationType,
  HaircutSuggestion,
  ManualConsultation,
  OverrideVisagismPayload,
  SaveManualPayload,
  VisagismActiveRecord,
  VisagismExistingState,
  VisagismResult,
  VisagismStep,
} from '@/Domain/Visagism/visagismModel'
import { ConsultationType as Type, VisagismStep as Step } from '@/Domain/Visagism/visagismModel'
import { http } from '@/http/client'

function readErrorCode(error: unknown): string | null {
  if (typeof error !== 'object' || error === null || Array.isArray(error)) {
    return null
  }

  const code = (error as Record<string, unknown>).code

  return typeof code === 'string' ? code : null
}

export const useVisagismStore = defineStore('visagism', () => {
  const step = ref<VisagismStep>(Step.Onboarding)
  const result = ref<VisagismResult | null>(null)
  const manual = ref<ManualConsultation | null>(null)
  const existing = ref<VisagismExistingState | null>(null)
  const selectedSuggestion = ref<HaircutSuggestion | null>(null)
  const selectedProductIds = ref<number[]>([])
  const sheetOpen = ref(false)
  const overrideOpen = ref(false)
  const rescueOpen = ref(false)
  const rescueType = ref<ConsultationType | null>(null)
  const error = ref(false)
  const limitExceeded = ref(false)
  const isProcessing = ref(false)
  let requestId = 0

  function applyActive(record: VisagismActiveRecord): void {
    if (record.consultation_type === Type.Ai && record.result) {
      result.value = record.result
      selectedSuggestion.value = record.result.suggestions[0] ?? null
      manual.value = null
      step.value = Step.Results
      return
    }

    if (record.consultation_type === Type.Manual && record.manual) {
      manual.value = record.manual
      result.value = null
      selectedSuggestion.value = null
      step.value = Step.ManualDone
    }
  }

  async function checkExisting(slug: string): Promise<void> {
    isProcessing.value = true
    error.value = false
    limitExceeded.value = false

    try {
      const response = await http<VisagismExistingState>(
        `/barbershops/${encodeURIComponent(slug)}/visagism/existing`,
      )
      existing.value = response.data

      if (response.data.active) {
        applyActive(response.data.active)
        return
      }

      step.value = Step.Onboarding
    } catch {
      error.value = true
      step.value = Step.Onboarding
    } finally {
      isProcessing.value = false
    }
  }

  function choosePath(type: ConsultationType): void {
    const trashed =
      type === Type.Ai ? existing.value?.trashed_ai : existing.value?.trashed_manual

    if (trashed) {
      rescueType.value = type
      rescueOpen.value = true
      return
    }

    step.value = type === Type.Ai ? Step.Capture : Step.ManualForm
  }

  async function restore(slug: string): Promise<void> {
    if (!rescueType.value) {
      return
    }

    isProcessing.value = true

    try {
      const response = await http<VisagismActiveRecord>(
        `/barbershops/${encodeURIComponent(slug)}/visagism/restore`,
        {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ consultation_type: rescueType.value }),
        },
      )
      existing.value = {
        active: response.data,
        trashed_ai: rescueType.value === Type.Ai ? null : (existing.value?.trashed_ai ?? null),
        trashed_manual:
          rescueType.value === Type.Manual ? null : (existing.value?.trashed_manual ?? null),
      }
      applyActive(response.data)
      rescueOpen.value = false
      rescueType.value = null
    } catch {
      error.value = true
    } finally {
      isProcessing.value = false
    }
  }

  async function restartFromScratch(slug: string): Promise<void> {
    if (!rescueType.value) {
      return
    }

    const type = rescueType.value
    isProcessing.value = true

    try {
      await http(`/barbershops/${encodeURIComponent(slug)}/visagism/force-delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ consultation_type: type }),
      })

      if (existing.value) {
        existing.value = {
          ...existing.value,
          trashed_ai: type === Type.Ai ? null : existing.value.trashed_ai,
          trashed_manual: type === Type.Manual ? null : existing.value.trashed_manual,
        }
      }

      rescueOpen.value = false
      rescueType.value = null
      step.value = type === Type.Ai ? Step.Capture : Step.ManualForm
    } catch {
      error.value = true
    } finally {
      isProcessing.value = false
    }
  }

  function closeRescue(): void {
    rescueOpen.value = false
    rescueType.value = null
  }

  async function analyze(slug: string, payload: AnalyzeFacePayload = {}): Promise<void> {
    const current = ++requestId
    step.value = Step.Analyzing
    isProcessing.value = true
    error.value = false
    limitExceeded.value = false
    result.value = null
    selectedSuggestion.value = null
    selectedProductIds.value = []
    sheetOpen.value = false

    try {
      const response = await http<VisagismResult>(
        `/barbershops/${encodeURIComponent(slug)}/visagism/analyze`,
        {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            image: payload.image ?? null,
            age: payload.age ?? null,
            hair_texture: payload.hair_texture ?? null,
            beard_density: payload.beard_density ?? null,
          }),
        },
      )

      if (current !== requestId) {
        return
      }

      result.value = response.data
      selectedSuggestion.value = response.data.suggestions[0] ?? null
      manual.value = null
      step.value = Step.Results
      await refreshExistingQuiet(slug)
    } catch (err) {
      if (current !== requestId) {
        return
      }

      if (readErrorCode(err) === 'VISAGISM_LIMIT_EXCEEDED') {
        limitExceeded.value = true
      } else {
        error.value = true
      }

      step.value = Step.Capture
    } finally {
      if (current === requestId) {
        isProcessing.value = false
      }
    }
  }

  async function override(slug: string, payload: OverrideVisagismPayload): Promise<void> {
    isProcessing.value = true
    error.value = false

    try {
      const response = await http<VisagismResult>(
        `/barbershops/${encodeURIComponent(slug)}/visagism/override`,
        {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload),
        },
      )

      result.value = response.data
      selectedSuggestion.value = response.data.suggestions[0] ?? null
      overrideOpen.value = false
      step.value = Step.Results
    } catch {
      error.value = true
    } finally {
      isProcessing.value = false
    }
  }

  async function saveManual(slug: string, payload: SaveManualPayload): Promise<void> {
    isProcessing.value = true
    error.value = false

    try {
      const response = await http<ManualConsultation>(
        `/barbershops/${encodeURIComponent(slug)}/visagism/manual`,
        {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload),
        },
      )

      manual.value = response.data
      result.value = null
      step.value = Step.ManualDone
      await refreshExistingQuiet(slug)
    } catch {
      error.value = true
    } finally {
      isProcessing.value = false
    }
  }

  async function refreshExistingQuiet(slug: string): Promise<void> {
    try {
      const response = await http<VisagismExistingState>(
        `/barbershops/${encodeURIComponent(slug)}/visagism/existing`,
      )
      existing.value = response.data
    } catch {
      // keep local state
    }
  }

  function selectSuggestion(suggestion: HaircutSuggestion): void {
    selectedSuggestion.value = suggestion
  }

  function openSheet(): void {
    if (!selectedSuggestion.value) {
      return
    }

    sheetOpen.value = true
  }

  function closeSheet(): void {
    sheetOpen.value = false
  }

  function openOverride(): void {
    overrideOpen.value = true
  }

  function closeOverride(): void {
    overrideOpen.value = false
  }

  function toggleProduct(productId: number): void {
    if (selectedProductIds.value.includes(productId)) {
      selectedProductIds.value = selectedProductIds.value.filter((id) => id !== productId)
      return
    }

    selectedProductIds.value = [...selectedProductIds.value, productId]
  }

  function bookingQuery(): { service?: string; product?: string } {
    const query: { service?: string; product?: string } = {}

    if (selectedSuggestion.value) {
      query.service = String(selectedSuggestion.value.service_id)
    }

    const selectedProducts = (result.value?.upsell_products ?? []).filter((item) =>
      selectedProductIds.value.includes(item.id),
    )
    const firstProduct = selectedProducts[0]

    if (firstProduct) {
      query.product = String(firstProduct.service_id)
    }

    return query
  }

  function reset(): void {
    requestId += 1
    step.value = Step.Onboarding
    result.value = null
    manual.value = null
    existing.value = null
    selectedSuggestion.value = null
    selectedProductIds.value = []
    sheetOpen.value = false
    overrideOpen.value = false
    rescueOpen.value = false
    rescueType.value = null
    error.value = false
    limitExceeded.value = false
    isProcessing.value = false
  }

  return {
    step,
    result,
    manual,
    existing,
    selectedSuggestion,
    selectedProductIds,
    sheetOpen,
    overrideOpen,
    rescueOpen,
    rescueType,
    error,
    limitExceeded,
    isProcessing,
    checkExisting,
    choosePath,
    restore,
    restartFromScratch,
    closeRescue,
    analyze,
    override,
    saveManual,
    selectSuggestion,
    openSheet,
    closeSheet,
    openOverride,
    closeOverride,
    toggleProduct,
    bookingQuery,
    reset,
  }
})
