import { ref } from 'vue'
import { defineStore } from 'pinia'
import { hasErrorCode } from '@/composables/useApiErrors'
import type { Barbershop } from '@/Domain/Tenant/barbershopModel'
import { http } from '@/http/client'
import { ApiErrorCode } from '@/types/apiErrorCode'

export const usePublicTenantStore = defineStore('publicTenant', () => {
  const barbershop = ref<Barbershop | null>(null)
  const loading = ref(false)
  const notFound = ref(false)
  let requestId = 0

  async function fetchBarbershop(slug: string): Promise<void> {
    const current = ++requestId
    loading.value = true
    notFound.value = false

    try {
      const response = await http<Barbershop>(`/barbershops/${encodeURIComponent(slug)}`)

      if (current !== requestId) {
        return
      }

      barbershop.value = response.data
    } catch (error) {
      if (current !== requestId) {
        return
      }

      barbershop.value = null

      if (isBarbershopNotFound(error)) {
        notFound.value = true
        return
      }

      throw error
    } finally {
      if (current === requestId) {
        loading.value = false
      }
    }
  }

  return { barbershop, loading, notFound, fetchBarbershop }
})

function isBarbershopNotFound(error: unknown): boolean {
  return hasErrorCode(error, ApiErrorCode.BarbershopNotFound)
}
