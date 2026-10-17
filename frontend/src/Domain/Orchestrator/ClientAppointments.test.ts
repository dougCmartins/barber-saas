import { flushPromises, mount } from '@vue/test-utils'
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'
import { createMemoryHistory, createRouter } from 'vue-router'
import type { Professional } from '@/Domain/Catalog/professionalModel'
import ClientAppointments from '@/Domain/Orchestrator/ClientAppointments.vue'
import type { ClientAppointment } from '@/Domain/Schedule/clientAppointmentModel'
import { i18n } from '@/i18n'
import { AppointmentStatus } from '@/types/appointmentStatus'

const { catalogState, scheduleState } = vi.hoisted(() => {
  // vitest hoists this callback before imports, so Vue is loaded here.
  // eslint-disable-next-line @typescript-eslint/no-require-imports
  const { reactive } = require('vue') as typeof import('vue')

  return {
    catalogState: reactive({
      services: [],
      professionals: [] as Professional[],
      loading: false,
      notFound: false,
      fetchPublicCatalog: vi.fn(async () => {}),
    }),
    scheduleState: reactive({
      items: [] as ClientAppointment[],
      loading: false,
      filters: {
        from: undefined as string | undefined,
        to: undefined as string | undefined,
        statuses: [] as string[],
        professionalId: null as number | null,
      },
      fetchAppointments: vi.fn(async () => {}),
      cancelAppointment: vi.fn(async () => {}),
      resetFilters: vi.fn(),
    }),
  }
})

vi.mock('@/Domain/Catalog/publicCatalogStore', () => ({
  usePublicCatalogStore: () => catalogState,
}))

vi.mock('@/Domain/Schedule/clientScheduleStore', () => ({
  useClientScheduleStore: () => scheduleState,
}))

const confirmed: ClientAppointment = {
  id: 1,
  starts_at: '2026-10-20 16:00:00',
  ends_at: '2026-10-20 16:45:00',
  professional_id: 4,
  professional_name: 'Rafa Santos',
  professional_avatar_url: null,
  service_names: ['Corte assinatura'],
  price: 65,
  status: AppointmentStatus.Confirmed,
  notes: null,
}

const pending: ClientAppointment = {
  id: 2,
  starts_at: '2026-10-25 10:00:00',
  ends_at: '2026-10-25 10:30:00',
  professional_id: 5,
  professional_name: 'Léo Martins',
  professional_avatar_url: null,
  service_names: ['Fade'],
  price: 75,
  status: AppointmentStatus.Pending,
  notes: null,
}

async function mountPage() {
  const router = createRouter({
    history: createMemoryHistory(),
    routes: [
      { path: '/', name: 'public-home-missing', component: { template: '<div />' } },
      { path: '/meus-agendamentos', name: 'client-appointments', component: ClientAppointments },
      { path: '/:slug/agendamento', name: 'public-booking', component: { template: '<div />' } },
    ],
  })

  await router.push('/meus-agendamentos')
  await router.isReady()

  const wrapper = mount(ClientAppointments, {
    global: { plugins: [router, i18n] },
  })
  await flushPromises()

  return { wrapper, router }
}

function buttonByText(wrapper: Awaited<ReturnType<typeof mountPage>>['wrapper'], label: string) {
  return wrapper.findAll('button').find((button) => button.text().replace(/\s+/g, ' ').trim() === label)
}

describe('ClientAppointments', () => {
  beforeEach(() => {
    vi.useFakeTimers({ toFake: ['Date'] })
    vi.setSystemTime(new Date(2026, 9, 17, 12, 0, 0))
    catalogState.professionals = [
      {
        id: 4,
        barbershop_id: 1,
        user_id: 2,
        name: 'Rafa Santos',
        specialty: null,
        avatar_url: null,
        experience_years: null,
        active: true,
      },
      {
        id: 5,
        barbershop_id: 1,
        user_id: 3,
        name: 'Léo Martins',
        specialty: null,
        avatar_url: null,
        experience_years: null,
        active: true,
      },
    ]
    scheduleState.items = [confirmed, pending]
    scheduleState.filters = { statuses: [], professionalId: null }
    scheduleState.fetchAppointments.mockClear()
    scheduleState.cancelAppointment.mockClear()
    scheduleState.cancelAppointment.mockImplementation(async (id: number) => {
      scheduleState.items = scheduleState.items.map((item) =>
        item.id === id ? { ...item, status: AppointmentStatus.Canceled } : item,
      )
    })
  })

  afterEach(() => {
    vi.useRealTimers()
  })

  it('opens the period filter sheet', async () => {
    const { wrapper } = await mountPage()

    await buttonByText(wrapper, 'Período')?.trigger('click')
    await flushPromises()

    expect(wrapper.find('[role="dialog"]').exists()).toBe(true)
    expect(wrapper.text()).toContain('Filtrar por período')
  })

  it('cancels an open appointment through the store', async () => {
    const { wrapper } = await mountPage()

    const before = wrapper.findAll('button').filter((button) => button.text() === 'Cancelar').length
    expect(before).toBe(2)

    await buttonByText(wrapper, 'Cancelar')?.trigger('click')
    await flushPromises()

    expect(scheduleState.cancelAppointment).toHaveBeenCalled()
    const after = wrapper.findAll('button').filter((button) => button.text() === 'Cancelar').length
    expect(after).toBe(1)
    expect(wrapper.text()).toContain('Cancelado')
  })

  it('reloads the list when filters are applied', async () => {
    const { wrapper } = await mountPage()

    await buttonByText(wrapper, 'Situação')?.trigger('click')
    await flushPromises()
    await buttonByText(wrapper, 'Concluído')?.trigger('click')
    await buttonByText(wrapper, 'Aplicar')?.trigger('click')
    await flushPromises()

    expect(scheduleState.fetchAppointments).toHaveBeenCalled()
    const lastCall = scheduleState.fetchAppointments.mock.calls.at(-1)?.[0]
    expect(lastCall?.statuses).toContain(AppointmentStatus.Completed)
  })
})
