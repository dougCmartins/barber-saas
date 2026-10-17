<script setup lang="ts">
import { Camera, ImagePlus, Sparkles } from '@lucide/vue'
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { buttonClasses } from '@/ui/buttonClasses'

const props = withDefaults(
  defineProps<{
    error?: boolean
    errorMessage?: string
  }>(),
  {
    error: false,
    errorMessage: undefined,
  },
)

const emit = defineEmits<{
  capture: [file: File]
  upload: [file: File]
}>()

const { t } = useI18n()
const cameraInput = ref<HTMLInputElement | null>(null)
const galleryInput = ref<HTMLInputElement | null>(null)

function openCamera(): void {
  cameraInput.value?.click()
}

function openGallery(): void {
  galleryInput.value?.click()
}

function onCameraChange(event: Event): void {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]

  if (file) {
    emit('capture', file)
  }

  input.value = ''
}

function onGalleryChange(event: Event): void {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]

  if (file) {
    emit('upload', file)
  }

  input.value = ''
}
</script>

<template>
  <section class="flex flex-1 flex-col gap-4">
    <header class="flex items-center gap-3 pt-2">
      <span class="grid size-10 place-items-center rounded-md bg-primary text-primary-foreground">
        <Sparkles class="size-5" />
      </span>
      <div>
        <h1 class="font-display text-2xl font-semibold uppercase tracking-wide text-foreground">
          {{ t('visagism.title') }}
        </h1>
        <p class="text-sm text-muted-foreground">{{ t('visagism.captureSubtitle') }}</p>
      </div>
    </header>

    <div
      class="relative flex min-h-64 flex-col items-center justify-center gap-4 rounded-lg border border-dashed border-primary/50 bg-card/60 px-4 py-8"
    >
      <svg
        class="absolute inset-0 m-auto size-40 text-primary/25"
        viewBox="0 0 160 200"
        fill="none"
        aria-hidden="true"
      >
        <ellipse cx="80" cy="88" rx="48" ry="58" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 4" />
        <path
          d="M40 150c8 22 32 34 40 34s32-12 40-34"
          stroke="currentColor"
          stroke-width="1.5"
          stroke-dasharray="4 4"
        />
      </svg>
      <p class="relative z-10 max-w-[14rem] text-center text-sm leading-6 text-foreground">
        {{ t('visagism.captureHint') }}
      </p>
    </div>

    <ul class="flex flex-col gap-2 text-xs leading-5 text-muted-foreground">
      <li>{{ t('visagism.checkLight') }}</li>
      <li>{{ t('visagism.checkGlasses') }}</li>
      <li>{{ t('visagism.checkExpression') }}</li>
    </ul>

    <p v-if="props.error" class="text-sm text-destructive" role="alert">
      {{ props.errorMessage ?? t('visagism.analyzeError') }}
    </p>

    <div class="mt-auto flex flex-col gap-3">
      <button type="button" :class="buttonClasses('outline', 'lg', 'h-12 w-full gap-2')" @click="openCamera">
        <Camera class="size-4" />
        {{ t('visagism.takePhoto') }}
      </button>
      <button type="button" :class="buttonClasses('outline', 'lg', 'h-12 w-full gap-2')" @click="openGallery">
        <ImagePlus class="size-4" />
        {{ t('visagism.uploadPhoto') }}
      </button>
    </div>

    <p class="pb-2 text-center text-xs leading-5 text-muted-foreground">
      {{ t('visagism.privacy') }}
    </p>

    <input
      ref="cameraInput"
      type="file"
      accept="image/*"
      capture="user"
      class="sr-only"
      @change="onCameraChange"
    />
    <input
      ref="galleryInput"
      type="file"
      accept="image/*"
      class="sr-only"
      @change="onGalleryChange"
    />
  </section>
</template>
