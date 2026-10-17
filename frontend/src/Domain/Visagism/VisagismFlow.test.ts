import { flushPromises, mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createMemoryHistory, createRouter } from 'vue-router'
import type { VisagismResult } from '@/Domain/Visagism/visagismModel'
import VisagismFlow from '@/Domain/Visagism/VisagismFlow.vue'
import { useVisagismStore } from '@/Domain/Visagism/visagismStore'
import { useSessionStore } from '@/Domain/Identity/sessionStore'
import { http } from '@/http/client'
import { i18n } from '@/i18n'

vi.mock('@/http/client', () => ({
  http: vi.fn<(path: string, init?: RequestInit) => Promise<{ data: unknown }>>(),
}))

const httpMock = vi.mocked(http)

const mockResult: VisagismResult = {
  face_shape: 'square',
  face_geometry_summary: 'Mandíbula marcada e testa larga.',
  hair_type: 'wavy',
  suggestions: [
    {
      id: 1,
      name: 'Corte Clássico Texturizado',
      match_percentage: 98,
      justification: 'Suaviza os ângulos fortes da mandíbula.',
      image_url: null,
      service_id: 8,
      price: 65,
    },
  ],
  upsell_products: [
    {
      id: 1,
      name: 'Pomada Modeladora',
      price: 49,
      microcopy: 'Mantém o acabamento seco.',
      image_url: null,
      service_id: 12,
    },
  ],
}

async function mountFlow() {
  const pinia = createPinia()
  setActivePinia(pinia)

  const session = useSessionStore()
  session.token = {
    token_type: 'Bearer',
    access_token: 'test-token',
    expires_in: 3600,
  }

  httpMock.mockResolvedValue({
    data: { active: null, trashed_ai: null, trashed_manual: null },
  })

  const router = createRouter({
    history: createMemoryHistory(),
    routes: [
      { path: '/login', name: 'login', component: { template: '<div />' } },
      { path: '/:slug/visagism', name: 'visagism', component: VisagismFlow },
      { path: '/:slug/agendamento', name: 'public-booking', component: { template: '<div />' } },
      { path: '/:slug', name: 'public-home', component: { template: '<div />' } },
    ],
  })

  await router.push('/navalha/visagism')
  await router.isReady()

  const wrapper = mount(VisagismFlow, {
    attachTo: document.body,
    global: { plugins: [router, i18n, pinia] },
  })

  await flushPromises()

  return { wrapper, router, pinia }
}

describe('VisagismFlow', () => {
  beforeEach(() => {
    httpMock.mockReset()
  })

  it('starts on onboarding dossier selection', async () => {
    const { wrapper } = await mountFlow()

    expect(wrapper.text()).toContain('Como prefere começar?')
    expect(wrapper.text()).toContain('Consultoria Visagismo IA')
    expect(wrapper.text()).toContain('Já sei o que quero')

    wrapper.unmount()
  })

  it('shows diagnosis after analyze and allows override link', async () => {
    const { wrapper } = await mountFlow()
    const store = useVisagismStore()

    httpMock.mockResolvedValueOnce({ data: mockResult }).mockResolvedValueOnce({
      data: { active: null, trashed_ai: null, trashed_manual: null },
    })

    store.step = 'capture'
    await store.analyze('navalha')
    await flushPromises()
    await wrapper.vm.$nextTick()

    expect(wrapper.text()).toContain('Análise Concluída')
    expect(wrapper.text()).toContain('Quadrado')
    expect(wrapper.text()).toContain('Não concorda com a análise?')

    wrapper.unmount()
  })

  it('opens rescue modal when trashed ai exists', async () => {
    httpMock.mockResolvedValueOnce({
      data: {
        active: null,
        trashed_ai: { id: 3, consultation_type: 'ai' },
        trashed_manual: null,
      },
    })

    const { wrapper } = await mountFlow()
    const store = useVisagismStore()

    store.choosePath('ai')
    await flushPromises()
    await wrapper.vm.$nextTick()

    const bodyText = document.body.textContent ?? ''
    expect(bodyText).toContain('Progresso Encontrado')
    expect(bodyText).toContain('Resgatar Informações')

    wrapper.unmount()
  })

  it('hands service and product off to booking from upsell', async () => {
    const { wrapper, router } = await mountFlow()
    const store = useVisagismStore()

    httpMock.mockResolvedValueOnce({ data: mockResult }).mockResolvedValueOnce({
      data: { active: null, trashed_ai: null, trashed_manual: null },
    })

    await store.analyze('navalha')
    await flushPromises()
    await wrapper.vm.$nextTick()

    store.openSheet()
    store.toggleProduct(1)
    await flushPromises()
    await wrapper.vm.$nextTick()

    const confirm = Array.from(document.body.querySelectorAll('button')).find((button) =>
      (button.textContent ?? '').includes('Confirmar Agendamento'),
    )
    confirm?.dispatchEvent(new MouseEvent('click', { bubbles: true }))
    await flushPromises()

    expect(router.currentRoute.value.name).toBe('public-booking')
    expect(router.currentRoute.value.query.service).toBe('8')
    expect(router.currentRoute.value.query.product).toBe('12')

    wrapper.unmount()
  })
})
