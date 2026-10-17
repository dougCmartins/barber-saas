<script setup lang="ts">
import { Plus, Sparkles } from '@lucide/vue'
import { computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { RouterLink, useRouter } from 'vue-router'
import heroImage from '@/assets/barbershop-hero.jpg'
import pomadeImage from '@/assets/pomade.jpg'
import { usePublicCatalogStore } from '@/Domain/Catalog/publicCatalogStore'
import { useClientAuthStore } from '@/Domain/Identity/clientAuthStore'
import ClientBarberCarousel from '@/Domain/Orchestrator/ClientBarberCarousel.vue'
import ClientHubNav from '@/Domain/Orchestrator/ClientHubNav.vue'
import ClientNextAppointment from '@/Domain/Orchestrator/ClientNextAppointment.vue'
import ClientProcedureList from '@/Domain/Orchestrator/ClientProcedureList.vue'
import type { HistoryEntry } from '@/Domain/Orchestrator/ClientProcedureList.vue'
import ClientWelcomeHeader from '@/Domain/Orchestrator/ClientWelcomeHeader.vue'
import { shopSlug } from '@/Domain/Orchestrator/clientHubModel'
import { emptyClientAppointmentFilters } from '@/Domain/Schedule/clientAppointmentModel'
import { useClientScheduleStore } from '@/Domain/Schedule/clientScheduleStore'
import { buttonClasses } from '@/ui/buttonClasses'
import { clock, dayMonth, dayShortMonth, isPastStart, nearestUpcoming, weekdayLabel } from '@/utils/dateTime'

const router = useRouter()
const { t } = useI18n()
const authStore = useClientAuthStore()
const catalog = usePublicCatalogStore()
const schedule = useClientScheduleStore()

const bookingTo = computed(() => ({
  name: 'public-booking' as const,
  params: { slug: shopSlug },
}))

const greetingName = computed(() => authStore.user?.first_name ?? t('clientAppointments.guestName'))
const greetingAvatar = computed(() => authStore.user?.avatar_url ?? heroImage)

const nextAppointment = computed(() => {
  const procedure = nearestUpcoming(schedule.items)

  if (!procedure) {
    return null
  }

  return {
    time: clock(procedure.starts_at),
    weekday: weekdayLabel(procedure.starts_at),
    date: dayMonth(procedure.starts_at),
    professionalName: procedure.professional_name,
    avatarUrl: procedure.professional_avatar_url || avatarFor(procedure.professional_name),
    servicesLabel: procedure.service_names.join(' + '),
    price: procedure.price,
  }
})

const history = computed((): HistoryEntry[] =>
  schedule.items
    .filter((item) => isPastStart(item.starts_at))
    .sort((left, right) => right.starts_at.localeCompare(left.starts_at))
    .map((item) => {
      const serviceName = item.service_names[0] ?? ''
      const service = catalog.services.find((entry) => entry.name === serviceName)

      return {
        id: item.id,
        date: dayShortMonth(item.starts_at),
        serviceName,
        professionalName: item.professional_name,
        serviceId: service?.id ?? null,
      }
    }),
)

onMounted(() => {
  void authStore.fetchMe()
  void catalog.fetchPublicCatalog(shopSlug)
  void schedule.fetchAppointments({ ...emptyClientAppointmentFilters }).catch(() => undefined)
})

function avatarFor(name: string): string {
  const match = catalog.professionals.find((item) => item.name === name)

  if (match?.avatar_url) {
    return match.avatar_url
  }

  return heroImage
}

function rebook(serviceId: number): void {
  void router.push({
    name: 'public-booking',
    params: { slug: shopSlug },
    query: { service: String(serviceId) },
  })
}
</script>

<template>
  <main class="mx-auto flex min-h-screen max-w-lg flex-col gap-4 bg-background px-4 pb-28 pt-6 text-foreground">
    <ClientWelcomeHeader :first-name="greetingName" :avatar-url="greetingAvatar" />

    <ClientNextAppointment
      v-if="nextAppointment"
      :time="nextAppointment.time"
      :weekday="nextAppointment.weekday"
      :date="nextAppointment.date"
      :professional-name="nextAppointment.professionalName"
      :avatar-url="nextAppointment.avatarUrl"
      :services-label="nextAppointment.servicesLabel"
      :price="nextAppointment.price"
      :booking-to="bookingTo"
    />

    <RouterLink
      :to="{ name: 'client-appointments' }"
      class="-mt-2 self-end text-sm font-semibold text-primary"
    >
      {{ t('clientAppointments.viewAll') }}
    </RouterLink>

    <RouterLink :to="bookingTo" :class="buttonClasses('default', 'lg', 'h-12 w-full gap-2')">
      <Plus class="size-4" /> Novo Agendamento
    </RouterLink>

    <section class="flex flex-col gap-4 pt-2">
      <h2 class="font-display text-xl font-semibold uppercase tracking-wide text-foreground">
        Seus barbeiros favoritos
      </h2>
      <ClientBarberCarousel
        :professionals="catalog.professionals"
        :fallback-avatar-url="heroImage"
        :booking-to="bookingTo"
      />
    </section>

    <section class="flex flex-col gap-4 pt-2">
      <h2 class="font-display text-xl font-semibold uppercase tracking-wide text-foreground">Para você</h2>
      <div class="flex flex-col gap-4">
        <RouterLink
          :to="{ name: 'visagism', params: { slug: shopSlug } }"
          class="flex items-center gap-4 rounded-lg border border-primary/30 bg-primary/10 p-4 transition-colors hover:bg-primary/15"
        >
          <span class="grid size-11 shrink-0 place-items-center rounded-md bg-primary text-primary-foreground">
            <Sparkles class="size-5" />
          </span>
          <div class="min-w-0">
            <h3 class="font-display text-xl font-semibold text-foreground">{{ t('catalog.aiTitle') }}</h3>
            <p class="mt-1 text-xs leading-5 text-muted-foreground">{{ t('catalog.aiSubtitle') }}</p>
          </div>
        </RouterLink>
        <article class="flex items-center gap-4 overflow-hidden rounded-lg border border-border bg-card p-3">
          <img
            :src="pomadeImage"
            alt="Pomada modeladora efeito matte"
            loading="lazy"
            width="816"
            height="816"
            class="size-20 shrink-0 rounded-md object-cover"
          />
          <div class="min-w-0">
            <h3 class="text-sm font-semibold leading-5 text-foreground">Pomada Modeladora Matte</h3>
            <p class="mt-1 text-xs text-primary">R$ 49 · retire na loja</p>
          </div>
        </article>
      </div>
    </section>

    <section class="flex flex-col gap-4 pt-2">
      <h2 class="font-display text-xl font-semibold uppercase tracking-wide text-foreground">Últimos procedimentos</h2>
      <ClientProcedureList :items="history" @rebook="rebook" />
    </section>

    <ClientHubNav active="home" home-to="/" :booking-to="bookingTo" />
  </main>
</template>
