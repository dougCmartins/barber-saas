<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import type { ListedAppointment } from '@/Domain/Schedule/scheduleModel'
import { AppointmentStatus } from '@/types/appointmentStatus'
import Button from '@/ui/Button.vue'
import { clock } from '@/utils/dateTime'
import { formatBrl } from '@/utils/money'

const props = defineProps<{
  appointment: ListedAppointment
  saving: boolean
  error: string
}>()

const emit = defineEmits<{
  close: []
  complete: []
  cancel: []
}>()

const { t } = useI18n()

const canChange = computed(
  () =>
    props.appointment.status === AppointmentStatus.Pending ||
    props.appointment.status === AppointmentStatus.Confirmed,
)

const when = computed(() =>
  t('schedule.timeRange', {
    start: clock(props.appointment.starts_at),
    end: clock(props.appointment.ends_at),
  }),
)

const price = computed(() => formatBrl(props.appointment.price))
</script>

<template>
  <div
    class="fixed inset-0 z-50 grid place-items-center bg-background/80 px-5 py-16"
    role="dialog"
    aria-modal="true"
    :aria-label="t('schedule.details')"
    @click.self="emit('close')"
  >
    <section class="w-full max-w-md rounded-md border border-border bg-card p-8 shadow">
      <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary">
        {{ t('schedule.kicker') }}
      </p>
      <h2 class="mt-2 font-display text-4xl font-semibold uppercase">
        {{ t('schedule.details') }}
      </h2>

      <dl class="mt-6 space-y-3 text-sm">
        <div>
          <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-muted-foreground">
            {{ t('schedule.client') }}
          </dt>
          <dd class="mt-1">{{ appointment.client_name }}</dd>
        </div>
        <div>
          <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-muted-foreground">
            {{ t('schedule.service') }}
          </dt>
          <dd class="mt-1">{{ appointment.service_name }}</dd>
        </div>
        <div>
          <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-muted-foreground">
            {{ t('schedule.professional') }}
          </dt>
          <dd class="mt-1">{{ appointment.professional_name }}</dd>
        </div>
        <div>
          <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-muted-foreground">
            {{ t('schedule.date') }}
          </dt>
          <dd class="mt-1">{{ when }}</dd>
        </div>
        <div>
          <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-muted-foreground">
            {{ t('schedule.price') }}
          </dt>
          <dd class="mt-1">{{ price }}</dd>
        </div>
      </dl>

      <p v-if="error" class="mt-6 text-sm text-red-500">
        {{ error }}
      </p>

      <div v-if="canChange" class="mt-6 grid gap-3">
        <button
          type="button"
          class="inline-flex h-10 items-center justify-center rounded-md bg-green-700 text-sm font-medium text-white disabled:opacity-50"
          :disabled="saving"
          data-testid="complete-appointment"
          @click="emit('complete')"
        >
          {{ t('schedule.complete') }}
        </button>
        <button
          type="button"
          class="inline-flex h-10 items-center justify-center rounded-md border border-red-500 text-sm font-medium text-red-500 disabled:opacity-50"
          :disabled="saving"
          data-testid="cancel-appointment"
          @click="emit('cancel')"
        >
          {{ t('schedule.cancel') }}
        </button>
      </div>

      <Button class="mt-4 w-full" variant="ghost" type="button" @click="emit('close')">
        {{ t('schedule.close') }}
      </Button>
    </section>
  </div>
</template>
