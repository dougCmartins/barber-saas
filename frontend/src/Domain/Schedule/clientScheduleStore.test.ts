import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import type { ClientAppointment } from '@/Domain/Schedule/clientAppointmentModel'
import { useClientScheduleStore } from '@/Domain/Schedule/clientScheduleStore'
import { http } from '@/http/client'
import { AppointmentStatus } from '@/types/appointmentStatus'

vi.mock('@/http/client', () => ({
  http: vi.fn<(path: string, init?: RequestInit) => Promise<{ data: unknown }>>(),
}))

const httpMock = vi.mocked(http)

const appointment: ClientAppointment = {
  id: 3,
  starts_at: '2026-10-12 10:00:00',
  ends_at: '2026-10-12 10:30:00',
  professional_id: 4,
  professional_name: 'Diego',
  professional_avatar_url: null,
  service_names: ['Haircut'],
  price: 45.5,
  status: AppointmentStatus.Pending,
  notes: null,
}

describe('clientScheduleStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    httpMock.mockReset()
  })

  it('fetches appointments with filter query params', async () => {
    httpMock.mockResolvedValue({ data: [appointment] })

    const store = useClientScheduleStore()
    await store.fetchAppointments({
      from: '2026-10-01',
      to: '2026-10-15',
      statuses: [AppointmentStatus.Pending],
      professionalId: 4,
    })

    expect(httpMock).toHaveBeenCalledWith(
      '/client/appointments?from=2026-10-01&to=2026-10-15&statuses%5B%5D=pending&professional_id=4',
    )
    expect(store.items).toEqual([appointment])
    expect(store.loading).toBe(false)
  })

  it('cancels an appointment through the client endpoint', async () => {
    httpMock
      .mockResolvedValueOnce({ data: [appointment] })
      .mockResolvedValueOnce({
        data: { ...appointment, status: AppointmentStatus.Canceled },
      })

    const store = useClientScheduleStore()
    await store.fetchAppointments()
    await store.cancelAppointment(3)

    expect(httpMock).toHaveBeenLastCalledWith('/client/appointments/3/cancel', {
      method: 'PATCH',
    })
    expect(store.items[0]?.status).toBe(AppointmentStatus.Canceled)
  })

  it('clears items when the list request fails', async () => {
    httpMock.mockRejectedValue({ code: 'UNAUTHORIZED' })

    const store = useClientScheduleStore()

    await expect(store.fetchAppointments()).rejects.toBeTruthy()
    expect(store.items).toEqual([])
  })
})
