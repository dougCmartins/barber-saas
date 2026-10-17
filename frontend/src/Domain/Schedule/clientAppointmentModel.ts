import type { AppointmentStatus } from '@/types/appointmentStatus'

export interface ClientAppointment {
  id: number
  starts_at: string
  ends_at: string
  professional_id: number
  professional_name: string
  professional_avatar_url: string | null
  service_names: string[]
  price: number
  status: AppointmentStatus
  notes: string | null
}

export interface ClientAppointmentFilters {
  from?: string
  to?: string
  statuses: AppointmentStatus[]
  professionalId: number | null
}

export const emptyClientAppointmentFilters: ClientAppointmentFilters = {
  statuses: [],
  professionalId: null,
}
