<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { buttonClasses } from '@/ui/buttonClasses'

defineProps<{
  title: string
  open: boolean
}>()

const emit = defineEmits<{
  close: []
  apply: []
  clear: []
}>()

const { t } = useI18n()
</script>

<template>
  <div
    v-if="open"
    class="fixed inset-0 z-50"
    role="dialog"
    aria-modal="true"
    :aria-label="title"
  >
    <button
      type="button"
      :aria-label="t('clientAppointments.close')"
      class="absolute inset-0 h-full w-full bg-background/70 backdrop-blur-sm"
      @click="emit('close')"
    />
    <div
      class="absolute inset-x-0 bottom-0 mx-auto w-full max-w-lg rounded-t-2xl border-t border-border bg-card px-4 pb-8 pt-3 shadow-2xl"
    >
      <span class="mx-auto block h-1 w-10 rounded-full bg-muted-foreground/40" />
      <h2 class="mt-4 font-display text-2xl font-semibold uppercase text-foreground">
        {{ title }}
      </h2>
      <div class="mt-4">
        <slot />
      </div>
      <div class="mt-6 grid grid-cols-2 gap-3">
        <button type="button" :class="buttonClasses('outline', 'lg')" @click="emit('clear')">
          {{ t('clientAppointments.clear') }}
        </button>
        <button type="button" :class="buttonClasses('default', 'lg')" @click="emit('apply')">
          {{ t('clientAppointments.apply') }}
        </button>
      </div>
    </div>
  </div>
</template>
