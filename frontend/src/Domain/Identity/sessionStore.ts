import { ref } from 'vue'
import { defineStore } from 'pinia'
import type { Token } from '@/Domain/Identity/tokenModel'

const storageKey = 'barber.session'

export const useSessionStore = defineStore('session', () => {
  const token = ref<Token | null>(readToken())

  function setToken(value: Token): void {
    token.value = value
    localStorage.setItem(storageKey, JSON.stringify(value))
  }

  function clear(): void {
    token.value = null
    localStorage.removeItem(storageKey)
  }

  return { token, setToken, clear }
})

function readToken(): Token | null {
  const raw = localStorage.getItem(storageKey)

  if (!raw) {
    return null
  }

  try {
    return JSON.parse(raw) as Token
  } catch {
    return null
  }
}
