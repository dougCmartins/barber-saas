import { ref } from 'vue'
import { defineStore } from 'pinia'
import avatarUrl from '@/assets/barbershop-hero.jpg'
import { useSessionStore } from '@/Domain/Identity/sessionStore'
import { http } from '@/http/client'

export interface ClientUser {
  id: number
  first_name: string
  email: string
  avatar_url: string
}

interface MeResponse {
  id: number
  name: string
  email: string
  barbershop_id: number | null
  role: string
}

export const useClientAuthStore = defineStore('clientAuth', () => {
  const user = ref<ClientUser | null>(null)
  const loading = ref(false)

  async function fetchMe(): Promise<void> {
    const session = useSessionStore()

    if (!session.token) {
      user.value = null
      return
    }

    loading.value = true

    try {
      const response = await http<MeResponse>('/me')
      const [firstName] = response.data.name.trim().split(/\s+/)

      user.value = {
        id: response.data.id,
        first_name: firstName || response.data.name,
        email: response.data.email,
        avatar_url: avatarUrl,
      }
    } catch {
      user.value = null
    } finally {
      loading.value = false
    }
  }

  function clear(): void {
    user.value = null
  }

  return { user, loading, fetchMe, clear }
})
