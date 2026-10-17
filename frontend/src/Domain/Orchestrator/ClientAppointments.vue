<script setup lang="ts">
import { ArrowLeft } from '@lucide/vue'
import { computed, onMounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'
import heroImage from '@/assets/barbershop-hero.jpg'
import { usePublicCatalogStore } from '@/Domain/Catalog/publicCatalogStore'
import AppointmentCard from '@/Domain/Orchestrator/AppointmentCard.vue'
import ClientHubNav from '@/Domain/Orchestrator/ClientHubNav.vue'
import { shopSlug } from '@/Domain/Orchestrator/clientHubModel'
import FilterBottomSheet from '@/Domain/Orchestrator/FilterBottomSheet.vue'
import FilterChip from '@/Domain/Orchestrator/FilterChip.vue'
import PeriodFilterContent from '@/Domain/Orchestrator/PeriodFilterContent.vue'
import type { ClientAppointmentFilters } from '@/Domain/Schedule/clientAppointmentModel'
import { emptyClientAppointmentFilters } from '@/Domain/Schedule/clientAppointmentModel'
import { useClientScheduleStore } from '@/Domain/Schedule/clientScheduleStore'
import {
  clientAppointmentStatuses,
} from '@/types/appointmentStatus'
import type { AppointmentStatus as AppointmentStatusValue } from '@/types/appointmentStatus'
import { buttonClasses } from '@/ui/buttonClasses'
import { periodRange, type PeriodPreset } from '@/utils/appointmentsFilter'
import { clock } from '@/utils/dateTime'

type Sheet = 'period' | 'status' | 'barber' | null

interface DraftFilters {
  period?: PeriodPreset
  from?: string
  to?: string
  statuses: AppointmentStatusValue[]
  professionalIds: number[]
}

const { t } = useI18n()
const today = new Date()
const schedule = useClientScheduleStore()
const catalog = usePublicCatalogStore()
const draft = ref<DraftFilters>({
  statuses: [],
  professionalIds: [],
})
const sheet = ref<Sheet>(null)

const bookingTo = computed(() => ({
  name: 'public-booking' as const,
  params: { slug: shopSlug },
}))

const statusOptions = clientAppointmentStatuses.map((status) => ({
  value: status,
  label: t(`clientAppointments.statuses.${status}`),
}))

const barberOptions = computed(() =>
  catalog.professionals.map((professional) => ({
    value: professional.id,
    label: professional.name,
  })),
)

const list = computed(() =>
  [...schedule.items].sort((left, right) => right.starts_at.localeCompare(left.starts_at)),
)

const periodActive = computed(() =>
  Boolean(schedule.filters.from || schedule.filters.to),
)

const periodLabel = computed(() => {
  if (draft.value.period && sheet.value === 'period') {
    return t(`clientAppointments.periods.${draft.value.period}`)
  }

  if (schedule.filters.from || schedule.filters.to) {
    return t('clientAppointments.periodCustom')
  }

  return t('clientAppointments.period')
})

const statusLabel = computed(() => {
  if (schedule.filters.statuses.length === 1) {
    return t(`clientAppointments.statuses.${schedule.filters.statuses[0]}`)
  }

  if (schedule.filters.statuses.length) {
    return t('clientAppointments.statusCount', { count: schedule.filters.statuses.length })
  }

  return t('clientAppointments.status')
})

const barberLabel = computed(() => {
  if (schedule.filters.professionalId !== null) {
    return (
      catalog.professionals.find((item) => item.id === schedule.filters.professionalId)?.name ??
      t('clientAppointments.barber')
    )
  }

  return t('clientAppointments.barber')
})

onMounted(() => {
  void catalog.fetchPublicCatalog(shopSlug)
  void schedule.fetchAppointments({ ...emptyClientAppointmentFilters })
})

function openSheet(next: Sheet): void {
  draft.value = {
    period: undefined,
    from: schedule.filters.from,
    to: schedule.filters.to,
    statuses: [...schedule.filters.statuses],
    professionalIds:
      schedule.filters.professionalId !== null ? [schedule.filters.professionalId] : [],
  }
  sheet.value = next
}

function toApiFilters(source: DraftFilters): ClientAppointmentFilters {
  let from = source.from
  let to = source.to

  if (source.period) {
    const [start, end] = periodRange(source.period, today)
    from = formatDate(start)
    to = formatDate(end)
  }

  return {
    from,
    to,
    statuses: [...source.statuses],
    professionalId: source.professionalIds[0] ?? null,
  }
}

function formatDate(date: Date): string {
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')

  return `${date.getFullYear()}-${month}-${day}`
}

function applyFilters(): void {
  void schedule.fetchAppointments(toApiFilters(draft.value))
  sheet.value = null
}

function clearFilters(): void {
  const next: DraftFilters =
    sheet.value === 'period'
      ? { ...draft.value, period: undefined, from: undefined, to: undefined }
      : sheet.value === 'status'
        ? { ...draft.value, statuses: [] }
        : { ...draft.value, professionalIds: [] }

  draft.value = next
  void schedule.fetchAppointments(toApiFilters(next))
  sheet.value = null
}

function closeSheet(): void {
  sheet.value = null
}

function toggleStatus(status: AppointmentStatusValue): void {
  draft.value = {
    ...draft.value,
    statuses: draft.value.statuses.includes(status)
      ? draft.value.statuses.filter((item) => item !== status)
      : [...draft.value.statuses, status],
  }
}

function toggleBarber(professionalId: number): void {
  draft.value = {
    ...draft.value,
    professionalIds: draft.value.professionalIds.includes(professionalId)
      ? []
      : [professionalId],
  }
}

function setPeriod(period: PeriodPreset | undefined): void {
  draft.value = { ...draft.value, period, from: undefined, to: undefined }
}

function setFrom(from: string): void {
  draft.value = {
    ...draft.value,
    period: undefined,
    from: from || undefined,
  }
}

function setTo(to: string): void {
  draft.value = {
    ...draft.value,
    period: undefined,
    to: to || undefined,
  }
}

function cancelAppointment(id: number): void {
  void schedule.cancelAppointment(id)
}

function dateLabel(startsAt: string): string {
  const [datePart = ''] = startsAt.split(' ')
  const [year = 0, month = 1, day = 1] = datePart.split('-').map(Number)
  const date = new Date(year, month - 1, day)

  return date.toLocaleDateString('pt-BR', {
    weekday: 'short',
    day: '2-digit',
    month: 'short',
  })
}

function avatarFor(url: string | null): string {
  return url || heroImage
}
</script>

<template>
  <main class="min-h-screen bg-background pb-28 text-foreground">
    <header class="sticky top-0 z-30 border-b border-border bg-background/95 backdrop-blur">
      <div class="mx-auto flex max-w-lg items-center gap-2 px-4 pt-4">
        <RouterLink
          to="/"
          :aria-label="t('clientAppointments.back')"
          :class="buttonClasses('ghost', 'icon')"
        >
          <ArrowLeft />
        </RouterLink>
        <h1 class="font-display text-3xl font-bold uppercase">
          {{ t('clientAppointments.title') }}
        </h1>
      </div>
      <div class="mx-auto flex max-w-lg gap-2 overflow-x-auto px-4 py-3">
        <FilterChip
          :label="periodLabel"
          :active="periodActive"
          @click="openSheet('period')"
        />
        <FilterChip
          :label="statusLabel"
          :active="schedule.filters.statuses.length > 0"
          @click="openSheet('status')"
        />
        <FilterChip
          :label="barberLabel"
          :active="schedule.filters.professionalId !== null"
          @click="openSheet('barber')"
        />
      </div>
    </header>

    <div class="mx-auto flex max-w-lg flex-col gap-4 px-4 py-5">
      <p v-if="schedule.loading" class="text-sm text-muted-foreground">
        {{ t('tenant.loading') }}
      </p>
      <div
        v-else-if="!list.length"
        class="rounded-lg border border-dashed border-border bg-card p-6 text-center"
      >
        <p class="font-display text-lg font-semibold">
          {{ t('clientAppointments.emptyTitle') }}
        </p>
        <p class="mt-1 text-sm text-muted-foreground">
          {{ t('clientAppointments.emptyBody') }}
        </p>
      </div>
      <AppointmentCard
        v-for="appointment in list"
        :key="appointment.id"
        :barber-name="appointment.professional_name"
        :avatar-url="avatarFor(appointment.professional_avatar_url)"
        :date-label="dateLabel(appointment.starts_at)"
        :time="clock(appointment.starts_at)"
        :services="appointment.service_names"
        :total="appointment.price"
        :status="appointment.status"
        @cancel="cancelAppointment(appointment.id)"
      />
    </div>

    <FilterBottomSheet
      :title="t('clientAppointments.filterPeriodTitle')"
      :open="sheet === 'period'"
      @close="closeSheet"
      @apply="applyFilters"
      @clear="clearFilters"
    >
      <PeriodFilterContent
        :period="draft.period"
        :from="draft.from ?? ''"
        :to="draft.to ?? ''"
        @update:period="setPeriod"
        @update:from="setFrom"
        @update:to="setTo"
      />
    </FilterBottomSheet>

    <FilterBottomSheet
      :title="t('clientAppointments.filterStatusTitle')"
      :open="sheet === 'status'"
      @close="closeSheet"
      @apply="applyFilters"
      @clear="clearFilters"
    >
      <div class="flex flex-wrap gap-2">
        <FilterChip
          v-for="option in statusOptions"
          :key="option.value"
          :caret="false"
          :label="option.label"
          :active="draft.statuses.includes(option.value)"
          @click="toggleStatus(option.value)"
        />
      </div>
    </FilterBottomSheet>

    <FilterBottomSheet
      :title="t('clientAppointments.filterBarberTitle')"
      :open="sheet === 'barber'"
      @close="closeSheet"
      @apply="applyFilters"
      @clear="clearFilters"
    >
      <div class="flex flex-wrap gap-2">
        <FilterChip
          v-for="option in barberOptions"
          :key="option.value"
          :caret="false"
          :label="option.label"
          :active="draft.professionalIds.includes(option.value)"
          @click="toggleBarber(option.value)"
        />
      </div>
    </FilterBottomSheet>

    <ClientHubNav active="account" home-to="/" :booking-to="bookingTo" />
  </main>
</template>
