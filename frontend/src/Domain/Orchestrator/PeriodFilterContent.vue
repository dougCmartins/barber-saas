<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import FilterChip from '@/Domain/Orchestrator/FilterChip.vue'
import { periodPresets, type PeriodPreset } from '@/utils/appointmentsFilter'

const props = defineProps<{
  period?: PeriodPreset | undefined
  from: string
  to: string
}>()

const emit = defineEmits<{
  'update:period': [value: PeriodPreset | undefined]
  'update:from': [value: string]
  'update:to': [value: string]
}>()

const { t } = useI18n()

const inputClass =
  'mt-1 h-11 w-full rounded-md border border-border bg-background px-3 text-sm text-foreground [color-scheme:dark] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring'

function togglePeriod(preset: PeriodPreset): void {
  emit('update:period', props.period === preset ? undefined : preset)
}

function onFrom(value: string): void {
  emit('update:from', value)
}

function onTo(value: string): void {
  emit('update:to', value)
}
</script>

<template>
  <div class="flex flex-wrap gap-2">
    <FilterChip
      v-for="preset in periodPresets"
      :key="preset"
      :caret="false"
      :label="t(`clientAppointments.periods.${preset}`)"
      :active="period === preset"
      @click="togglePeriod(preset)"
    />
  </div>
  <p class="mt-5 text-xs font-semibold uppercase tracking-[0.12em] text-muted-foreground">
    {{ t('clientAppointments.customRange') }}
  </p>
  <div class="mt-2 grid grid-cols-2 gap-3">
    <label class="text-xs text-muted-foreground">
      {{ t('clientAppointments.dateFrom') }}
      <input
        type="date"
        :value="from"
        :class="inputClass"
        @input="onFrom(($event.target as HTMLInputElement).value)"
      />
    </label>
    <label class="text-xs text-muted-foreground">
      {{ t('clientAppointments.dateTo') }}
      <input
        type="date"
        :value="to"
        :class="inputClass"
        @input="onTo(($event.target as HTMLInputElement).value)"
      />
    </label>
  </div>
</template>
