import { ref } from 'vue'
import { defineStore } from 'pinia'
import { hasErrorCode } from '@/composables/useApiErrors'
import { useClientAuthStore } from '@/Domain/Identity/clientAuthStore'
import type { Appointment, AvailableSlot, BookingSelection } from '@/Domain/Orchestrator/bookingModel'
import { http } from '@/http/client'
import { ApiErrorCode } from '@/types/apiErrorCode'

export const usePublicBookingStore = defineStore('publicBooking', () => {
  const availableSlots = ref<AvailableSlot[]>([])
  const slotsLoading = ref(false)
  const slotsError = ref(false)
  const slotUnavailable = ref(false)
  const isLoading = ref(false)
  let requestId = 0

  async function book(slug: string, selection: BookingSelection): Promise<Appointment | null> {
    slotUnavailable.value = false
    isLoading.value = true

    const client = useClientAuthStore().user

    try {
      const response = await http<Appointment>(`/barbershops/${encodeURIComponent(slug)}/appointments`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          professional_id: selection.professional_id,
          service_ids: selection.service_ids,
          starts_at: selection.starts_at,
          client_name: client?.first_name ?? 'Cliente Logado',
          client_email: client?.email ?? 'cliente@exemplo.com',
        }),
      })

      return response.data
    } catch (error) {
      if (!isSlotUnavailable(error)) {
        throw error
      }

      slotUnavailable.value = true
      return null
    } finally {
      isLoading.value = false
    }
  }

  async function fetchAvailableSlots(
    slug: string,
    professionalId: number,
    date: string,
    serviceIds: number[],
  ): Promise<AvailableSlot[]> {
    const current = ++requestId
    slotsLoading.value = true
    slotsError.value = false
    availableSlots.value = []

    const query = new URLSearchParams({ date })

    for (const serviceId of serviceIds) {
      query.append('service_ids[]', String(serviceId))
    }

    try {
      const response = await http<AvailableSlot[]>(
        `/barbershops/${encodeURIComponent(slug)}/professionals/${professionalId}/slots?${query}`,
      )

      if (current !== requestId) {
        return response.data
      }

      availableSlots.value = response.data
      return response.data
    } catch (error) {
      if (current === requestId) {
        availableSlots.value = []
        slotsError.value = true
      }

      throw error
    } finally {
      if (current === requestId) {
        slotsLoading.value = false
      }
    }
  }

  return {
    availableSlots,
    slotsLoading,
    slotsError,
    slotUnavailable,
    isLoading,
    book,
    fetchAvailableSlots,
  }
})

function isSlotUnavailable(error: unknown): boolean {
  return hasErrorCode(error, ApiErrorCode.SlotUnavailable)
}
