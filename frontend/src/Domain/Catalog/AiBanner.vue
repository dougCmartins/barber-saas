<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { Sparkles } from '@lucide/vue'
import { buttonClasses } from '@/ui/buttonClasses'

const { t } = useI18n()
</script>

<template>
  <a
    href="#"
    :class="
      buttonClasses(
        'ghost',
        undefined,
        'group h-auto w-full justify-between whitespace-normal border border-primary/30 bg-primary/10 p-5 text-left hover:bg-primary/15',
      )
    "
    @click.prevent
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
  </a>
</template>
