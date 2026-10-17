import { describe, expect, it } from 'vitest'
import { apiErrorMessage, apiFieldMessage } from '@/http/apiError'
import { ptBR } from '@/i18n/pt-BR'
import { ApiErrorCode } from '@/types/apiErrorCode'

function translate(key: string): string {
  const [group, name] = key.split('.')
  const section = (ptBR as Record<string, Record<string, string>>)[group ?? '']

  return section?.[name ?? ''] ?? key
}

describe('apiError', () => {
  it('translates a domain code instead of the english message', () => {
    const message = apiErrorMessage(
      { code: ApiErrorCode.InvalidCredentials, message: 'Invalid credentials.' },
      translate,
    )

    expect(message).toBe('Credenciais inválidas.')
  })

  it('translates a validation rule instead of the english sentence', () => {
    expect(apiFieldMessage('email', 'email', translate)).toBe('Indique um email válido.')
    expect(apiFieldMessage('shop_slug', 'unique', translate)).toBe('Este valor já está em uso.')
    expect(apiFieldMessage('password', 'min', translate)).toBe('A senha precisa de pelo menos 8 caracteres.')
  })

  it('falls back to portuguese when the code is unknown', () => {
    expect(apiErrorMessage({ message: 'Something exploded.' }, translate)).toBe(
      'Não foi possível concluir o pedido.',
    )
    expect(apiFieldMessage('email', 'The email field must be a valid email address.', translate)).toBe(
      'Não foi possível validar este campo.',
    )
  })
})
