import { flushPromises, mount } from '@vue/test-utils'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createMemoryHistory, createRouter } from 'vue-router'
import type { Professional } from '@/Domain/Catalog/professionalModel'
import type { Service } from '@/Domain/Catalog/serviceModel'
import type { AvailableSlot } from '@/Domain/Orchestrator/bookingModel'
import BookingView from '@/Domain/Orchestrator/BookingView.vue'
import type { Barbershop } from '@/Domain/Tenant/barbershopModel'
import { i18n } from '@/i18n'
import { AppointmentStatus } from '@/types/appointmentStatus'

const { bookingState, catalogState, tenantState, fetchAvailableSlots, book, nextSlots } = vi.hoisted(() => {
  // vitest hoists this callback before imports, so Vue is loaded here.
  // eslint-disable-next-line @typescript-eslint/no-require-imports
  const { reactive } = require('vue') as typeof import('vue')
  const nextSlots = { current: [] as AvailableSlot[] }
  const bookingState = reactive({
    availableSlots: [] as AvailableSlot[],
    slotsLoading: false,
    slotsError: false,
    slotUnavailable: false,
    isLoading: false,
    fetchAvailableSlots: vi.fn(async () => [] as AvailableSlot[]),
    book: vi.fn(),
  })
  const catalogState = reactive({
    services: [] as Service[],
    professionals: [] as Professional[],
    loading: false,
    notFound: false,
    fetchPublicCatalog: vi.fn(async () => {}),
  })
  const tenantState = reactive({
    barbershop: null as Barbershop | null,
    loading: false,
    notFound: false,
    fetchBarbershop: vi.fn(async () => {}),
  })

  return {
    bookingState,
    catalogState,
    tenantState,
    fetchAvailableSlots: bookingState.fetchAvailableSlots,
    book: bookingState.book,
    nextSlots,
  }
})

vi.mock('@/Domain/Orchestrator/publicBookingStore', () => ({
  usePublicBookingStore: () => bookingState,
}))

vi.mock('@/Domain/Catalog/publicCatalogStore', () => ({
  usePublicCatalogStore: () => catalogState,
}))

vi.mock('@/Domain/Tenant/publicTenantStore', () => ({
  usePublicTenantStore: () => tenantState,
}))

const service: Service = {
  id: 8,
  barbershop_id: 1,
  name: 'Corte assinatura',
  description: null,
  price: 65,
  duration_in_minutes: 30,
  active: true,
}

const diego: Professional = {
  id: 4,
  barbershop_id: 1,
  user_id: 2,
  name: 'Diego',
  specialty: 'Fade & textura',
  avatar_url: null,
  experience_years: null,
  active: true,
}

const rui: Professional = {
  ...diego,
  id: 7,
  user_id: 3,
  name: 'Rui',
  specialty: 'Barba & clássico',
}

const slots: AvailableSlot[] = [
  { starts_at: '2026-10-15 09:00:00' },
  { starts_at: '2026-10-15 09:30:00' },
]

const shop: Barbershop = {
  id: 1,
  name: 'Navalha',
  slug: 'north-shop',
  active: true,
  address: null,
  city: null,
  closing_time: null,
  rating: 5,
  total_reviews: 0,
  banner_url: null,
}

async function mountView(query = 'service=8') {
  const router = createRouter({
    history: createMemoryHistory(),
    routes: [
      { path: '/:slug/agendamento', name: 'public-booking', component: BookingView },
      { path: '/:slug', name: 'public-home', component: { template: '<div />' } },
      { path: '/', name: 'public-home-missing', component: { template: '<div />' } },
      { path: '/dashboard', name: 'dashboard', component: { template: '<div />' } },
    ],
  })

  await router.push(`/north-shop/agendamento?${query}`)
  await router.isReady()

  return mount(BookingView, {
    global: { plugins: [router, i18n] },
  })
}

function bookableStart(): string {
  const date = new Date()
  date.setHours(0, 0, 0, 0)

  if (date.getDay() === 0) {
    const next = new Date(date)
    next.setDate(date.getDate() + 1)

    if (next.getFullYear() === date.getFullYear()) {
      date.setDate(date.getDate() + 1)
    }
  }

  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')

  return `${date.getFullYear()}-${month}-${day}`
}

describe('BookingView', () => {
  beforeEach(() => {
    nextSlots.current = slots
    catalogState.services = [service]
    catalogState.professionals = [diego, rui]
    catalogState.loading = false
    catalogState.notFound = false
    tenantState.barbershop = shop
    tenantState.loading = false
    tenantState.notFound = false
    bookingState.availableSlots = []
    bookingState.slotsLoading = false
    bookingState.slotsError = false
    bookingState.slotUnavailable = false
    bookingState.isLoading = false
    fetchAvailableSlots.mockReset()
    fetchAvailableSlots.mockImplementation(async () => {
      bookingState.availableSlots = [...nextSlots.current]
      return bookingState.availableSlots
    })
    book.mockReset()
    book.mockResolvedValue({
      id: 3,
      barbershop_id: 1,
      professional_id: 4,
      service_id: 8,
      client_name: 'Cliente Logado',
      client_email: 'cliente@exemplo.com',
      starts_at: '2026-10-15 09:30:00',
      ends_at: '2026-10-15 10:00:00',
      price: 65,
      status: AppointmentStatus.Pending,
    })
  })

  it('shows the barber cards, the day strip and only the returned hours', async () => {
    const wrapper = await mountView()
    await flushPromises()

    const times = wrapper.get('[aria-label="Horário"]').findAll('button')
    const cards = wrapper.get('[role="radiogroup"]').findAll('button')

    const sundays = wrapper.get('[aria-label="Dia"]').findAll('button').filter((button) => button.text().includes('DOM'))

    expect(times.map((button) => button.text())).toEqual(['09:00', '09:30'])
    expect(cards[0]?.text()).toContain('Diego')
    expect(cards[0]?.text()).toContain('Fade & textura')
    expect(cards[1]?.text()).toContain('Rui')
    expect(cards[1]?.text()).toContain('Barba & clássico')
    expect(wrapper.text()).toContain('Escolha seu horário')
    expect(wrapper.text()).toContain('Agendamento · 4 de 4')
    expect(wrapper.text()).toContain('Corte assinatura · Diego')
    expect(wrapper.find('input').exists()).toBe(false)
    expect(sundays.length).toBeGreaterThan(0)
    sundays.forEach((button) => expect(button.attributes('disabled')).toBeDefined())
  })

  it('refetches when the barber or the day changes', async () => {
    const wrapper = await mountView()
    await flushPromises()

    expect(fetchAvailableSlots).toHaveBeenCalledWith('north-shop', 4, bookableStart(), [8])

    const ruiButton = wrapper.get('[role="radiogroup"]').findAll('button').find((button) => button.text().includes('Rui'))
    await ruiButton?.trigger('click')
    await flushPromises()

    expect(fetchAvailableSlots).toHaveBeenLastCalledWith('north-shop', 7, bookableStart(), [8])

    const nextOpen = wrapper
      .get('[aria-label="Dia"]')
      .findAll('button')
      .find((button) => button.attributes('disabled') === undefined && button.attributes('aria-pressed') !== 'true')
    await nextOpen?.trigger('click')
    await flushPromises()

    expect(fetchAvailableSlots).toHaveBeenLastCalledWith('north-shop', 7, nextOpen?.attributes('data-date'), [8])
  })

  it('confirms the chosen hour without asking for a name or email', async () => {
    const wrapper = await mountView()
    await flushPromises()

    const hour = wrapper.findAll('button').find((button) => button.text() === '09:30')
    await hour?.trigger('click')
    const confirm = wrapper.findAll('button').find((button) => button.text().includes('Confirmar'))
    await confirm?.trigger('click')
    await flushPromises()

    expect(book).toHaveBeenCalledWith('north-shop', {
      professional_id: 4,
      service_ids: [8],
      starts_at: `${bookableStart()} 09:30:00`,
    })
    expect(wrapper.text()).toContain('Horário agendado.')
    expect(wrapper.find('input').exists()).toBe(false)
  })

  it('shows the empty day copy when the list has no hours', async () => {
    nextSlots.current = []

    const wrapper = await mountView()
    await flushPromises()

    expect(wrapper.text()).toContain('Nenhum horário comporta estes procedimentos.')
    expect(wrapper.find('[aria-label="Horário"]').exists()).toBe(false)
  })

  it('disables confirm while the request is running', async () => {
    const wrapper = await mountView()
    await flushPromises()

    const hour = wrapper.findAll('button').find((button) => button.text() === '09:30')
    await hour?.trigger('click')
    bookingState.isLoading = true
    await wrapper.vm.$nextTick()

    const confirm = wrapper.findAll('button').find((button) => button.text().includes('A enviar'))

    expect(confirm?.attributes('disabled')).toBeDefined()
  })

  it('seeds multiple services from the visagism services query', async () => {
    const beard: Service = {
      ...service,
      id: 9,
      name: 'Barba',
      price: 25,
      duration_in_minutes: 20,
    }
    catalogState.services = [service, beard]

    const wrapper = await mountView('services=8,9')
    await flushPromises()

    expect(wrapper.text()).toContain('Corte assinatura + Barba · Diego')
    expect(fetchAvailableSlots).toHaveBeenCalledWith('north-shop', 4, bookableStart(), [8, 9])
  })

  it('seeds service and product from the visagism query', async () => {
    const pomade: Service = {
      ...service,
      id: 12,
      name: 'Pomada Modeladora',
      price: 49,
      duration_in_minutes: 5,
    }
    catalogState.services = [service, pomade]

    const wrapper = await mountView('service=8&product=12')
    await flushPromises()

    expect(wrapper.text()).toContain('Corte assinatura + Pomada Modeladora · Diego')
    expect(fetchAvailableSlots).toHaveBeenCalledWith('north-shop', 4, bookableStart(), [8, 12])
  })

  it('lets the showcase service be removed and moves the month', async () => {
    const beard: Service = {
      ...service,
      id: 9,
      name: 'Barba',
      price: 25,
      duration_in_minutes: 20,
    }
    catalogState.services = [service, beard]

    const wrapper = await mountView()
    await flushPromises()

    const remove = wrapper.findAll('button').find((button) => button.text().includes('Remover'))
    await remove?.trigger('click')
    await flushPromises()

    expect(wrapper.text()).toContain('Nenhum procedimento selecionado')
    expect(wrapper.text()).toContain('Selecione um procedimento.')

    const barba = wrapper.findAll('article').find((article) => article.text().includes('Barba'))
    await barba?.get('button').trigger('click')
    await flushPromises()

    expect(wrapper.text()).toContain('Barba · Diego')
    expect(fetchAvailableSlots).toHaveBeenLastCalledWith('north-shop', 4, bookableStart(), [9])

    const month = wrapper.get('p.capitalize').text()
    const nextMonth = wrapper.get('[aria-label="Próximo mês"]')
    const previousMonth = wrapper.get('[aria-label="Mês anterior"]')
    const control = nextMonth.attributes('disabled') === undefined ? nextMonth : previousMonth
    await control.trigger('click')
    await flushPromises()

    expect(wrapper.get('p.capitalize').text()).not.toBe(month)
  })
})
