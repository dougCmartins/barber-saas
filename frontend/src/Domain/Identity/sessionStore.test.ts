import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useSessionStore } from '@/Domain/Identity/sessionStore'
import type { Token } from '@/Domain/Identity/tokenModel'
import { http } from '@/http/client'

const token: Token = {
  access_token: 'abc',
  expires_in: 3600,
  token_type: 'Bearer',
}

describe('sessionStore', () => {
  beforeEach(() => {
    localStorage.clear()
    setActivePinia(createPinia())
    vi.unstubAllGlobals()
  })

  it('stores the token and clears the session', () => {
    const store = useSessionStore()

    store.setToken(token)

    expect(store.token).toEqual(token)
    expect(localStorage.getItem('barber.session')).toBe(JSON.stringify(token))

    setActivePinia(createPinia())
    const restored = useSessionStore()

    expect(restored.token).toEqual(token)

    restored.clear()

    expect(restored.token).toBeNull()
    expect(localStorage.getItem('barber.session')).toBeNull()
  })

  it('sends the bearer token and returns the response body as data', async () => {
    const store = useSessionStore()
    store.setToken(token)

    let authorization: string | null = null
    const fetchMock = vi.fn<(url: string, init?: RequestInit) => Promise<Response>>(async (_url, init) => {
      authorization = new Headers(init?.headers).get('Authorization')

      return new Response(JSON.stringify({ access_token: 'abc' }), {
        status: 200,
        headers: { 'Content-Type': 'application/json' },
      })
    })
    vi.stubGlobal('fetch', fetchMock)

    const response = await http<{ access_token: string }>('/login')

    expect(response.data.access_token).toBe('abc')
    expect(fetchMock).toHaveBeenCalledWith(
      'http://localhost:8086/api/login',
      expect.objectContaining({
        headers: expect.any(Headers),
      }),
    )
    expect(authorization).toBe('Bearer abc')
  })
})
