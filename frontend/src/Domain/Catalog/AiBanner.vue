<script setup lang="ts">
import { Sparkles } from '@lucide/vue'
import { useI18n } from 'vue-i18n'
import { RouterLink } from 'vue-router'
import { buttonClasses } from '@/ui/buttonClasses'

defineProps<{
  to: { name: string; params?: Record<string, string> }
}>()

const { t } = useI18n()
</script>

<template>
  <RouterLink
    :to="to"
    :class="
      buttonClasses(
        'ghost',
        undefined,
        'group h-auto w-full justify-between whitespace-normal border border-primary/30 bg-primary/10 p-5 text-left hover:bg-primary/15',
      )
    "
  >
    <span class="flex min-w-0 items-center gap-4">
      <span class="grid size-11 shrink-0 place-items-center rounded-md bg-primary text-primary-foreground">
        <Sparkles class="size-5" />
      </span>
      <span>
        <span class="block font-display text-lg font-semibold text-foreground">{{ t('catalog.aiTitle') }}</span>
        <span class="mt-0.5 block text-sm text-muted-foreground">{{ t('catalog.aiSubtitle') }}</span>
      </span>
    </span>
    <span class="shrink-0 text-primary transition-transform group-hover:translate-x-1">→</span>
  </RouterLink>
</template>
