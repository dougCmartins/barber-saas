import { ref } from 'vue'
import { defineStore } from 'pinia'
import type { ListedAppointment } from '@/Domain/Schedule/scheduleModel'
import { http } from '@/http/client'

export const usePrivateScheduleStore = defineStore('privateSchedule', () => {
  const appointments = ref<ListedAppointment[]>([])
  const loading = ref(false)
  let requestId = 0

  async function fetchAppointments(date: string): Promise<void> {
    const current = ++requestId
    loading.value = true

    try {
      const query = new URLSearchParams({ date })
      const response = await http<ListedAppointment[]>(`/appointments?${query}`)

      if (current !== requestId) {
        return
      }

      appointments.value = response.data
    } catch (error) {
      if (current !== requestId) {
        return
      }

      appointments.value = []
      throw error
    } finally {
      if (current === requestId) {
        loading.value = false
      }
    }
  }

  async function cancelAppointment(id: number): Promise<void> {
    const response = await http<ListedAppointment>(`/appointments/${id}/cancel`, { method: 'PATCH' })
    replace(response.data)
  }

  async function completeAppointment(id: number): Promise<void> {
    const response = await http<ListedAppointment>(`/appointments/${id}/complete`, { method: 'PATCH' })
    replace(response.data)
  }

  function replace(appointment: ListedAppointment): void {
    appointments.value = appointments.value.map((item) =>
      item.id === appointment.id ? appointment : item,
    )
  }

  return { appointments, loading, fetchAppointments, cancelAppointment, completeAppointment }
})
