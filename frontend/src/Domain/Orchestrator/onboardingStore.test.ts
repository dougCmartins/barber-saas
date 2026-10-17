import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import type { OnboardingRequest, OnboardingResult } from '@/Domain/Orchestrator/onboardingModel'
import { useOnboardingStore } from '@/Domain/Orchestrator/onboardingStore'
import { http } from '@/http/client'
import { UserRole } from '@/types/userRole'

vi.mock('@/http/client', () => ({
  http: vi.fn<(path: string, init?: RequestInit) => Promise<{ data: unknown }>>(),
}))

const httpMock = vi.mocked(http)

const request: OnboardingRequest = {
  shop_name: 'North Shop',
  shop_slug: 'north-shop',
  owner_name: 'Ada',
  owner_email: 'ada@example.com',
  owner_password: 'password1',
}

const result: OnboardingResult = {
  shop: {
    id: 1,
    name: 'North Shop',
    slug: 'north-shop',
    active: true,
    address: null,
    city: null,
    closing_time: null,
    rating: 5,
    total_reviews: 0,
    banner_url: null,
  },
  owner: {
    id: 2,
    barbershop_id: 1,
    name: 'Ada',
    email: 'ada@example.com',
    role: UserRole.Owner,
  },
}

describe('onboardingStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    httpMock.mockReset()
  })

  it('returns the shop and owner from response data', async () => {
    httpMock.mockResolvedValue({ data: result })

    const store = useOnboardingStore()
    const created = await store.submit(request)

    expect(httpMock).toHaveBeenCalledWith('/onboarding', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(request),
    })
    expect(created).toEqual(result)
  })
})
