import { useSessionStore } from '@/Domain/Identity/sessionStore'

export async function http<T>(path: string, init: RequestInit = {}): Promise<{ data: T }> {
  const session = useSessionStore()
  const headers = new Headers(init.headers)
  headers.set('Accept', 'application/json')

  if (session.token) {
    headers.set('Authorization', `${session.token.token_type} ${session.token.access_token}`)
  }

  const response = await fetch(`${import.meta.env.VITE_API_URL}${path}`, {
    ...init,
    headers,
  })
  const body: unknown = await response.json()

  if (!response.ok) {
    throw body
  }

  return { data: body as T }
}
