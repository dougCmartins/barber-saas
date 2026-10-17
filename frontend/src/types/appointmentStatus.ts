export const AppointmentStatus = {
  Pending: 'pending',
  Confirmed: 'confirmed',
  Canceled: 'canceled',
  Completed: 'completed',
  Missed: 'missed',
} as const

export type AppointmentStatus = (typeof AppointmentStatus)[keyof typeof AppointmentStatus]

export const clientAppointmentStatuses = [
  AppointmentStatus.Pending,
  AppointmentStatus.Confirmed,
  AppointmentStatus.Completed,
  AppointmentStatus.Canceled,
] as const

export type ClientAppointmentStatus = (typeof clientAppointmentStatuses)[number]
