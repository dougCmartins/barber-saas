import { ApiErrorCode } from '@/types/apiErrorCode'

type ErrorCode = (typeof ApiErrorCode)[keyof typeof ApiErrorCode]

const messagesByCode: Record<ErrorCode, string> = {
  [ApiErrorCode.InvalidCredentials]: 'errors.invalidCredentials',
  [ApiErrorCode.SlotUnavailable]: 'errors.slotUnavailable',
  [ApiErrorCode.AppointmentNotFound]: 'errors.appointmentNotFound',
  [ApiErrorCode.AppointmentClosed]: 'errors.appointmentClosed',
  [ApiErrorCode.BarbershopNotFound]: 'errors.barbershopNotFound',
  [ApiErrorCode.BookableOfferNotFound]: 'errors.offerUnavailable',
  [ApiErrorCode.VisagismLimitExceeded]: 'visagism.limitExceeded',
  [ApiErrorCode.VisagismAnalysisNotFound]: 'visagism.analysisNotFound',
}

const messagesByRule: Record<string, string> = {
  required: 'auth.required',
  email: 'auth.invalidEmail',
  unique: 'errors.alreadyUsed',
  min: 'errors.tooShort',
  max: 'errors.tooLong',
  date: 'booking.invalidDate',
  dateformat: 'booking.invalidDate',
  regex: 'auth.invalidSlug',
}

type Translate = (key: string) => string

export function apiErrorMessage(error: unknown, translate: Translate): string {
  const code = readString(error, 'code')
  const key = code && isErrorCode(code) ? messagesByCode[code] : undefined

  return translate(key ?? 'auth.genericError')
}

export function apiFieldMessage(field: string, rule: string, translate: Translate): string {
  const normalized = rule.toLowerCase()

  if (normalized === 'min' && (field === 'password' || field === 'owner_password')) {
    return translate('auth.passwordMin')
  }

  return translate(messagesByRule[normalized] ?? 'errors.invalidField')
}

function isErrorCode(value: string): value is ErrorCode {
  return (Object.values(ApiErrorCode) as string[]).includes(value)
}

function readString(error: unknown, key: string): string | undefined {
  if (typeof error !== 'object' || error === null || Array.isArray(error)) {
    return undefined
  }

  const value = (error as Record<string, unknown>)[key]

  return typeof value === 'string' && value !== '' ? value : undefined
}
