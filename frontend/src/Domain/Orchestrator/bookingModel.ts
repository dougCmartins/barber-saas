import type { AppointmentStatus } from '@/types/appointmentStatus'

export interface AvailableSlot {
  starts_at: string
}

export interface BookingSelection {
  professional_id: number
  service_ids: number[]
  starts_at: string
}

export interface Appointment {
  id: number
  barbershop_id: number
  professional_id: number
  service_id: number
  client_name: string
  client_email: string
  starts_at: string
  ends_at: string
  price: number
  status: AppointmentStatus
}
