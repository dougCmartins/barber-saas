<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { useForm } from 'vee-validate'
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { z } from 'zod'
import { useApiErrors } from '@/composables/useApiErrors'
import { useSessionStore } from '@/Domain/Identity/sessionStore'
import Button from '@/ui/Button.vue'
import { cn } from '@/ui/cn'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const session = useSessionStore()
const { readFieldErrors, readMessage } = useApiErrors()
const signedIn = ref(false)

const validationSchema = computed(() =>
  toTypedSchema(
    z.object({
      email: z.string().min(1, t('auth.required')).email(t('auth.invalidEmail')),
      password: z.string().min(1, t('auth.required')),
    }),
  ),
)

const { errors, defineField, handleSubmit, isSubmitting, setErrors } = useForm({
  validationSchema,
  initialValues: {
    email: '',
    password: '',
  },
})

const [email, emailAttrs] = defineField('email')
const [password, passwordAttrs] = defineField('password')

const fieldClass =
  'mt-2 h-10 w-full rounded-md border border-input bg-background px-3 text-sm text-foreground outline-none focus-visible:ring-1 focus-visible:ring-ring'

const onSubmit = handleSubmit(async (values) => {
  signedIn.value = false

  try {
    await session.login(values.email, values.password)
  } catch (caught) {
    applyApiErrors(caught)
    return
  }

  const redirect = route.query.redirect
  if (typeof redirect === 'string' && redirect.startsWith('/') && !redirect.startsWith('//')) {
    await router.push(redirect)
    return
  }

  signedIn.value = true
})

function applyApiErrors(caught: unknown): void {
  const fieldErrors = readFieldErrors(caught, ['email', 'password'])

  if (Object.keys(fieldErrors).length > 0) {
    setErrors(fieldErrors)
    return
  }

  setErrors({ email: readMessage(caught) })
}
</script>

<template>
  <main class="grid min-h-screen place-items-center bg-background px-5 py-16 text-foreground">
    <form class="w-full max-w-md rounded-md border border-border bg-card p-8 shadow" novalidate @submit="onSubmit">
      <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary">
        {{ t('auth.loginKicker') }}
      </p>
      <h1 class="mt-2 font-display text-4xl font-semibold uppercase">
        {{ t('auth.loginTitle') }}
      </h1>

      <label class="mt-8 block text-xs font-semibold uppercase tracking-[0.14em] text-muted-foreground">
        {{ t('auth.email') }}
        <input
          v-model="email"
          v-bind="emailAttrs"
          type="email"
          autocomplete="email"
          :class="cn(fieldClass, errors.email && 'border-red-500')"
        />
        <span v-if="errors.email" class="mt-1 block text-sm text-red-500">{{ errors.email }}</span>
      </label>

      <label class="mt-5 block text-xs font-semibold uppercase tracking-[0.14em] text-muted-foreground">
        {{ t('auth.password') }}
        <input
          v-model="password"
          v-bind="passwordAttrs"
          type="password"
          autocomplete="current-password"
          :class="cn(fieldClass, errors.password && 'border-red-500')"
        />
        <span v-if="errors.password" class="mt-1 block text-sm text-red-500">{{ errors.password }}</span>
      </label>

      <p v-if="signedIn" class="mt-4 text-sm text-primary">{{ t('auth.signedIn') }}</p>

      <Button type="submit" size="lg" class="mt-6 w-full" :disabled="isSubmitting">
        {{ isSubmitting ? t('auth.submitting') : t('auth.submitLogin') }}
      </Button>

      <p class="mt-6 text-center text-sm text-muted-foreground">
        <RouterLink to="/registo" class="text-primary hover:text-primary/80">
          {{ t('auth.createShop') }}
        </RouterLink>
      </p>
    </form>
  </main>
</template>
