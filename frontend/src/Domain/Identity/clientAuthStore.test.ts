import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import avatarUrl from '@/assets/barbershop-hero.jpg'
import { useClientAuthStore } from '@/Domain/Identity/clientAuthStore'
import { useSessionStore } from '@/Domain/Identity/sessionStore'
import { http } from '@/http/client'

vi.mock('@/http/client', () => ({
  http: vi.fn<(path: string, init?: RequestInit) => Promise<{ data: unknown }>>(),
}))

const httpMock = vi.mocked(http)

describe('clientAuthStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    httpMock.mockReset()
    localStorage.clear()
  })

  it('starts without a user when there is no session', async () => {
    const store = useClientAuthStore()

    await store.fetchMe()

    expect(store.user).toBeNull()
    expect(httpMock).not.toHaveBeenCalled()
  })

  it('loads the authenticated profile from /me', async () => {
    const session = useSessionStore()
    session.setToken({
      access_token: 'token',
      expires_in: 3600,
      token_type: 'Bearer',
    })
    httpMock.mockResolvedValue({
      data: {
        id: 9,
        name: 'Ana Client',
        email: 'ana@example.com',
        barbershop_id: null,
        role: 'client',
      },
    })

    const store = useClientAuthStore()
    await store.fetchMe()

    expect(httpMock).toHaveBeenCalledWith('/me')
    expect(store.user).toEqual({
      id: 9,
      first_name: 'Ana',
      email: 'ana@example.com',
      avatar_url: avatarUrl,
    })
  })

  it('clears the user on demand', async () => {
    const session = useSessionStore()
    session.setToken({
      access_token: 'token',
      expires_in: 3600,
      token_type: 'Bearer',
    })
    httpMock.mockResolvedValue({
      data: {
        id: 9,
        name: 'Ana',
        email: 'ana@example.com',
        barbershop_id: null,
        role: 'client',
      },
    })

    const store = useClientAuthStore()
    await store.fetchMe()
    store.clear()

    expect(store.user).toBeNull()
  })
})
