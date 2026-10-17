import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { usePublicCatalogStore } from '@/Domain/Catalog/publicCatalogStore'
import type { Professional } from '@/Domain/Catalog/professionalModel'
import type { Service } from '@/Domain/Catalog/serviceModel'
import { http } from '@/http/client'
import { ApiErrorCode } from '@/types/apiErrorCode'

vi.mock('@/http/client', () => ({
  http: vi.fn<(path: string, init?: RequestInit) => Promise<{ data: unknown }>>(),
}))

const httpMock = vi.mocked(http)

const services: Service[] = [
  {
    id: 1,
    barbershop_id: 1,
    name: 'Haircut',
    description: null,
    price: 40,
    duration_in_minutes: 30,
    active: true,
  },
]

const professionals: Professional[] = [
  {
    id: 4,
    barbershop_id: 1,
    user_id: 2,
    name: 'Diego',
    specialty: null,
    avatar_url: null,
    experience_years: null,
    active: true,
  },
]

describe('publicCatalogStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    httpMock.mockReset()
  })

  it('stores the service list from response data', async () => {
    httpMock.mockResolvedValue({ data: services })

    const store = usePublicCatalogStore()
    await store.fetchServices('north-shop')

    expect(httpMock).toHaveBeenCalledWith('/barbershops/north-shop/services')
    expect(store.services).toEqual(services)
    expect(store.notFound).toBe(false)
    expect(store.loading).toBe(false)
  })

  it('marks the catalog as not found when the api returns that code', async () => {
    httpMock.mockRejectedValue({ code: ApiErrorCode.BarbershopNotFound })

    const store = usePublicCatalogStore()
    await store.fetchServices('missing-shop')

    expect(store.services).toEqual([])
    expect(store.notFound).toBe(true)
    expect(store.loading).toBe(false)
  })

  it('stores services and professionals from the public catalog', async () => {
    httpMock.mockImplementation((path: string) => {
      if (path.endsWith('/professionals')) {
        return Promise.resolve({ data: professionals })
      }

      return Promise.resolve({ data: services })
    })

    const store = usePublicCatalogStore()
    await store.fetchPublicCatalog('north-shop')

    expect(httpMock).toHaveBeenCalledWith('/barbershops/north-shop/services')
    expect(httpMock).toHaveBeenCalledWith('/barbershops/north-shop/professionals')
    expect(store.services).toEqual(services)
    expect(store.professionals).toEqual(professionals)
    expect(store.loading).toBe(false)
  })
})
