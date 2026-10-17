<script setup lang="ts">
import { CalendarDays, Home, User } from '@lucide/vue'
import { useI18n } from 'vue-i18n'
import { RouterLink } from 'vue-router'
import type { RouteLocationRaw } from 'vue-router'
import { buttonClasses } from '@/ui/buttonClasses'

withDefaults(
  defineProps<{
    active: 'home' | 'schedule' | 'account'
    homeTo: RouteLocationRaw
    bookingTo: RouteLocationRaw
    accountTo?: RouteLocationRaw
  }>(),
  {
    accountTo: '/meus-agendamentos',
  },
)

const { t } = useI18n()

function itemClass(isActive: boolean): string {
  return buttonClasses(
    'ghost',
    undefined,
    ['h-12 flex-col gap-1', isActive ? 'text-primary' : 'text-muted-foreground'].join(' '),
  )
}
</script>

<template>
  <nav
    class="fixed inset-x-0 bottom-0 z-40 border-t border-border bg-background/95 px-4 pb-[max(0.75rem,env(safe-area-inset-bottom))] pt-2 backdrop-blur"
    :aria-label="t('nav.label')"
  >
    <div class="mx-auto grid max-w-lg grid-cols-3">
      <RouterLink
        :to="homeTo"
        :aria-current="active === 'home' ? 'page' : undefined"
        :class="itemClass(active === 'home')"
      >
        <Home class="size-5" />
        <span class="text-xs">{{ t('nav.home') }}</span>
      </RouterLink>
      <RouterLink
        :to="bookingTo"
        :aria-current="active === 'schedule' ? 'page' : undefined"
        :class="itemClass(active === 'schedule')"
      >
        <CalendarDays class="size-5" />
        <span class="text-xs">{{ t('nav.schedule') }}</span>
      </RouterLink>
      <RouterLink
        :to="accountTo"
        :aria-current="active === 'account' ? 'page' : undefined"
        :class="itemClass(active === 'account')"
      >
        <User class="size-5" />
        <span class="text-xs">{{ t('nav.account') }}</span>
      </RouterLink>
    </div>
  </nav>
</template>
