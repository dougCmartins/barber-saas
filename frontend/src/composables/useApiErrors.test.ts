import { createApp, defineComponent } from 'vue'
import { describe, expect, it } from 'vitest'
import { hasErrorCode, useApiErrors } from '@/composables/useApiErrors'
import { i18n } from '@/i18n'
import { ApiErrorCode } from '@/types/apiErrorCode'

function setupErrors() {
  let api!: ReturnType<typeof useApiErrors>
  const app = createApp(
    defineComponent({
      setup() {
        api = useApiErrors()
        return () => null
      },
    }),
  )
  app.use(i18n)
  app.mount(document.createElement('div'))

  return api
}

describe('useApiErrors', () => {
  it('maps the first string of each field and falls back to the envelope message', () => {
    const { readFieldErrors, readMessage } = setupErrors()

    const fieldErrors = readFieldErrors(
      { errors: { email: ['email'], password: [123] } },
      ['email', 'password'],
    )

    expect(fieldErrors.email).toBe('Indique um email válido.')
    expect(fieldErrors.password).toBeUndefined()
    expect(readMessage({ code: ApiErrorCode.InvalidCredentials })).toBe('Credenciais inválidas.')
  })

  it('recognizes a known envelope code', () => {
    expect(hasErrorCode({ code: ApiErrorCode.SlotUnavailable }, ApiErrorCode.SlotUnavailable)).toBe(true)
    expect(hasErrorCode({ code: 'OTHER' }, ApiErrorCode.SlotUnavailable)).toBe(false)
    expect(hasErrorCode(null, ApiErrorCode.SlotUnavailable)).toBe(false)
  })
})
