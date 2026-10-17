import { defineStore } from 'pinia'
import type { OnboardingRequest, OnboardingResult } from '@/Domain/Orchestrator/onboardingModel'
import { http } from '@/http/client'

export const useOnboardingStore = defineStore('onboarding', () => {
  async function submit(data: OnboardingRequest): Promise<OnboardingResult> {
    const response = await http<OnboardingResult>('/onboarding', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    })

    return response.data
  }

  return { submit }
})
