import { ref } from 'vue'
import { defineStore } from 'pinia'
import type {
  ClientAppointment,
  ClientAppointmentFilters,
} from '@/Domain/Schedule/clientAppointmentModel'
import { emptyClientAppointmentFilters } from '@/Domain/Schedule/clientAppointmentModel'
import { http } from '@/http/client'

export const useClientScheduleStore = defineStore('clientSchedule', () => {
  const items = ref<ClientAppointment[]>([])
  const loading = ref(false)
  const filters = ref<ClientAppointmentFilters>({ ...emptyClientAppointmentFilters })
  let requestId = 0

  async function fetchAppointments(next?: ClientAppointmentFilters): Promise<void> {
    if (next) {
      filters.value = {
        from: next.from,
        to: next.to,
        statuses: [...next.statuses],
        professionalId: next.professionalId,
      }
    }

    const current = ++requestId
    loading.value = true

    const query = new URLSearchParams()

    if (filters.value.from) {
      query.set('from', filters.value.from)
    }

    if (filters.value.to) {
      query.set('to', filters.value.to)
    }

    for (const status of filters.value.statuses) {
      query.append('statuses[]', status)
    }

    if (filters.value.professionalId !== null) {
      query.set('professional_id', String(filters.value.professionalId))
    }

    const suffix = query.toString() ? `?${query}` : ''

    try {
      const response = await http<ClientAppointment[]>(`/client/appointments${suffix}`)

      if (current !== requestId) {
        return
      }

      items.value = response.data
    } catch (error) {
      if (current !== requestId) {
        return
      }

      items.value = []
      throw error
    } finally {
      if (current === requestId) {
        loading.value = false
      }
    }
  }

  async function cancelAppointment(id: number): Promise<void> {
    const response = await http<ClientAppointment>(`/client/appointments/${id}/cancel`, {
      method: 'PATCH',
    })

    items.value = items.value.map((item) => (item.id === id ? response.data : item))
  }

  function resetFilters(): void {
    filters.value = { ...emptyClientAppointmentFilters, statuses: [] }
  }

  return {
    items,
    loading,
    filters,
    fetchAppointments,
    cancelAppointment,
    resetFilters,
  }
})
