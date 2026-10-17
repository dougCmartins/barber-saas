export const ApiErrorCode = {
  InvalidCredentials: 'INVALID_CREDENTIALS',
  SlotUnavailable: 'SLOT_UNAVAILABLE',
  AppointmentNotFound: 'APPOINTMENT_NOT_FOUND',
  AppointmentClosed: 'APPOINTMENT_CLOSED',
  BarbershopNotFound: 'BARBERSHOP_NOT_FOUND',
  BookableOfferNotFound: 'BOOKABLE_OFFER_NOT_FOUND',
  VisagismLimitExceeded: 'VISAGISM_LIMIT_EXCEEDED',
  VisagismAnalysisNotFound: 'VISAGISM_ANALYSIS_NOT_FOUND',
} as const

export type ApiErrorCode = (typeof ApiErrorCode)[keyof typeof ApiErrorCode]
