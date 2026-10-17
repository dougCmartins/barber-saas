<script setup lang="ts">
import { CalendarDays, Clock3 } from '@lucide/vue'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import AppointmentStatusTag from '@/Domain/Orchestrator/AppointmentStatusTag.vue'
import { AppointmentStatus } from '@/types/appointmentStatus'
import type { AppointmentStatus as AppointmentStatusValue } from '@/types/appointmentStatus'
import { buttonClasses } from '@/ui/buttonClasses'

const props = defineProps<{
  barberName: string
  avatarUrl: string
  dateLabel: string
  time: string
  services: string[]
  total: number
  status: AppointmentStatusValue
}>()

const emit = defineEmits<{
  cancel: []
}>()

const { t } = useI18n()

const canCancel = computed(
  () =>
    props.status === AppointmentStatus.Confirmed || props.status === AppointmentStatus.Pending,
)
</script>

<template>
  <article class="rounded-lg border border-border bg-card p-4">
    <div class="flex items-center gap-3">
      <img
        :src="avatarUrl"
        :alt="barberName"
        loading="lazy"
        class="size-11 shrink-0 rounded-full object-cover object-center ring-2 ring-primary/40"
      />
      <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-semibold text-foreground">{{ barberName }}</p>
        <p class="mt-0.5 flex flex-wrap items-center gap-x-3 text-xs text-muted-foreground">
          <span class="flex items-center gap-1">
            <CalendarDays class="size-3" />{{ dateLabel }}
          </span>
          <span class="flex items-center gap-1">
            <Clock3 class="size-3" />{{ time }}
          </span>
        </p>
      </div>
      <AppointmentStatusTag :status="status" />
    </div>
    <div class="mt-4 flex items-end justify-between gap-4 border-t border-border pt-3">
      <div class="min-w-0">
        <p class="text-xs leading-5 text-muted-foreground">{{ services.join(' + ') }}</p>
        <p class="mt-1 font-display text-xl font-semibold text-foreground">
          {{ t('clientAppointments.price', { price: total }) }}
        </p>
      </div>
      <button
        v-if="canCancel"
        type="button"
        :class="buttonClasses('outline', 'sm')"
        @click="emit('cancel')"
      >
        {{ t('clientAppointments.cancel') }}
      </button>
    </div>
  </article>
</template>
