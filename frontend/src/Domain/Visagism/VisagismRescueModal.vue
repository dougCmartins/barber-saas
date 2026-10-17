<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import type { ConsultationType } from '@/Domain/Visagism/visagismModel'
import { buttonClasses } from '@/ui/buttonClasses'

const props = defineProps<{
  open: boolean
  consultationType: ConsultationType | null
  processing?: boolean
}>()

const emit = defineEmits<{
  close: []
  restore: []
  restart: []
}>()

const { t } = useI18n()

const typeLabel = computed(() => {
  if (props.consultationType === 'manual') {
    return t('visagism.rescueTypeManual')
  }

  return t('visagism.rescueTypeAi')
})
</script>

<template>
  <Teleport to="body">
    <div
      v-if="open"
      class="fixed inset-0 z-50 flex items-end justify-center bg-background/70 backdrop-blur-sm"
      role="dialog"
      aria-modal="true"
      :aria-label="t('visagism.rescueTitle')"
      @click.self="emit('close')"
    >
      <div class="mx-auto w-full max-w-lg rounded-t-2xl border-t border-border bg-card px-4 pb-8 pt-5 shadow-lg">
        <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-border" />
        <h2 class="font-display text-2xl font-semibold uppercase tracking-wide text-foreground">
          {{ t('visagism.rescueTitle') }}
        </h2>
        <p class="mt-2 text-sm leading-6 text-muted-foreground">
          {{ t('visagism.rescueBody', { type: typeLabel }) }}
        </p>
        <div class="mt-6 flex flex-col gap-3">
          <button
            type="button"
            :class="buttonClasses('default', 'lg', 'h-12 w-full')"
            :disabled="processing"
            @click="emit('restore')"
          >
            {{ t('visagism.rescueRestore') }}
          </button>
          <button
            type="button"
            :class="buttonClasses('destructive', 'lg', 'h-12 w-full')"
            :disabled="processing"
            @click="emit('restart')"
          >
            {{ t('visagism.rescueRestart') }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>
