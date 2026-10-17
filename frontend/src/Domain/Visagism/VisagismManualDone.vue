<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import type { ManualConsultation } from '@/Domain/Visagism/visagismModel'
import { buttonClasses } from '@/ui/buttonClasses'

defineProps<{
  manual: ManualConsultation
}>()

const emit = defineEmits<{
  booking: []
  back: []
}>()

const { t } = useI18n()
</script>

<template>
  <section class="flex flex-1 flex-col gap-4 pt-2">
    <header>
      <p class="text-xs font-medium uppercase tracking-[0.14em] text-primary">
        {{ t('visagism.manualDoneKicker') }}
      </p>
      <h1 class="mt-1 font-display text-2xl font-semibold uppercase tracking-wide text-foreground">
        {{ t('visagism.manualDoneTitle') }}
      </h1>
    </header>

    <article class="rounded-lg border border-border bg-card p-4">
      <p class="text-xs uppercase tracking-wide text-muted-foreground">{{ t('visagism.manualDesiredLabel') }}</p>
      <p class="mt-1 text-sm leading-6 text-foreground">{{ manual.desired_notes }}</p>
      <p class="mt-4 text-xs uppercase tracking-wide text-muted-foreground">
        {{ t('visagism.manualDislikedLabel') }}
      </p>
      <p class="mt-1 text-sm leading-6 text-foreground">{{ manual.disliked_notes }}</p>
      <p class="mt-4 text-xs text-muted-foreground">
        {{ t('visagism.manualPhotosCount', { count: manual.reference_photos.length }) }}
      </p>
    </article>

    <button type="button" :class="buttonClasses('default', 'lg', 'mt-auto h-12 w-full')" @click="emit('booking')">
      {{ t('visagism.manualGoBooking') }}
    </button>
    <button type="button" :class="buttonClasses('ghost', 'default', 'w-full')" @click="emit('back')">
      {{ t('visagism.back') }}
    </button>
  </section>
</template>
