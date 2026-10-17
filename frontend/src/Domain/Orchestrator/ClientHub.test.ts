import { flushPromises, mount } from '@vue/test-utils'
import type { VueWrapper } from '@vue/test-utils'
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'
import { createMemoryHistory, createRouter } from 'vue-router'
import type { Professional } from '@/Domain/Catalog/professionalModel'
import type { Service } from '@/Domain/Catalog/serviceModel'
import ClientHub from '@/Domain/Orchestrator/ClientHub.vue'
import type { ClientAppointment } from '@/Domain/Schedule/clientAppointmentModel'
import { i18n } from '@/i18n'
import { AppointmentStatus } from '@/types/appointmentStatus'

const { catalogState, authState, scheduleState } = vi.hoisted(() => {
  // vitest hoists this callback before imports, so Vue is loaded here.
  // eslint-disable-next-line @typescript-eslint/no-require-imports
  const { reactive } = require('vue') as typeof import('vue')

  return {
    catalogState: reactive({
      services: [] as Service[],
      professionals: [] as Professional[],
      loading: false,
      notFound: false,
      fetchPublicCatalog: vi.fn(async () => {}),
      fetchServices: vi.fn(async () => {}),
    }),
    authState: reactive({
      user: {
        id: 1,
        first_name: 'Ana',
        email: 'ana@example.com',
        avatar_url: '/hero.jpg',
      } as {
        id: number
        first_name: string
        email: string
        avatar_url: string
      } | null,
      loading: false,
      fetchMe: vi.fn(async () => {}),
      clear: vi.fn(),
    }),
    scheduleState: reactive({
      items: [] as ClientAppointment[],
      loading: false,
      filters: { statuses: [], professionalId: null },
      fetchAppointments: vi.fn(async () => {}),
      cancelAppointment: vi.fn(async () => {}),
      resetFilters: vi.fn(),
    }),
  }
})

vi.mock('@/Domain/Catalog/publicCatalogStore', () => ({
  usePublicCatalogStore: () => catalogState,
}))

vi.mock('@/Domain/Identity/clientAuthStore', () => ({
  useClientAuthStore: () => authState,
}))

vi.mock('@/Domain/Schedule/clientScheduleStore', () => ({
  useClientScheduleStore: () => scheduleState,
}))

const services: Service[] = [
  {
    id: 8,
    barbershop_id: 1,
    name: 'Corte assinatura',
    description: null,
    price: 65,
    duration_in_minutes: 45,
    active: true,
  },
  {
    id: 9,
    barbershop_id: 1,
    name: 'Ritual completo',
    description: null,
    price: 110,
    duration_in_minutes: 80,
    active: true,
  },
]

const professionals: Professional[] = [
  {
    id: 4,
    barbershop_id: 1,
    user_id: 2,
    name: 'Rafa Santos',
    specialty: 'Barba & clássico',
    avatar_url: null,
    experience_years: null,
    active: true,
  },
  {
    id: 5,
    barbershop_id: 1,
    user_id: 3,
    name: 'Léo Martins',
    specialty: 'Fade & textura',
    avatar_url: null,
    experience_years: null,
    active: true,
  },
]

const upcoming: ClientAppointment = {
  id: 1,
  starts_at: '2026-10-15 16:00:00',
  ends_at: '2026-10-15 17:00:00',
  professional_id: 4,
  professional_name: 'Rafa Santos',
  professional_avatar_url: null,
  service_names: ['Corte assinatura', 'Barboterapia'],
  price: 120,
  status: AppointmentStatus.Pending,
  notes: null,
}

const past: ClientAppointment = {
  id: 2,
  starts_at: '2026-09-28 16:00:00',
  ends_at: '2026-09-28 16:45:00',
  professional_id: 4,
  professional_name: 'Rafa Santos',
  professional_avatar_url: null,
  service_names: ['Corte assinatura'],
  price: 65,
  status: AppointmentStatus.Completed,
  notes: null,
}

function linkWithText(wrapper: VueWrapper, label: string) {
  return wrapper.findAll('a').find((link) => link.text().replace(/\s+/g, ' ').trim() === label)
}

async function mountHub() {
  const router = createRouter({
    history: createMemoryHistory(),
    routes: [
      { path: '/', name: 'public-home-missing', component: ClientHub },
      {
        path: '/meus-agendamentos',
        name: 'client-appointments',
        component: { template: '<div />' },
      },
      { path: '/:slug/agendamento', name: 'public-booking', component: { template: '<div />' } },
      { path: '/:slug/visagism', name: 'visagism', component: { template: '<div />' } },
      { path: '/:slug', name: 'public-home', component: { template: '<div />' } },
    ],
  })

  await router.push('/')
  await router.isReady()

  const wrapper = mount(ClientHub, {
    global: { plugins: [router, i18n] },
  })
  await flushPromises()

  return { wrapper, router }
}

describe('ClientHub', () => {
  beforeEach(() => {
    vi.useFakeTimers({ toFake: ['Date'] })
    vi.setSystemTime(new Date(2026, 9, 12, 12, 0, 0))
    catalogState.services = services
    catalogState.professionals = professionals
    catalogState.fetchPublicCatalog.mockClear()
    authState.user = {
      id: 1,
      first_name: 'Ana',
      email: 'ana@example.com',
      avatar_url: '/hero.jpg',
    }
    authState.fetchMe.mockClear()
    scheduleState.items = [upcoming, past]
    scheduleState.fetchAppointments.mockClear()
  })

  afterEach(() => {
    vi.useRealTimers()
  })

  it('greets the client and shows the next appointment', async () => {
    const { wrapper } = await mountHub()

    expect(wrapper.text()).toContain('Olá, Ana')
    expect(wrapper.text()).toContain('16:00')
    expect(wrapper.text()).toContain('Quinta, 15 de outubro')
    expect(wrapper.text()).toContain('Rafa Santos')
    expect(wrapper.text()).toContain('R$ 120')
    expect(wrapper.text()).toContain('Corte assinatura + Barboterapia')
    expect(catalogState.fetchPublicCatalog).toHaveBeenCalledWith('navalha')
    expect(authState.fetchMe).toHaveBeenCalled()
    expect(scheduleState.fetchAppointments).toHaveBeenCalled()
  })

  it('hides the next appointment card when nothing is upcoming', async () => {
    scheduleState.items = [past]
    vi.setSystemTime(new Date(2026, 9, 16, 12, 0, 0))

    const { wrapper } = await mountHub()

    expect(wrapper.find('[aria-label="Próximo agendamento"]').exists()).toBe(false)
    expect(wrapper.text()).toContain('Novo Agendamento')
  })

  it('lists the professionals from the catalog', async () => {
    const { wrapper } = await mountHub()

    expect(wrapper.text()).toContain('Rafa')
    expect(wrapper.text()).toContain('Léo')
  })

  it('opens booking from the new appointment action and the nav', async () => {
    const { wrapper, router } = await mountHub()

    await linkWithText(wrapper, 'Novo Agendamento')?.trigger('click')
    await flushPromises()

    expect(router.currentRoute.value.name).toBe('public-booking')
    expect(router.currentRoute.value.params.slug).toBe('navalha')

    await router.push('/')
    await flushPromises()

    await linkWithText(wrapper, 'Agendar')?.trigger('click')
    await flushPromises()

    expect(router.currentRoute.value.name).toBe('public-booking')
    expect(router.currentRoute.value.params.slug).toBe('navalha')
  })

  it('rebooks a past procedure with the catalog service id', async () => {
    const { wrapper, router } = await mountHub()

    const again = wrapper.findAll('button').find((button) => button.text() === 'Agendar novamente')
    await again?.trigger('click')
    await flushPromises()

    expect(router.currentRoute.value.name).toBe('public-booking')
    expect(router.currentRoute.value.params.slug).toBe('navalha')
    expect(router.currentRoute.value.query.service).toBe('8')
  })

  it('opens all appointments from the hub link and profile nav', async () => {
    const { wrapper, router } = await mountHub()

    await linkWithText(wrapper, 'Ver todos os agendamentos →')?.trigger('click')
    await flushPromises()

    expect(router.currentRoute.value.name).toBe('client-appointments')

    await router.push('/')
    await flushPromises()

    await linkWithText(wrapper, 'Perfil')?.trigger('click')
    await flushPromises()

    expect(router.currentRoute.value.name).toBe('client-appointments')
  })
})
