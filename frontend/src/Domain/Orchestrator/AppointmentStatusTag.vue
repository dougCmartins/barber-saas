<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { AppointmentStatus } from '@/types/appointmentStatus'
import type { AppointmentStatus as AppointmentStatusValue } from '@/types/appointmentStatus'
import { cn } from '@/ui/cn'

const props = defineProps<{
  status: AppointmentStatusValue
}>()

const { t } = useI18n()

const statusStyles: Record<AppointmentStatusValue, string> = {
  [AppointmentStatus.Confirmed]: 'bg-primary/15 text-primary',
  [AppointmentStatus.Pending]: 'bg-primary/15 text-primary',
  [AppointmentStatus.Completed]: 'bg-success/15 text-success',
  [AppointmentStatus.Missed]: 'bg-warning/15 text-warning',
  [AppointmentStatus.Canceled]: 'bg-destructive/15 text-destructive',
}

const label = computed(() => t(`clientAppointments.statuses.${props.status}`))
</script>

<template>
  <span
    :class="cn('inline-flex rounded-full px-2.5 py-1 text-xs font-semibold', statusStyles[status])"
  >
    {{ label }}
  </span>
</template>
