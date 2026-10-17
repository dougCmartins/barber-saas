import { useI18n } from 'vue-i18n'
import { apiErrorMessage, apiFieldMessage } from '@/http/apiError'
import type { ApiErrorCode } from '@/types/apiErrorCode'

export function useApiErrors() {
  const { t } = useI18n()

  function readFieldErrors<T extends string>(
    caught: unknown,
    fields: readonly T[],
  ): Partial<Record<T, string>> {
    if (!isRecord(caught) || !isRecord(caught.errors)) {
      return {}
    }

    const fieldErrors: Partial<Record<T, string>> = {}

    for (const field of fields) {
      const value = caught.errors[field]
      if (Array.isArray(value) && typeof value[0] === 'string') {
        fieldErrors[field] = apiFieldMessage(field, value[0], t)
      }
    }

    return fieldErrors
  }

  function readMessage(caught: unknown): string {
    return apiErrorMessage(caught, t)
  }

  return { readFieldErrors, readMessage, hasErrorCode }
}

export function hasErrorCode(caught: unknown, code: ApiErrorCode): boolean {
  return isRecord(caught) && caught.code === code
}

function isRecord(value: unknown): value is Record<string, unknown> {
  return typeof value === 'object' && value !== null && !Array.isArray(value)
}
