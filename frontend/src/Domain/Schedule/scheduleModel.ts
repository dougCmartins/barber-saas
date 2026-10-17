import type { AppointmentStatus } from '@/types/appointmentStatus'

export type { AppointmentStatus }

export interface ListedAppointment {
  id: number
  starts_at: string
  ends_at: string
  client_name: string
  service_name: string
  professional_name: string
  price: number
  status: AppointmentStatus
}
