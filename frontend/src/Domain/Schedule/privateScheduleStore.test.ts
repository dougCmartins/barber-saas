import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import type { ListedAppointment } from '@/Domain/Schedule/scheduleModel'
import { usePrivateScheduleStore } from '@/Domain/Schedule/privateScheduleStore'
import { http } from '@/http/client'
import { AppointmentStatus } from '@/types/appointmentStatus'

vi.mock('@/http/client', () => ({
  http: vi.fn<(path: string, init?: RequestInit) => Promise<{ data: unknown }>>(),
}))

const httpMock = vi.mocked(http)

const appointments: ListedAppointment[] = [
  {
    id: 3,
    starts_at: '2026-10-15 10:00:00',
    ends_at: '2026-10-15 10:30:00',
    client_name: 'Ana',
    service_name: 'Haircut',
    professional_name: 'Diego',
    price: 45.5,
    status: AppointmentStatus.Pending,
  },
]

describe('privateScheduleStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    httpMock.mockReset()
  })

  it('loads the appointment list from response data', async () => {
    httpMock.mockResolvedValue({ data: appointments })

    const store = usePrivateScheduleStore()
    await store.fetchAppointments('2026-10-15')

    expect(httpMock).toHaveBeenCalledWith('/appointments?date=2026-10-15')
    expect(store.appointments).toEqual(appointments)
    expect(store.loading).toBe(false)
  })

  it('replaces the local status after a complete request', async () => {
    httpMock.mockResolvedValueOnce({ data: appointments })
    httpMock.mockResolvedValueOnce({ data: { ...appointments[0], status: AppointmentStatus.Completed } })

    const store = usePrivateScheduleStore()
    await store.fetchAppointments('2026-10-15')
    await store.completeAppointment(3)

    expect(httpMock).toHaveBeenLastCalledWith('/appointments/3/complete', { method: 'PATCH' })
    expect(store.appointments[0]?.status).toBe(AppointmentStatus.Completed)
  })

  it('replaces the local status after a cancel request', async () => {
    httpMock.mockResolvedValueOnce({ data: appointments })
    httpMock.mockResolvedValueOnce({ data: { ...appointments[0], status: AppointmentStatus.Canceled } })

    const store = usePrivateScheduleStore()
    await store.fetchAppointments('2026-10-15')
    await store.cancelAppointment(3)

    expect(httpMock).toHaveBeenLastCalledWith('/appointments/3/cancel', { method: 'PATCH' })
    expect(store.appointments[0]?.status).toBe(AppointmentStatus.Canceled)
  })
})
