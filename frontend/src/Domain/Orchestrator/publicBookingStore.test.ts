import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import type { Appointment, AvailableSlot, BookingSelection } from '@/Domain/Orchestrator/bookingModel'
import { usePublicBookingStore } from '@/Domain/Orchestrator/publicBookingStore'
import { http } from '@/http/client'
import { ApiErrorCode } from '@/types/apiErrorCode'
import { AppointmentStatus } from '@/types/appointmentStatus'

vi.mock('@/http/client', () => ({
  http: vi.fn<(path: string, init?: RequestInit) => Promise<{ data: unknown }>>(),
}))

vi.mock('@/Domain/Identity/clientAuthStore', () => ({
  useClientAuthStore: () => ({ user: null }),
}))

const httpMock = vi.mocked(http)

const selection: BookingSelection = {
  professional_id: 4,
  service_ids: [8, 9],
  starts_at: '2026-10-10 10:00:00',
}

const appointment: Appointment = {
  id: 3,
  barbershop_id: 1,
  professional_id: 4,
  service_id: 8,
  client_name: 'Ana',
  client_email: 'ana@example.com',
  starts_at: '2026-10-10 10:00:00',
  ends_at: '2026-10-10 10:30:00',
  price: 45.5,
  status: AppointmentStatus.Pending,
}

describe('publicBookingStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    httpMock.mockReset()
  })

  it('posts the selection with the fixed client and clears the loading flag', async () => {
    let release: (value: { data: Appointment }) => void = () => {}
    httpMock.mockImplementation(
      () =>
        new Promise((resolve) => {
          release = resolve
        }),
    )

    const store = usePublicBookingStore()
    const pending = store.book('north-shop', selection)

    expect(store.isLoading).toBe(true)

    release({ data: appointment })
    const created = await pending

    expect(httpMock).toHaveBeenCalledWith('/barbershops/north-shop/appointments', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        ...selection,
        client_name: 'Cliente Logado',
        client_email: 'cliente@exemplo.com',
      }),
    })
    expect(created).toEqual(appointment)
    expect(store.slotUnavailable).toBe(false)
    expect(store.isLoading).toBe(false)
  })

  it('marks the slot unavailable and returns no appointment when the slot is taken', async () => {
    httpMock.mockRejectedValue({
      code: ApiErrorCode.SlotUnavailable,
      message: 'This time slot is unavailable.',
    })

    const store = usePublicBookingStore()
    const created = await store.book('north-shop', selection)

    expect(created).toBeNull()
    expect(store.slotUnavailable).toBe(true)
    expect(store.isLoading).toBe(false)
  })

  it('returns the slot list from response data', async () => {
    const slots: AvailableSlot[] = [
      { starts_at: '2026-10-15 09:00:00' },
      { starts_at: '2026-10-15 09:30:00' },
    ]
    httpMock.mockResolvedValue({ data: slots })

    const store = usePublicBookingStore()
    const available = await store.fetchAvailableSlots('north-shop', 4, '2026-10-15', [8, 9])

    expect(httpMock).toHaveBeenCalledWith(
      '/barbershops/north-shop/professionals/4/slots?date=2026-10-15&service_ids%5B%5D=8&service_ids%5B%5D=9',
    )
    expect(available).toEqual(slots)
    expect(store.availableSlots).toEqual(slots)
  })
})
