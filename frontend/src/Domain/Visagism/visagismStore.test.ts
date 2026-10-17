import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import type { VisagismExistingState, VisagismResult } from '@/Domain/Visagism/visagismModel'
import { ConsultationType, VisagismStep } from '@/Domain/Visagism/visagismModel'
import { useVisagismStore } from '@/Domain/Visagism/visagismStore'
import { http } from '@/http/client'

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
      justification: 'Suaviza os ângulos.',
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

const emptyExisting: VisagismExistingState = {
  active: null,
  trashed_ai: null,
  trashed_manual: null,
}

describe('visagismStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    httpMock.mockReset()
  })

  it('checkExisting lands on onboarding when empty', async () => {
    httpMock.mockResolvedValueOnce({ data: emptyExisting })

    const store = useVisagismStore()
    await store.checkExisting('north-shop')

    expect(store.step).toBe(VisagismStep.Onboarding)
  })

  it('moves capture to analyzing then results on success', async () => {
    let release: (value: { data: VisagismResult }) => void = () => {}
    httpMock.mockImplementationOnce(
      () =>
        new Promise((resolve) => {
          release = resolve
        }),
    )
    httpMock.mockResolvedValueOnce({ data: emptyExisting })

    const store = useVisagismStore()
    const pending = store.analyze('north-shop', { image: 'data:image/jpeg;base64,abc' })

    expect(store.step).toBe(VisagismStep.Analyzing)
    expect(store.isProcessing).toBe(true)

    release({ data: mockResult })
    await pending

    expect(httpMock).toHaveBeenCalledWith(
      '/barbershops/north-shop/visagism/analyze',
      expect.objectContaining({ method: 'POST' }),
    )
    expect(store.step).toBe(VisagismStep.Results)
    expect(store.result?.face_shape).toBe('square')
    expect(store.isProcessing).toBe(false)
  })

  it('sets limitExceeded when analyze returns VISAGISM_LIMIT_EXCEEDED', async () => {
    httpMock.mockRejectedValue({ code: 'VISAGISM_LIMIT_EXCEEDED' })

    const store = useVisagismStore()
    await store.analyze('north-shop')

    expect(store.step).toBe(VisagismStep.Capture)
    expect(store.limitExceeded).toBe(true)
  })

  it('opens rescue modal when choosing path with trashed same type', async () => {
    httpMock.mockResolvedValueOnce({
      data: {
        active: null,
        trashed_ai: { id: 9, consultation_type: 'ai' },
        trashed_manual: null,
      },
    })

    const store = useVisagismStore()
    await store.checkExisting('north-shop')
    store.choosePath(ConsultationType.Ai)

    expect(store.rescueOpen).toBe(true)
    expect(store.rescueType).toBe('ai')
  })

  it('restore jumps to results for ai dossier', async () => {
    httpMock
      .mockResolvedValueOnce({
        data: {
          active: null,
          trashed_ai: { id: 9, consultation_type: 'ai' },
          trashed_manual: null,
        },
      })
      .mockResolvedValueOnce({
        data: {
          id: 9,
          consultation_type: 'ai',
          result: mockResult,
        },
      })

    const store = useVisagismStore()
    await store.checkExisting('north-shop')
    store.choosePath(ConsultationType.Ai)
    await store.restore('north-shop')

    expect(store.step).toBe(VisagismStep.Results)
    expect(store.result?.face_shape).toBe('square')
    expect(store.rescueOpen).toBe(false)
  })

  it('override updates result from API', async () => {
    httpMock.mockResolvedValueOnce({
      data: { ...mockResult, face_shape: 'oval', hair_type: 'straight' },
    })

    const store = useVisagismStore()
    store.result = mockResult
    store.step = VisagismStep.Results
    store.overrideOpen = true

    await store.override('north-shop', {
      face_shape: 'oval',
      hair_type: 'straight',
      feedback_reason: 'A IA errou o formato',
    })

    expect(store.result?.face_shape).toBe('oval')
    expect(store.overrideOpen).toBe(false)
  })

  it('builds booking query from suggestion and selected product', async () => {
    httpMock.mockResolvedValueOnce({ data: mockResult }).mockResolvedValueOnce({ data: emptyExisting })

    const store = useVisagismStore()
    await store.analyze('north-shop')
    store.toggleProduct(1)

    expect(store.bookingQuery()).toEqual({ service: '8', product: '12' })
  })
})
