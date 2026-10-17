<script setup lang="ts">
import { ArrowLeft, CalendarDays, Check, ChevronLeft, ChevronRight, Clock3, Minus, Plus } from '@lucide/vue'
import { computed, ref, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import heroImage from '@/assets/barbershop-hero.jpg'
import { usePublicCatalogStore } from '@/Domain/Catalog/publicCatalogStore'
import type { Professional } from '@/Domain/Catalog/professionalModel'
import type { Service } from '@/Domain/Catalog/serviceModel'
import type { AvailableSlot } from '@/Domain/Orchestrator/bookingModel'
import { usePublicBookingStore } from '@/Domain/Orchestrator/publicBookingStore'
import Brand from '@/Domain/Tenant/Brand.vue'
import { usePublicTenantStore } from '@/Domain/Tenant/publicTenantStore'
import BottomNav from '@/ui/BottomNav.vue'
import { buttonClasses } from '@/ui/buttonClasses'
import { cn } from '@/ui/cn'
import { splitDuration } from '@/utils/duration'

interface DayOption {
  value: string
  weekday: string
  day: string
  label: string
  closed: boolean
  past: boolean
  disabled: boolean
}

const route = useRoute()
const { t, tm } = useI18n()
const catalog = usePublicCatalogStore()
const tenant = usePublicTenantStore()
const booking = usePublicBookingStore()
const today = startOfToday()
const currentYear = today.getFullYear()
const openingDate = initialBookableDate(today)
const month = ref(openingDate.getMonth())
const selectedDate = ref(dateValue(openingDate))
const selectedProfessionalId = ref<number | null>(null)
const selectedServiceIds = ref<number[]>([])
const selectedStartsAt = ref('')
const booked = ref(false)
const failed = ref(false)
const seeded = ref(false)

const slug = computed(() => {
  const value = route.params.slug

  return typeof value === 'string' ? value : ''
})

const serviceId = computed(() => {
  const value = route.query.service
  const raw = Array.isArray(value) ? value[0] : value
  const id = Number(raw)

  return Number.isInteger(id) && id > 0 ? id : null
})

const productId = computed(() => {
  const value = route.query.product
  const raw = Array.isArray(value) ? value[0] : value
  const id = Number(raw)

  return Number.isInteger(id) && id > 0 ? id : null
})

const serviceIdsFromQuery = computed(() => {
  const value = route.query.services
  const raw = Array.isArray(value) ? value[0] : value

  if (typeof raw !== 'string' || !raw.trim()) {
    return [] as number[]
  }

  return raw
    .split(',')
    .map((part) => Number(part.trim()))
    .filter((id) => Number.isInteger(id) && id > 0)
})

const selectedProfessional = computed(
  () => catalog.professionals.find((item) => item.id === selectedProfessionalId.value) ?? null,
)

const selectedServices = computed(() =>
  catalog.services.filter((item) => selectedServiceIds.value.includes(item.id)),
)

const totalDuration = computed(() =>
  selectedServices.value.reduce((total, item) => total + item.duration_in_minutes, 0),
)

const totalPrice = computed(() => selectedServices.value.reduce((total, item) => total + item.price, 0))

const shopName = computed(() => tenant.barbershop?.name || t('schedule.brand'))

const homeTo = computed(() =>
  slug.value ? { name: 'public-home', params: { slug: slug.value } } : { name: 'public-home-missing' },
)

const days = computed(() => buildDays(month.value))

const selectedDay = computed(() => days.value.find((day) => day.value === selectedDate.value) ?? null)

const monthLabel = computed(() => `${messageList('booking.months')[month.value] ?? ''} ${currentYear}`)

const selectedTime = computed(() => (selectedStartsAt.value ? selectedStartsAt.value.slice(11, 16) : ''))

const summaryTitle = computed(() => {
  if (!selectedServices.value.length) {
    return t('booking.noneSelected')
  }

  return t('booking.summary', {
    service: selectedServices.value.map((item) => item.name).join(' + '),
    professional: selectedProfessional.value?.name ?? '',
  })
})

const slotState = computed(() => {
  if (booking.slotsLoading) {
    return 'loading'
  }

  if (booking.slotsError) {
    return 'error'
  }

  if (!booking.availableSlots.length) {
    return 'empty'
  }

  return 'ready'
})

const canConfirm = computed(
  () =>
    Boolean(selectedProfessional.value && selectedServices.value.length && selectedStartsAt.value) &&
    !booking.isLoading,
)

watch(
  slug,
  (value) => {
    if (!value) {
      return
    }

    void catalog.fetchPublicCatalog(value)
    void tenant.fetchBarbershop(value)
  },
  { immediate: true },
)

watch(
  () => catalog.professionals.map((item) => item.id).join(','),
  () => {
    const stillThere = catalog.professionals.some((item) => item.id === selectedProfessionalId.value)

    if (!stillThere) {
      selectedProfessionalId.value = catalog.professionals[0]?.id ?? null
    }
  },
  { immediate: true },
)

watch(
  () => catalog.services.map((item) => item.id).join(','),
  () => {
    const known = catalog.services.map((item) => item.id)

    if (!seeded.value) {
      if (!known.length) {
        return
      }

      const fromServices = serviceIdsFromQuery.value.filter((id) => known.includes(id))
      const initial = serviceId.value
      const product = productId.value
      const fromVisagism = [initial, product].filter(
        (id): id is number => id !== null && known.includes(id),
      )

      if (fromServices.length) {
        selectedServiceIds.value = fromServices
      } else if (fromVisagism.length) {
        selectedServiceIds.value = [...new Set(fromVisagism)]
      } else {
        selectedServiceIds.value = []
      }

      seeded.value = true
      return
    }

    selectedServiceIds.value = selectedServiceIds.value.filter((id) => known.includes(id))
  },
  { immediate: true },
)

watch([selectedProfessionalId, selectedDate, () => selectedServiceIds.value.join(',')], () => {
  booked.value = false
  failed.value = false
  void loadSlots()
}, { immediate: true })

watch(
  () => [booking.slotsLoading, booking.availableSlots.map((slot) => slot.starts_at).join('|'), selectedDate.value],
  () => {
    if (booking.slotsLoading || !selectedStartsAt.value) {
      return
    }

    const stillThere = booking.availableSlots.some(
      (slot) => `${selectedDate.value} ${slot.starts_at.slice(11, 16)}:00` === selectedStartsAt.value,
    )

    if (!stillThere) {
      selectedStartsAt.value = ''
    }
  },
)

async function loadSlots(): Promise<void> {
  const professionalId = selectedProfessionalId.value
  const serviceIds = selectedServices.value.map((item) => item.id)
  const day = days.value.find((item) => item.value === selectedDate.value)

  if (!professionalId || !serviceIds.length || !slug.value || !day || day.disabled) {
    booking.availableSlots = []
    booking.slotsLoading = false
    booking.slotsError = false
    return
  }

  try {
    await booking.fetchAvailableSlots(slug.value, professionalId, selectedDate.value, serviceIds)
  } catch {
    return
  }
}

function toggleService(service: Service): void {
  if (selectedServiceIds.value.includes(service.id)) {
    selectedServiceIds.value = selectedServiceIds.value.filter((id) => id !== service.id)
    return
  }

  selectedServiceIds.value = [...selectedServiceIds.value, service.id]
}

function changeMonth(nextMonth: number): void {
  if (nextMonth < 0 || nextMonth > 11) {
    return
  }

  const nextDays = buildDays(nextMonth)
  const first =
    nextDays.find((item) => !item.disabled) ?? nextDays.find((item) => !item.closed) ?? nextDays[0]

  month.value = nextMonth

  if (first) {
    selectedDate.value = first.value
  }
}

function portrait(professional: Professional): string {
  return professional.avatar_url || heroImage
}

function slotTime(slot: AvailableSlot): string {
  return slot.starts_at.slice(11, 16)
}

function chooseSlot(slot: AvailableSlot): void {
  selectedStartsAt.value = `${selectedDate.value} ${slotTime(slot)}:00`
  failed.value = false
}

function isSelected(slot: AvailableSlot): boolean {
  return selectedStartsAt.value === `${selectedDate.value} ${slotTime(slot)}:00`
}

function isServiceSelected(service: Service): boolean {
  return selectedServiceIds.value.includes(service.id)
}

async function confirm(): Promise<void> {
  const professional = selectedProfessional.value
  const serviceIds = selectedServices.value.map((item) => item.id)

  if (!professional || !serviceIds.length || !selectedStartsAt.value || booking.isLoading) {
    return
  }

  failed.value = false
  booked.value = false

  try {
    const created = await booking.book(slug.value, {
      professional_id: professional.id,
      service_ids: serviceIds,
      starts_at: selectedStartsAt.value,
    })

    if (!created) {
      failed.value = true
      return
    }

    booked.value = true
  } catch {
    failed.value = true
  }
}

function formatDuration(totalMinutes: number): string {
  const { hours, minutes } = splitDuration(totalMinutes)

  if (!hours) {
    return t('catalog.durationMinutes', { count: minutes })
  }

  if (!minutes) {
    return t('catalog.durationHours', { hours })
  }

  return t('catalog.durationHoursMinutes', { hours, minutes })
}

function buildDays(monthIndex: number): DayOption[] {
  const total = new Date(currentYear, monthIndex + 1, 0).getDate()
  const weekdays = messageList('booking.weekdays')
  const monthName = (messageList('booking.months')[monthIndex] ?? '').toLowerCase()

  return Array.from({ length: total }, (_, index) => {
    const day = index + 1
    const date = new Date(currentYear, monthIndex, day)
    date.setHours(0, 0, 0, 0)
    const closed = date.getDay() === 0
    const past = date < today

    return {
      value: dateValue(date),
      weekday: weekdays[date.getDay()] ?? '',
      day: String(day).padStart(2, '0'),
      label: `${day} de ${monthName} de ${currentYear}`,
      closed,
      past,
      disabled: closed || past,
    }
  })
}

function messageList(key: string): string[] {
  const value = tm(key)

  return Array.isArray(value) ? value.map((item) => String(item)) : []
}

function startOfToday(): Date {
  const date = new Date()
  date.setHours(0, 0, 0, 0)

  return date
}

function initialBookableDate(current: Date): Date {
  if (current.getDay() !== 0) {
    return current
  }

  const next = new Date(current)
  next.setDate(current.getDate() + 1)

  if (next.getFullYear() !== current.getFullYear()) {
    return current
  }

  return next
}

function dateValue(date: Date): string {
  const monthNumber = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')

  return `${date.getFullYear()}-${monthNumber}-${day}`
}
</script>

<template>
  <main class="min-h-screen bg-background pb-40 text-foreground md:pb-12">
    <header class="border-b border-border">
      <div class="mx-auto grid max-w-5xl grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-3 px-4 py-4 md:px-8">
        <RouterLink :to="homeTo" :aria-label="t('booking.back')" :class="buttonClasses('ghost', 'icon')">
          <ArrowLeft />
        </RouterLink>
        <Brand compact class="justify-center" :name="shopName" />
        <span class="w-9" />
      </div>
    </header>

    <div class="mx-auto max-w-5xl px-4 py-7 md:px-8 md:py-10">
      <div class="mb-9">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary">
          {{ t('booking.step') }}
        </p>
        <h1 class="mt-2 font-display text-4xl font-semibold uppercase md:text-5xl">
          {{ t('booking.pageTitle') }}
        </h1>
        <div class="mt-5 grid grid-cols-4 gap-2">
          <span class="h-1 bg-primary" />
          <span class="h-1 bg-primary" />
          <span class="h-1 bg-primary" />
          <span class="h-1 bg-primary" />
        </div>
      </div>

      <p v-if="catalog.loading" class="text-sm text-muted-foreground">
        {{ t('tenant.loading') }}
      </p>

      <template v-else>
        <section>
          <h2 class="font-display text-2xl font-semibold">
            {{ t('booking.procedure') }}
          </h2>
          <p class="mt-1 text-sm text-muted-foreground">
            {{ t('booking.procedureHint') }}
          </p>
          <div class="mt-4 grid gap-x-8 md:grid-cols-2">
            <article
              v-for="item in catalog.services"
              :key="item.id"
              class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-4 border-b border-border py-4"
            >
              <div class="min-w-0">
                <p class="font-display text-xl font-semibold">{{ item.name }}</p>
                <p class="mt-1 text-xs text-muted-foreground">
                  {{ formatDuration(item.duration_in_minutes) }} · {{ t('catalog.price', { price: item.price }) }}
                </p>
              </div>
              <button
                type="button"
                :class="buttonClasses(isServiceSelected(item) ? 'default' : 'outline', 'sm')"
                :aria-pressed="isServiceSelected(item)"
                :aria-label="t('booking.toggleService', { action: isServiceSelected(item) ? t('booking.remove') : t('booking.add'), name: item.name })"
                @click="toggleService(item)"
              >
                <Minus v-if="isServiceSelected(item)" />
                <Plus v-else />
                {{ isServiceSelected(item) ? t('booking.remove') : t('booking.add') }}
              </button>
            </article>
          </div>
        </section>

        <section class="mt-9 border-t border-border pt-8">
          <h2 class="font-display text-2xl font-semibold">
            {{ t('booking.withWhom') }}
          </h2>
          <p v-if="!catalog.professionals.length" class="mt-4 text-sm text-red-500">
            {{ t('booking.noProfessional') }}
          </p>
          <div v-else class="mt-4 flex gap-3 overflow-x-auto pb-2" role="radiogroup" :aria-label="t('booking.professional')">
            <button
              v-for="item in catalog.professionals"
              :key="item.id"
              type="button"
              :class="buttonClasses('ghost', undefined, cn('h-auto w-32 shrink-0 flex-col gap-3 whitespace-normal border bg-card p-3 text-left hover:bg-secondary', item.id === selectedProfessionalId ? 'border-primary ring-1 ring-primary' : 'border-border'))"
              :aria-pressed="item.id === selectedProfessionalId"
              @click="selectedProfessionalId = item.id"
            >
              <span class="relative block aspect-square w-full overflow-hidden rounded-md bg-secondary">
                <img :src="portrait(item)" :alt="item.name" loading="lazy" class="h-full w-full object-cover" />
                <span
                  v-if="item.id === selectedProfessionalId"
                  class="absolute inset-0 border-2 border-primary"
                  aria-hidden="true"
                />
              </span>
              <span class="block w-full">
                <span class="block text-sm font-semibold text-foreground">{{ item.name }}</span>
                <span class="mt-1 block text-xs text-muted-foreground">{{ item.specialty }}</span>
              </span>
            </button>
          </div>
        </section>

        <section class="mt-9 border-t border-border pt-8">
          <div class="flex items-center justify-between gap-3">
            <h2 class="font-display text-2xl font-semibold">
              {{ t('booking.whichDay') }}
            </h2>
            <div class="flex items-center gap-1">
              <button
                type="button"
                :class="buttonClasses('ghost', 'icon')"
                :disabled="month === 0"
                :aria-label="t('booking.previousMonth')"
                @click="changeMonth(month - 1)"
              >
                <ChevronLeft />
              </button>
              <p class="min-w-32 text-center text-sm font-semibold capitalize">
                {{ monthLabel }}
              </p>
              <button
                type="button"
                :class="buttonClasses('ghost', 'icon')"
                :disabled="month === 11"
                :aria-label="t('booking.nextMonth')"
                @click="changeMonth(month + 1)"
              >
                <ChevronRight />
              </button>
            </div>
          </div>
          <div class="mt-4 flex gap-2 overflow-x-auto pb-2" role="group" :aria-label="t('booking.date')">
            <button
              v-for="day in days"
              :key="day.value"
              type="button"
              :class="buttonClasses('ghost', undefined, cn('h-20 w-16 shrink-0 flex-col border', day.value === selectedDate ? 'border-primary bg-primary text-primary-foreground' : 'border-border bg-card'))"
              :disabled="day.disabled"
              :data-date="day.value"
              :aria-label="day.label"
              :aria-pressed="day.value === selectedDate"
              @click="selectedDate = day.value"
            >
              <span class="text-[10px] font-semibold">{{ day.weekday }}</span>
              <span class="font-display text-2xl">{{ day.day }}</span>
            </button>
          </div>
        </section>

        <section class="mt-9 border-t border-border pt-8">
          <h2 class="font-display text-2xl font-semibold">
            {{ t('booking.whichTime') }}
          </h2>
          <p class="mt-1 text-sm text-muted-foreground">
            {{ t('booking.reservedWith', { duration: formatDuration(totalDuration), name: selectedProfessional?.name ?? '' }) }}
          </p>
          <div v-if="!selectedServices.length" class="mt-4 border border-border bg-card p-4 text-sm text-muted-foreground">
            <p class="font-semibold text-foreground">{{ t('booking.selectProcedureTitle') }}</p>
            <p class="mt-1">{{ t('booking.selectProcedureBody') }}</p>
          </div>
          <p v-else-if="slotState === 'loading'" class="mt-4 text-sm text-muted-foreground">
            {{ t('booking.loadingSlots') }}
          </p>
          <p v-else-if="slotState === 'error'" class="mt-4 text-sm text-red-500">
            {{ t('auth.genericError') }}
          </p>
          <div v-else-if="slotState === 'empty'" class="mt-4 border border-border bg-card p-4 text-sm text-muted-foreground">
            <p class="font-semibold text-foreground">{{ t('booking.noFitTitle') }}</p>
            <p class="mt-1">{{ t('booking.noFitBody') }}</p>
          </div>
          <div v-else class="mt-4 grid grid-cols-3 gap-2 sm:grid-cols-5" role="group" :aria-label="t('booking.time')">
            <button
              v-for="slot in booking.availableSlots"
              :key="slot.starts_at"
              type="button"
              :class="buttonClasses('outline', undefined, cn('h-12', isSelected(slot) && 'border-primary bg-primary text-primary-foreground hover:bg-primary/90'))"
              :aria-pressed="isSelected(slot)"
              @click="chooseSlot(slot)"
            >
              {{ slotTime(slot) }}
            </button>
          </div>
        </section>
      </template>
    </div>

    <div class="fixed inset-x-0 bottom-16 z-30 border-t border-border bg-background/95 p-4 backdrop-blur md:static md:mx-auto md:mt-2 md:max-w-5xl md:border md:p-5">
      <div class="mx-auto grid max-w-5xl grid-cols-[minmax(0,1fr)_auto] items-center gap-4">
        <div class="min-w-0">
          <p class="truncate text-xs font-semibold">
            {{ summaryTitle }}
          </p>
          <p class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-muted-foreground">
            <span class="flex items-center gap-1">
              <CalendarDays class="size-3" />
              {{ selectedDay?.label }}
            </span>
            <span class="flex items-center gap-1">
              <Clock3 class="size-3" />
              {{ selectedTime || t('booking.chooseTime') }}
            </span>
            <span class="flex items-center gap-1">
              <Minus class="size-3" />
              {{ formatDuration(totalDuration) }} · {{ t('catalog.price', { price: totalPrice }) }}
            </span>
          </p>
          <p v-if="booked" class="mt-2 text-sm text-primary">
            {{ t('booking.confirmed') }}
          </p>
          <p v-else-if="failed" class="mt-2 text-sm text-red-500">
            {{ booking.slotUnavailable ? t('errors.slotUnavailable') : t('auth.genericError') }}
          </p>
        </div>
        <button type="button" :class="buttonClasses('default', 'lg')" :disabled="!canConfirm" @click="confirm">
          <Check />
          {{ booking.isLoading ? t('auth.submitting') : t('booking.confirm') }}
        </button>
      </div>
    </div>

    <BottomNav active="schedule" :slug="slug" />
  </main>
</template>
