import { flushPromises, mount } from '@vue/test-utils'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createMemoryHistory, createRouter } from 'vue-router'
import type { Service } from '@/Domain/Catalog/serviceModel'
import PublicHome from '@/Domain/Tenant/PublicHome.vue'
import type { Barbershop } from '@/Domain/Tenant/barbershopModel'
import { i18n } from '@/i18n'

const { catalogState, tenantState } = vi.hoisted(() => {
  // vitest hoists this callback before imports, so Vue is loaded here.
  // eslint-disable-next-line @typescript-eslint/no-require-imports
  const { reactive } = require('vue') as typeof import('vue')

  return {
    catalogState: reactive({
      services: [] as Service[],
      professionals: [],
      loading: false,
      notFound: false,
      fetchPublicCatalog: vi.fn(async () => {}),
      fetchServices: vi.fn(async () => {}),
    }),
    tenantState: reactive({
      barbershop: null as Barbershop | null,
      loading: false,
      notFound: false,
      fetchBarbershop: vi.fn(async () => {}),
    }),
  }
})

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

const shop: Barbershop = {
  id: 1,
  name: 'Navalha',
  slug: 'navalha',
  active: true,
  address: null,
  city: null,
  closing_time: null,
  rating: 5,
  total_reviews: 0,
  banner_url: null,
}

describe('PublicHome', () => {
  beforeEach(() => {
    catalogState.services = [service]
    catalogState.loading = false
    catalogState.notFound = false
    tenantState.barbershop = shop
    tenantState.loading = false
    tenantState.notFound = false
  })

  it('opens the booking page with the chosen service id', async () => {
    const router = createRouter({
      history: createMemoryHistory(),
      routes: [
        { path: '/:slug/agendamento', name: 'public-booking', component: { template: '<div />' } },
        { path: '/:slug/visagism', name: 'visagism', component: { template: '<div />' } },
        { path: '/:slug', name: 'public-home', component: PublicHome },
        { path: '/', name: 'public-home-missing', component: { template: '<div />' } },
        { path: '/login', name: 'login', component: { template: '<div />' } },
        { path: '/dashboard', name: 'dashboard', component: { template: '<div />' } },
      ],
    })

    await router.push('/navalha')
    await router.isReady()

    const wrapper = mount(PublicHome, {
      global: { plugins: [router, i18n] },
    })
    await flushPromises()

    const choose = wrapper.findAll('button').find((button) => button.text() === 'Escolher')
    await choose?.trigger('click')
    await flushPromises()

    expect(router.currentRoute.value.name).toBe('public-booking')
    expect(router.currentRoute.value.params.slug).toBe('navalha')
    expect(router.currentRoute.value.query.service).toBe('8')
  })
})
