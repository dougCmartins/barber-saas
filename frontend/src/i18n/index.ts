import { createI18n } from 'vue-i18n'
import { ptBR } from '@/i18n/pt-BR'

export const i18n = createI18n({
  legacy: false,
  locale: 'pt-BR',
  fallbackLocale: 'pt-BR',
  messages: {
    'pt-BR': ptBR,
  },
})
