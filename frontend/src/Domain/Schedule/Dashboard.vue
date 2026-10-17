<script setup lang="ts">
import { Bell, Bot, CalendarDays, ChevronRight, Clock3, DollarSign, Menu, Scissors, Settings, TrendingUp, Users } from '@lucide/vue'
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useApiErrors } from '@/composables/useApiErrors'
import AppointmentDetails from '@/Domain/Schedule/AppointmentDetails.vue'
import type { ListedAppointment } from '@/Domain/Schedule/scheduleModel'
import { usePrivateScheduleStore } from '@/Domain/Schedule/privateScheduleStore'
import Brand from '@/Domain/Tenant/Brand.vue'
import { AppointmentStatus } from '@/types/appointmentStatus'
import { buttonClasses } from '@/ui/buttonClasses'
import { clock, isoDate } from '@/utils/dateTime'
import { formatBrl } from '@/utils/money'

const { t } = useI18n()
const { readMessage } = useApiErrors()
const schedule = usePrivateScheduleStore()
const failed = ref(false)
const saving = ref(false)
const actionError = ref('')
const selectedAppointment = ref<ListedAppointment | null>(null)
const todayDate = isoDate()

const navItems = [
  { label: t('schedule.agenda'), icon: CalendarDays, active: true },
  { label: t('schedule.finance'), icon: DollarSign, active: false },
  { label: t('schedule.team'), icon: Users, active: false },
  { label: t('schedule.aiSettings'), icon: Bot, active: false },
]

const headerDate = computed(() => {
  const formatted = new Intl.DateTimeFormat('pt-BR', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
  }).format(new Date())

  return formatted.charAt(0).toUpperCase() + formatted.slice(1)
})

const pendingCount = computed(
  () => schedule.appointments.filter((appointment) => appointment.status === AppointmentStatus.Pending).length,
)

const activeCount = computed(
  () => schedule.appointments.filter((appointment) => appointment.status !== AppointmentStatus.Canceled).length,
)

const expectedRevenue = computed(() =>
  schedule.appointments.reduce(
    (sum, appointment) => (appointment.status === AppointmentStatus.Canceled ? sum : sum + appointment.price),
    0,
  ),
)

const occupancy = computed(() => {
  const total = schedule.appointments.length

  if (!total) {
    return '0%'
  }

  return `${Math.round((activeCount.value / total) * 100)}%`
})

const activeTeam = computed(() => {
  const names = schedule.appointments
    .map((appointment) => appointment.professional_name)
    .filter((name) => name)

  return String(new Set(names).size)
})

const revenueLabel = computed(() => formatBrl(expectedRevenue.value))

void load(todayDate)

async function load(date: string): Promise<void> {
  failed.value = false

  try {
    await schedule.fetchAppointments(date)
  } catch {
    failed.value = true
  }
}

function openAppointment(appointment: ListedAppointment): void {
  selectedAppointment.value = appointment
  actionError.value = ''
}

async function completeSelected(): Promise<void> {
  await changeSelected('complete')
}

async function cancelSelected(): Promise<void> {
  await changeSelected('cancel')
}

async function changeSelected(action: 'complete' | 'cancel'): Promise<void> {
  const current = selectedAppointment.value

  if (!current) {
    return
  }

  saving.value = true
  actionError.value = ''

  try {
    if (action === 'complete') {
      await schedule.completeAppointment(current.id)
    } else {
      await schedule.cancelAppointment(current.id)
    }

    selectedAppointment.value = null
  } catch (error) {
    actionError.value = readMessage(error)
  } finally {
    saving.value = false
  }
}

function statusLabel(status: AppointmentStatus): string {
  if (status === AppointmentStatus.Completed) {
    return t('schedule.statusCompleted')
  }

  if (status === AppointmentStatus.Canceled) {
    return t('schedule.statusCanceled')
  }

  return t('schedule.statusPending')
}

function statusClass(status: AppointmentStatus): string {
  if (status === AppointmentStatus.Completed) {
    return 'bg-success/15 text-success'
  }

  if (status === AppointmentStatus.Canceled) {
    return 'bg-destructive/15 text-destructive'
  }

  return 'bg-warning/15 text-warning'
}
</script>

<template>
  <main class="flex min-h-screen bg-background text-foreground">
    <aside class="hidden w-64 shrink-0 border-r border-border bg-sidebar p-5 lg:flex lg:flex-col">
      <Brand :name="t('schedule.brand')" />
      <nav class="mt-10 space-y-1" :aria-label="t('schedule.navLabel')">
        <button
          v-for="item in navItems"
          :key="item.label"
          type="button"
          :class="buttonClasses('ghost', undefined, item.active ? 'w-full justify-start bg-primary/10 text-primary' : 'w-full justify-start text-sidebar-foreground')"
        >
          <component :is="item.icon" class="size-4" />
          {{ item.label }}
        </button>
      </nav>
      <div class="mt-auto border-t border-sidebar-border pt-5">
        <button type="button" :class="buttonClasses('ghost', undefined, 'w-full justify-start text-muted-foreground')">
          <Scissors />
          {{ t('schedule.viewShop') }}
        </button>
        <button type="button" :class="buttonClasses('ghost', undefined, 'mt-1 w-full justify-start text-muted-foreground')">
          <Settings />
          {{ t('schedule.preferences') }}
        </button>
      </div>
    </aside>

    <div class="min-w-0 flex-1">
      <header class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-4 border-b border-border px-4 py-4 md:px-8 lg:px-10">
        <div class="min-w-0 lg:hidden">
          <Brand compact :name="t('schedule.brand')" />
        </div>
        <div class="hidden min-w-0 lg:block">
          <p class="text-sm text-muted-foreground">
            {{ headerDate }}
          </p>
          <h1 class="font-display text-2xl font-semibold uppercase">
            {{ t('schedule.overview') }}
          </h1>
        </div>
        <div class="flex shrink-0 items-center gap-2">
          <button type="button" :class="buttonClasses('ghost', 'icon')" :aria-label="t('schedule.notifications')">
            <Bell />
          </button>
          <button type="button" :class="buttonClasses('ghost', 'icon')" :aria-label="t('schedule.menu')">
            <Menu />
          </button>
          <span class="hidden size-9 place-items-center rounded-full bg-primary font-display font-semibold text-primary-foreground sm:grid" />
        </div>
      </header>

      <div class="flex gap-2 overflow-x-auto border-b border-border px-4 py-3 lg:hidden">
        <button
          v-for="item in navItems"
          :key="item.label"
          type="button"
          :class="buttonClasses('ghost', 'sm', item.active ? 'shrink-0 bg-secondary text-secondary-foreground shadow-sm hover:bg-secondary/80' : 'shrink-0')"
        >
          <component :is="item.icon" />
          {{ item.label }}
        </button>
        <button
          type="button"
          :class="buttonClasses('ghost', 'icon', 'shrink-0')"
          :aria-label="t('schedule.viewShop')"
        >
          <ChevronRight />
        </button>
      </div>

      <div class="px-4 py-7 md:px-8 lg:px-10 lg:py-10">
        <div class="mb-7 grid grid-cols-[minmax(0,1fr)_auto] items-end gap-4">
          <div class="min-w-0">
            <p class="text-sm text-muted-foreground">
              {{ t('schedule.greeting') }}
            </p>
            <h2 class="mt-1 font-display text-4xl font-semibold uppercase">
              {{ t('schedule.todayTitle') }}
            </h2>
          </div>
          <button type="button" :class="buttonClasses('default', 'default', 'hidden sm:inline-flex')">
            <CalendarDays />
            {{ t('schedule.newAppointment') }}
          </button>
        </div>

        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
          <article class="border border-border bg-card p-5">
            <div class="grid grid-cols-[minmax(0,1fr)_auto] gap-4">
              <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-muted-foreground">
                  {{ t('schedule.appointments') }}
                </p>
                <p class="mt-3 font-display text-3xl font-semibold text-foreground">
                  {{ schedule.appointments.length }}
                </p>
              </div>
              <span class="grid size-10 shrink-0 place-items-center rounded-md bg-secondary text-primary">
                <CalendarDays class="size-5" />
              </span>
            </div>
            <p class="mt-3 text-xs text-muted-foreground">
              {{ t('schedule.pendingDetail', { count: pendingCount }) }}
            </p>
          </article>

          <article class="border border-border bg-card p-5">
            <div class="grid grid-cols-[minmax(0,1fr)_auto] gap-4">
              <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-muted-foreground">
                  {{ t('schedule.expectedRevenue') }}
                </p>
                <p class="mt-3 font-display text-3xl font-semibold text-foreground">
                  {{ revenueLabel }}
                </p>
              </div>
              <span class="grid size-10 shrink-0 place-items-center rounded-md bg-secondary text-primary">
                <DollarSign class="size-5" />
              </span>
            </div>
            <p class="mt-3 text-xs text-muted-foreground" />
          </article>

          <article class="border border-border bg-card p-5">
            <div class="grid grid-cols-[minmax(0,1fr)_auto] gap-4">
              <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-muted-foreground">
                  {{ t('schedule.occupancy') }}
                </p>
                <p class="mt-3 font-display text-3xl font-semibold text-foreground">
                  {{ occupancy }}
                </p>
              </div>
              <span class="grid size-10 shrink-0 place-items-center rounded-md bg-secondary text-primary">
                <TrendingUp class="size-5" />
              </span>
            </div>
            <p class="mt-3 text-xs text-muted-foreground">
              {{ t('schedule.occupancyDetail', { active: activeCount, total: schedule.appointments.length }) }}
            </p>
          </article>

          <article class="border border-border bg-card p-5">
            <div class="grid grid-cols-[minmax(0,1fr)_auto] gap-4">
              <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-muted-foreground">
                  {{ t('schedule.activeTeam') }}
                </p>
                <p class="mt-3 font-display text-3xl font-semibold text-foreground">
                  {{ activeTeam }}
                </p>
              </div>
              <span class="grid size-10 shrink-0 place-items-center rounded-md bg-secondary text-primary">
                <Users class="size-5" />
              </span>
            </div>
            <p class="mt-3 text-xs text-muted-foreground">
              {{ t('schedule.teamDetail') }}
            </p>
          </article>
        </section>

        <p v-if="schedule.loading" class="mt-8 text-sm text-muted-foreground">
          {{ t('schedule.loading') }}
        </p>
        <p v-else-if="failed" class="mt-8 text-sm text-red-500">
          {{ t('auth.genericError') }}
        </p>

        <section class="mt-8 border border-border bg-card">
          <div class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-4 border-b border-border p-5">
            <div class="min-w-0">
              <h3 class="font-display text-2xl font-semibold uppercase">
                {{ t('schedule.todayAgenda') }}
              </h3>
              <p class="mt-1 text-sm text-muted-foreground">
                {{ t('schedule.todayAgendaHint') }}
              </p>
            </div>
            <span class="shrink-0 text-xs font-semibold text-primary">
              {{ t('schedule.totalCount', { count: schedule.appointments.length }) }}
            </span>
          </div>

          <p
            v-if="!schedule.loading && !failed && !schedule.appointments.length"
            class="px-5 py-4 text-sm text-muted-foreground"
          >
            {{ t('schedule.empty') }}
          </p>

          <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-left text-sm">
              <thead class="border-b border-border text-xs uppercase tracking-[0.08em] text-muted-foreground">
                <tr>
                  <th class="px-5 py-4 font-medium">
                    {{ t('schedule.columnTime') }}
                  </th>
                  <th class="px-5 py-4 font-medium">
                    {{ t('schedule.client') }}
                  </th>
                  <th class="px-5 py-4 font-medium">
                    {{ t('schedule.service') }}
                  </th>
                  <th class="px-5 py-4 font-medium">
                    {{ t('schedule.barber') }}
                  </th>
                  <th class="px-5 py-4 font-medium">
                    {{ t('schedule.status') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="appointment in schedule.appointments"
                  :key="appointment.id"
                  class="border-b border-border last:border-0"
                  @click="openAppointment(appointment)"
                >
                  <td class="px-5 py-4 font-semibold text-primary">
                    {{ clock(appointment.starts_at) }}
                  </td>
                  <td class="px-5 py-4 font-semibold">
                    {{ appointment.client_name }}
                  </td>
                  <td class="px-5 py-4 text-muted-foreground">
                    {{ appointment.service_name }}
                  </td>
                  <td class="px-5 py-4 text-muted-foreground">
                    {{ appointment.professional_name }}
                  </td>
                  <td class="px-5 py-4">
                    <span :class="['inline-flex rounded-md px-2.5 py-1 text-xs font-semibold', statusClass(appointment.status)]">
                      {{ statusLabel(appointment.status) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="divide-y divide-border md:hidden">
            <article
              v-for="appointment in schedule.appointments"
              :key="appointment.id"
              class="p-4"
              @click="openAppointment(appointment)"
            >
              <div class="grid grid-cols-[minmax(0,1fr)_auto] gap-3">
                <div class="min-w-0">
                  <p class="truncate font-semibold">
                    {{ appointment.client_name }}
                  </p>
                  <p class="mt-1 truncate text-sm text-muted-foreground">
                    {{ appointment.service_name }} · {{ appointment.professional_name }}
                  </p>
                </div>
                <span :class="['inline-flex rounded-md px-2.5 py-1 text-xs font-semibold', statusClass(appointment.status)]">
                  {{ statusLabel(appointment.status) }}
                </span>
              </div>
              <p class="mt-3 flex items-center gap-1.5 text-sm font-semibold text-primary">
                <Clock3 class="size-4" />
                {{ clock(appointment.starts_at) }}
              </p>
            </article>
          </div>
        </section>
      </div>
    </div>

    <AppointmentDetails
      v-if="selectedAppointment"
      :appointment="selectedAppointment"
      :saving="saving"
      :error="actionError"
      @close="selectedAppointment = null"
      @complete="completeSelected"
      @cancel="cancelSelected"
    />
  </main>
</template>
