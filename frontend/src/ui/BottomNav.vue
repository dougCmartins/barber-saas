<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { CalendarDays, Home, LayoutDashboard } from '@lucide/vue'
import { buttonClasses } from '@/ui/buttonClasses'

const props = defineProps<{
  active: 'home' | 'schedule'
  slug: string
}>()

const { t } = useI18n()

const homeTo = computed(() =>
  props.slug ? { name: 'public-home', params: { slug: props.slug } } : '/',
)

function itemClass(isActive: boolean): string {
  return buttonClasses(
    'ghost',
    undefined,
    ['h-12 flex-col gap-1 px-2 text-[10px]', isActive ? 'text-primary' : 'text-muted-foreground'].join(' '),
  )
}
</script>

<template>
  <nav
    class="fixed inset-x-0 bottom-0 z-40 border-t border-border bg-background/95 px-5 pb-[max(0.75rem,env(safe-area-inset-bottom))] pt-2 backdrop-blur md:hidden"
    :aria-label="t('nav.label')"
  >
    <div class="mx-auto grid max-w-md grid-cols-3">
      <RouterLink :to="homeTo" :class="itemClass(active === 'home')">
        <Home class="size-4" />
        <span>{{ t('nav.home') }}</span>
      </RouterLink>
      <a href="#servicos" :class="itemClass(active === 'schedule')">
        <CalendarDays class="size-4" />
        <span>{{ t('nav.schedule') }}</span>
      </a>
      <RouterLink :to="{ name: 'dashboard' }" :class="itemClass(false)">
        <LayoutDashboard class="size-4" />
        <span>{{ t('nav.management') }}</span>
      </RouterLink>
    </div>
  </nav>
</template>
