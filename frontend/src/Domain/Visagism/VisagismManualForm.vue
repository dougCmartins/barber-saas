<script setup lang="ts">
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import type { SaveManualPayload } from '@/Domain/Visagism/visagismModel'
import { buttonClasses } from '@/ui/buttonClasses'

defineProps<{
  processing?: boolean
  error?: boolean
}>()

const emit = defineEmits<{
  submit: [payload: SaveManualPayload]
}>()

const { t } = useI18n()
const photos = ref<string[]>([])
const desiredNotes = ref('')
const dislikedNotes = ref('')
const localError = ref(false)

async function onFiles(event: Event): Promise<void> {
  const input = event.target as HTMLInputElement
  const files = Array.from(input.files ?? []).slice(0, 2)
  const next: string[] = []

  for (const file of files) {
    next.push(await readAsDataUrl(file))
  }

  photos.value = next
  input.value = ''
}

function readAsDataUrl(file: File): Promise<string> {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.onload = () => resolve(String(reader.result ?? ''))
    reader.onerror = () => reject(reader.error)
    reader.readAsDataURL(file)
  })
}

function submit(): void {
  localError.value = false

  if (!photos.value.length || !desiredNotes.value.trim() || !dislikedNotes.value.trim()) {
    localError.value = true
    return
  }

  emit('submit', {
    reference_photos: photos.value,
    desired_notes: desiredNotes.value.trim(),
    disliked_notes: dislikedNotes.value.trim(),
  })
}
</script>

<template>
  <section class="flex flex-1 flex-col gap-4">
    <header class="pt-2">
      <p class="text-xs font-medium uppercase tracking-[0.14em] text-primary">
        {{ t('visagism.manualKicker') }}
      </p>
      <h1 class="mt-1 font-display text-2xl font-semibold uppercase tracking-wide text-foreground">
        {{ t('visagism.manualTitle') }}
      </h1>
      <p class="mt-2 text-sm leading-6 text-muted-foreground">{{ t('visagism.manualSubtitle') }}</p>
    </header>

    <label class="flex flex-col gap-2">
      <span class="text-xs uppercase tracking-wide text-muted-foreground">
        {{ t('visagism.manualPhotosLabel') }}
      </span>
      <input
        type="file"
        accept="image/*"
        multiple
        class="block w-full text-sm text-muted-foreground file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-2 file:text-sm file:font-medium file:text-primary-foreground"
        @change="onFiles"
      />
      <span v-if="photos.length" class="text-xs text-foreground">
        {{ t('visagism.manualPhotosCount', { count: photos.length }) }}
      </span>
    </label>

    <label class="flex flex-col gap-2">
      <span class="text-xs uppercase tracking-wide text-muted-foreground">
        {{ t('visagism.manualDesiredLabel') }}
      </span>
      <textarea
        v-model="desiredNotes"
        rows="3"
        class="rounded-lg border border-border bg-card px-3 py-2 text-sm text-foreground outline-none focus:border-primary"
        :placeholder="t('visagism.manualDesiredPlaceholder')"
      />
    </label>

    <label class="flex flex-col gap-2">
      <span class="text-xs uppercase tracking-wide text-muted-foreground">
        {{ t('visagism.manualDislikedLabel') }}
      </span>
      <textarea
        v-model="dislikedNotes"
        rows="3"
        class="rounded-lg border border-border bg-card px-3 py-2 text-sm text-foreground outline-none focus:border-primary"
        :placeholder="t('visagism.manualDislikedPlaceholder')"
      />
    </label>

    <p v-if="localError || error" class="text-sm text-destructive" role="alert">
      {{ t('visagism.manualFormError') }}
    </p>

    <button
      type="button"
      :class="buttonClasses('default', 'lg', 'mt-auto h-12 w-full')"
      :disabled="processing"
      @click="submit"
    >
      {{ t('visagism.manualSubmit') }}
    </button>
  </section>
</template>
