import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import type { Barbershop } from '@/Domain/Tenant/barbershopModel'
import { usePublicTenantStore } from '@/Domain/Tenant/publicTenantStore'
import { http } from '@/http/client'
import { ApiErrorCode } from '@/types/apiErrorCode'

vi.mock('@/http/client', () => ({
  http: vi.fn<(path: string, init?: RequestInit) => Promise<{ data: unknown }>>(),
}))

const httpMock = vi.mocked(http)

const shop: Barbershop = {
  id: 1,
  name: 'North Shop',
  slug: 'north-shop',
  active: true,
  address: 'Rua Harmonia, 42',
  city: 'Sao Paulo',
  closing_time: '20:00',
  rating: 4.8,
  total_reviews: 12,
  banner_url: null,
}

describe('publicTenantStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    httpMock.mockReset()
  })

  it('stores the barbershop from response data', async () => {
    httpMock.mockResolvedValue({ data: shop })

    const store = usePublicTenantStore()
    await store.fetchBarbershop('north-shop')

    expect(httpMock).toHaveBeenCalledWith('/barbershops/north-shop')
    expect(store.barbershop).toEqual(shop)
    expect(store.notFound).toBe(false)
    expect(store.loading).toBe(false)
  })

  it('marks the shop as not found when the api returns that code', async () => {
    httpMock.mockRejectedValue({ code: ApiErrorCode.BarbershopNotFound })

    const store = usePublicTenantStore()
    await store.fetchBarbershop('missing-shop')

    expect(store.barbershop).toBeNull()
    expect(store.notFound).toBe(true)
    expect(store.loading).toBe(false)
  })
})
