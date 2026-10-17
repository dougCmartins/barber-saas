<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { useForm } from 'vee-validate'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { RouterLink, useRouter } from 'vue-router'
import { z } from 'zod'
import { useApiErrors } from '@/composables/useApiErrors'
import { useSessionStore } from '@/Domain/Identity/sessionStore'
import { useOnboardingStore } from '@/Domain/Orchestrator/onboardingStore'
import Button from '@/ui/Button.vue'
import { cn } from '@/ui/cn'

const router = useRouter()
const session = useSessionStore()
const onboarding = useOnboardingStore()
const { readFieldErrors, readMessage } = useApiErrors()
const { t } = useI18n()

const slugPattern = /^[a-z0-9]+(?:-[a-z0-9]+)*$/

const validationSchema = computed(() =>
  toTypedSchema(
    z.object({
      owner_name: z.string().min(1, t('auth.required')),
      owner_email: z.string().min(1, t('auth.required')).email(t('auth.invalidEmail')),
      owner_password: z.string().min(8, t('auth.passwordMin')),
      shop_name: z.string().min(1, t('auth.required')),
      shop_slug: z.string().min(1, t('auth.required')).regex(slugPattern, t('auth.invalidSlug')),
    }),
  ),
)

const { errors, defineField, handleSubmit, isSubmitting, setErrors } = useForm({
  validationSchema,
  initialValues: {
    owner_name: '',
    owner_email: '',
    owner_password: '',
    shop_name: '',
    shop_slug: '',
  },
})

const [ownerName, ownerNameAttrs] = defineField('owner_name')
const [ownerEmail, ownerEmailAttrs] = defineField('owner_email')
const [ownerPassword, ownerPasswordAttrs] = defineField('owner_password')
const [shopName, shopNameAttrs] = defineField('shop_name')
const [shopSlug, shopSlugAttrs] = defineField('shop_slug')

const fieldClass =
  'mt-2 h-10 w-full rounded-md border border-input bg-background px-3 text-sm text-foreground outline-none focus-visible:ring-1 focus-visible:ring-ring'

const onSubmit = handleSubmit(async (values) => {
  try {
    const result = await onboarding.submit(values)
    await session.login(values.owner_email, values.owner_password)
    await router.push(`/${result.shop.slug}`)
  } catch (caught) {
    applyApiErrors(caught)
  }
})

const fieldNames = ['owner_name', 'owner_email', 'owner_password', 'shop_name', 'shop_slug'] as const

function applyApiErrors(caught: unknown): void {
  const fieldErrors = readFieldErrors(caught, fieldNames)

  if (Object.keys(fieldErrors).length > 0) {
    setErrors(fieldErrors)
    return
  }

  setErrors({ owner_email: readMessage(caught) })
}
</script>

<template>
  <main class="grid min-h-screen place-items-center bg-background px-5 py-16 text-foreground">
    <form
      class="w-full max-w-md rounded-md border border-border bg-card p-8 shadow"
      novalidate
      @submit="onSubmit"
    >
      <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary">
        {{ t('auth.registerKicker') }}
      </p>
      <h1 class="mt-2 font-display text-4xl font-semibold uppercase">
        {{ t('auth.registerTitle') }}
      </h1>

      <label class="mt-8 block text-xs font-semibold uppercase tracking-[0.14em] text-muted-foreground">
        {{ t('auth.ownerName') }}
        <input
          v-model="ownerName"
          v-bind="ownerNameAttrs"
          type="text"
          autocomplete="name"
          :class="cn(fieldClass, errors.owner_name && 'border-red-500')"
        />
        <span v-if="errors.owner_name" class="mt-1 block text-sm text-red-500">{{ errors.owner_name }}</span>
      </label>

      <label class="mt-5 block text-xs font-semibold uppercase tracking-[0.14em] text-muted-foreground">
        {{ t('auth.email') }}
        <input
          v-model="ownerEmail"
          v-bind="ownerEmailAttrs"
          type="email"
          autocomplete="email"
          :class="cn(fieldClass, errors.owner_email && 'border-red-500')"
        />
        <span v-if="errors.owner_email" class="mt-1 block text-sm text-red-500">{{ errors.owner_email }}</span>
      </label>

      <label class="mt-5 block text-xs font-semibold uppercase tracking-[0.14em] text-muted-foreground">
        {{ t('auth.password') }}
        <input
          v-model="ownerPassword"
          v-bind="ownerPasswordAttrs"
          type="password"
          autocomplete="new-password"
          :class="cn(fieldClass, errors.owner_password && 'border-red-500')"
        />
        <span v-if="errors.owner_password" class="mt-1 block text-sm text-red-500">{{
          errors.owner_password
        }}</span>
      </label>

      <label class="mt-5 block text-xs font-semibold uppercase tracking-[0.14em] text-muted-foreground">
        {{ t('auth.shopName') }}
        <input
          v-model="shopName"
          v-bind="shopNameAttrs"
          type="text"
          :class="cn(fieldClass, errors.shop_name && 'border-red-500')"
        />
        <span v-if="errors.shop_name" class="mt-1 block text-sm text-red-500">{{ errors.shop_name }}</span>
      </label>

      <label class="mt-5 block text-xs font-semibold uppercase tracking-[0.14em] text-muted-foreground">
        {{ t('auth.shopSlug') }}
        <input
          v-model="shopSlug"
          v-bind="shopSlugAttrs"
          type="text"
          :class="cn(fieldClass, errors.shop_slug && 'border-red-500')"
        />
        <span v-if="errors.shop_slug" class="mt-1 block text-sm text-red-500">{{ errors.shop_slug }}</span>
      </label>

      <Button type="submit" size="lg" class="mt-6 w-full" :disabled="isSubmitting">
        {{ isSubmitting ? t('auth.submitting') : t('auth.submitRegister') }}
      </Button>

      <p class="mt-6 text-center text-sm text-muted-foreground">
        <RouterLink to="/login" class="text-primary hover:text-primary/80">
          {{ t('auth.alreadyHaveAccount') }}
        </RouterLink>
      </p>
    </form>
  </main>
</template>
