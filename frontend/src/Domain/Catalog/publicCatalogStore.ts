import { ref } from 'vue'
import { defineStore } from 'pinia'
import { hasErrorCode } from '@/composables/useApiErrors'
import type { Professional } from '@/Domain/Catalog/professionalModel'
import type { Service } from '@/Domain/Catalog/serviceModel'
import { http } from '@/http/client'
import { ApiErrorCode } from '@/types/apiErrorCode'

export const usePublicCatalogStore = defineStore('publicCatalog', () => {
  const services = ref<Service[]>([])
  const professionals = ref<Professional[]>([])
  const loading = ref(false)
  const notFound = ref(false)
  let requestId = 0

  async function fetchServices(slug: string): Promise<void> {
    const current = ++requestId
    loading.value = true
    notFound.value = false

    try {
      const response = await http<Service[]>(`/barbershops/${encodeURIComponent(slug)}/services`)

      if (current !== requestId) {
        return
      }

      services.value = response.data
    } catch (error) {
      if (current !== requestId) {
        return
      }

      services.value = []

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

  async function fetchPublicCatalog(slug: string): Promise<void> {
    const current = ++requestId
    loading.value = true
    notFound.value = false

    try {
      const [serviceResponse, professionalResponse] = await Promise.all([
        http<Service[]>(`/barbershops/${encodeURIComponent(slug)}/services`),
        http<Professional[]>(`/barbershops/${encodeURIComponent(slug)}/professionals`),
      ])

      if (current !== requestId) {
        return
      }

      services.value = serviceResponse.data
      professionals.value = professionalResponse.data
    } catch (error) {
      if (current !== requestId) {
        return
      }

      services.value = []
      professionals.value = []

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

  return { services, professionals, loading, notFound, fetchServices, fetchPublicCatalog }
})

function isBarbershopNotFound(error: unknown): boolean {
  return hasErrorCode(error, ApiErrorCode.BarbershopNotFound)
}
