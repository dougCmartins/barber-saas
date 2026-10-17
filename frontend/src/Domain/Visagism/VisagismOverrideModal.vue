<script setup lang="ts">
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import type { FaceShape, HairTexture, OverrideVisagismPayload, VisagismResult } from '@/Domain/Visagism/visagismModel'
import { FaceShape as Faces, HairTexture as Hairs } from '@/Domain/Visagism/visagismModel'
import { buttonClasses } from '@/ui/buttonClasses'

const props = defineProps<{
  open: boolean
  result: VisagismResult | null
  processing?: boolean
}>()

const emit = defineEmits<{
  close: []
  submit: [payload: OverrideVisagismPayload]
}>()

const { t } = useI18n()
const faceShape = ref<string>(Faces.Square)
const hairType = ref<string>(Hairs.Wavy)
const feedbackReason = ref('')
const localError = ref(false)

const faceOptions = Object.values(Faces) as FaceShape[]
const hairOptions = Object.values(Hairs) as HairTexture[]

watch(
  () => props.open,
  (open) => {
    if (!open) {
      return
    }

    faceShape.value = props.result?.face_shape ?? Faces.Square
    hairType.value = props.result?.hair_type ?? Hairs.Wavy
    feedbackReason.value = ''
    localError.value = false
  },
)

function submit(): void {
  localError.value = false

  if (!feedbackReason.value.trim()) {
    localError.value = true
    return
  }

  emit('submit', {
    face_shape: faceShape.value,
    hair_type: hairType.value,
    feedback_reason: feedbackReason.value.trim(),
  })
}
</script>

<template>
  <Teleport to="body">
    <div
      v-if="open"
      class="fixed inset-0 z-50 flex items-end justify-center bg-background/70 backdrop-blur-sm"
      role="dialog"
      aria-modal="true"
      :aria-label="t('visagism.overrideTitle')"
      @click.self="emit('close')"
    >
      <div class="mx-auto max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-t-2xl border-t border-border bg-card px-4 pb-8 pt-5 shadow-lg">
        <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-border" />
        <h2 class="font-display text-2xl font-semibold uppercase tracking-wide text-foreground">
          {{ t('visagism.overrideTitle') }}
        </h2>
        <p class="mt-2 text-sm leading-6 text-muted-foreground">{{ t('visagism.overrideSubtitle') }}</p>

        <div class="mt-5 flex flex-col gap-4">
          <label class="flex flex-col gap-2">
            <span class="text-xs uppercase tracking-wide text-muted-foreground">
              {{ t('visagism.overrideFaceLabel') }}
            </span>
            <select
              v-model="faceShape"
              class="rounded-lg border border-border bg-background px-3 py-2.5 text-sm text-foreground outline-none focus:border-primary"
            >
              <option v-for="option in faceOptions" :key="option" :value="option">
                {{ t(`visagism.faceShapes.${option}`) }}
              </option>
            </select>
          </label>

          <label class="flex flex-col gap-2">
            <span class="text-xs uppercase tracking-wide text-muted-foreground">
              {{ t('visagism.overrideHairLabel') }}
            </span>
            <select
              v-model="hairType"
              class="rounded-lg border border-border bg-background px-3 py-2.5 text-sm text-foreground outline-none focus:border-primary"
            >
              <option v-for="option in hairOptions" :key="option" :value="option">
                {{ t(`visagism.hairTypes.${option}`) }}
              </option>
            </select>
          </label>

          <label class="flex flex-col gap-2">
            <span class="text-xs uppercase tracking-wide text-muted-foreground">
              {{ t('visagism.overrideFeedbackLabel') }}
            </span>
            <textarea
              v-model="feedbackReason"
              rows="4"
              class="rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground outline-none focus:border-primary"
              :placeholder="t('visagism.overrideFeedbackPlaceholder')"
            />
          </label>

          <p v-if="localError" class="text-sm text-destructive" role="alert">
            {{ t('visagism.overrideFeedbackRequired') }}
          </p>

          <button
            type="button"
            :class="buttonClasses('default', 'lg', 'h-12 w-full')"
            :disabled="processing"
            @click="submit"
          >
            {{ t('visagism.overrideSubmit') }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>
