<script setup lang="ts">
import { ArrowLeft } from '@lucide/vue'
import { computed, onMounted, watch } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useSessionStore } from '@/Domain/Identity/sessionStore'
import VisagismCapture from '@/Domain/Visagism/VisagismCapture.vue'
import VisagismManualDone from '@/Domain/Visagism/VisagismManualDone.vue'
import VisagismManualForm from '@/Domain/Visagism/VisagismManualForm.vue'
import VisagismOnboarding from '@/Domain/Visagism/VisagismOnboarding.vue'
import VisagismOverrideModal from '@/Domain/Visagism/VisagismOverrideModal.vue'
import VisagismRescueModal from '@/Domain/Visagism/VisagismRescueModal.vue'
import VisagismResults from '@/Domain/Visagism/VisagismResults.vue'
import VisagismUpsellModal from '@/Domain/Visagism/VisagismUpsellModal.vue'
import { ConsultationType, VisagismStep } from '@/Domain/Visagism/visagismModel'
import type { OverrideVisagismPayload, SaveManualPayload } from '@/Domain/Visagism/visagismModel'
import { useVisagismStore } from '@/Domain/Visagism/visagismStore'
import { buttonClasses } from '@/ui/buttonClasses'

const route = useRoute()
const router = useRouter()
const session = useSessionStore()
const { t } = useI18n()
const store = useVisagismStore()

const slug = computed(() => {
  const value = route.params.slug

  return typeof value === 'string' ? value : ''
})

const homeTo = computed(() =>
  slug.value ? { name: 'public-home' as const, params: { slug: slug.value } } : { name: 'public-home-missing' as const },
)

const totalLabel = computed(() => {
  const cutPrice = store.selectedSuggestion?.price ?? 0
  const products = store.result?.upsell_products ?? []
  const selected = products.filter((item) => store.selectedProductIds.includes(item.id))
  const productsTotal = selected.reduce((sum, item) => sum + item.price, 0)

  return (cutPrice + productsTotal).toFixed(2).replace('.', ',')
})

async function bootstrap(): Promise<void> {
  store.reset()
  document.title = t('visagism.pageTitle')

  if (!session.token) {
    await router.replace({ name: 'login', query: { redirect: route.fullPath } })
    return
  }

  if (slug.value) {
    await store.checkExisting(slug.value)
  }
}

onMounted(() => {
  void bootstrap()
})

watch(slug, () => {
  void bootstrap()
})

async function fileToPayload(file: File): Promise<void> {
  if (!slug.value) {
    return
  }

  const image = await readAsDataUrl(file)
  await store.analyze(slug.value, { image })
}

function readAsDataUrl(file: File): Promise<string> {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.onload = () => resolve(String(reader.result ?? ''))
    reader.onerror = () => reject(reader.error)
    reader.readAsDataURL(file)
  })
}

function confirmBooking(): void {
  const query = store.bookingQuery()
  store.closeSheet()

  void router.push({
    name: 'public-booking',
    params: { slug: slug.value },
    query,
  })
}

function goBookingFromManual(): void {
  void router.push({
    name: 'public-booking',
    params: { slug: slug.value },
  })
}

async function submitOverride(payload: OverrideVisagismPayload): Promise<void> {
  if (!slug.value) {
    return
  }

  await store.override(slug.value, payload)
}

async function submitManual(payload: SaveManualPayload): Promise<void> {
  if (!slug.value) {
    return
  }

  await store.saveManual(slug.value, payload)
}
</script>

<template>
  <main class="mx-auto flex min-h-screen max-w-lg flex-col gap-4 bg-background px-4 pb-10 pt-4 text-foreground">
    <div class="flex items-center gap-2">
      <RouterLink :to="homeTo" :class="buttonClasses('ghost', 'icon')" :aria-label="t('visagism.back')">
        <ArrowLeft class="size-4" />
      </RouterLink>
    </div>

    <Transition name="visagism-fade" mode="out-in">
      <VisagismOnboarding
        v-if="store.step === VisagismStep.Onboarding"
        key="onboarding"
        @choose-ai="store.choosePath(ConsultationType.Ai)"
        @choose-manual="store.choosePath(ConsultationType.Manual)"
      />

      <VisagismCapture
        v-else-if="store.step === VisagismStep.Capture"
        key="capture"
        :error="store.error || store.limitExceeded"
        :error-message="store.limitExceeded ? t('visagism.limitExceeded') : undefined"
        @capture="fileToPayload"
        @upload="fileToPayload"
      />

      <section
        v-else-if="store.step === VisagismStep.Analyzing"
        key="analyzing"
        class="flex flex-1 flex-col items-center justify-center gap-4 py-16 text-center"
      >
        <div
          class="size-14 animate-pulse rounded-full border border-primary/40 bg-primary/15 shadow-[0_0_24px_rgba(212,175,55,0.25)]"
          aria-hidden="true"
        />
        <h1 class="font-display text-2xl font-semibold uppercase tracking-wide text-foreground">
          {{ t('visagism.analyzingTitle') }}
        </h1>
        <p class="max-w-xs text-sm leading-6 text-muted-foreground">
          {{ t('visagism.analyzingBody') }}
        </p>
      </section>

      <VisagismResults
        v-else-if="store.step === VisagismStep.Results && store.result"
        key="results"
        :result="store.result"
        :selected-suggestion-id="store.selectedSuggestion?.id ?? null"
        @select="store.selectSuggestion"
        @choose="store.openSheet"
        @override="store.openOverride"
      />

      <VisagismManualForm
        v-else-if="store.step === VisagismStep.ManualForm"
        key="manual-form"
        :processing="store.isProcessing"
        :error="store.error"
        @submit="submitManual"
      />

      <VisagismManualDone
        v-else-if="store.step === VisagismStep.ManualDone && store.manual"
        key="manual-done"
        :manual="store.manual"
        @booking="goBookingFromManual"
        @back="store.step = VisagismStep.Onboarding"
      />
    </Transition>

    <VisagismUpsellModal
      :open="store.sheetOpen"
      :suggestion="store.selectedSuggestion"
      :products="store.result?.upsell_products ?? []"
      :selected-product-ids="store.selectedProductIds"
      :total-label="totalLabel"
      @close="store.closeSheet"
      @toggle-product="store.toggleProduct"
      @confirm="confirmBooking"
    />

    <VisagismOverrideModal
      :open="store.overrideOpen"
      :result="store.result"
      :processing="store.isProcessing"
      @close="store.closeOverride"
      @submit="submitOverride"
    />

    <VisagismRescueModal
      :open="store.rescueOpen"
      :consultation-type="store.rescueType"
      :processing="store.isProcessing"
      @close="store.closeRescue"
      @restore="store.restore(slug)"
      @restart="store.restartFromScratch(slug)"
    />
  </main>
</template>

<style scoped>
.visagism-fade-enter-active,
.visagism-fade-leave-active {
  transition:
    opacity 220ms ease,
    transform 220ms ease;
}

.visagism-fade-enter-from,
.visagism-fade-leave-to {
  opacity: 0;
  transform: translateX(12px);
}
</style>
