<script setup lang="ts">
import { Sparkles, Paperclip } from '@lucide/vue'
import { useI18n } from 'vue-i18n'
import { buttonClasses } from '@/ui/buttonClasses'
import { cn } from '@/ui/cn'

const emit = defineEmits<{
  'choose-ai': []
  'choose-manual': []
}>()

const { t } = useI18n()
</script>

<template>
  <section class="flex flex-1 flex-col gap-5 pt-2">
    <header>
      <p class="text-xs font-medium uppercase tracking-[0.14em] text-primary">
        {{ t('visagism.onboardingKicker') }}
      </p>
      <h1 class="mt-1 font-display text-2xl font-semibold uppercase tracking-wide text-foreground">
        {{ t('visagism.onboardingTitle') }}
      </h1>
      <p class="mt-2 text-sm leading-6 text-muted-foreground">
        {{ t('visagism.onboardingSubtitle') }}
      </p>
    </header>

    <button
      type="button"
      :class="
        cn(
          'flex flex-col gap-3 rounded-xl border border-primary/40 bg-card p-5 text-left shadow-[0_0_24px_rgba(212,175,55,0.12)] transition hover:border-primary',
        )
      "
      @click="emit('choose-ai')"
    >
      <span class="flex items-center gap-2">
        <span class="grid size-10 place-items-center rounded-full bg-primary/15 text-primary">
          <Sparkles class="size-5" />
        </span>
        <span class="rounded-md bg-primary px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-primary-foreground">
          {{ t('visagism.onboardingRecommended') }}
        </span>
      </span>
      <span class="font-display text-xl font-semibold uppercase tracking-wide text-foreground">
        {{ t('visagism.onboardingAiTitle') }}
      </span>
      <span class="text-sm leading-6 text-muted-foreground">
        {{ t('visagism.onboardingAiBody') }}
      </span>
    </button>

    <button
      type="button"
      :class="buttonClasses('outline', 'lg', 'h-auto flex-col items-start gap-3 rounded-xl p-5 text-left')"
      @click="emit('choose-manual')"
    >
      <span class="grid size-10 place-items-center rounded-full bg-muted text-foreground">
        <Paperclip class="size-5" />
      </span>
      <span class="font-display text-xl font-semibold uppercase tracking-wide text-foreground">
        {{ t('visagism.onboardingManualTitle') }}
      </span>
      <span class="text-sm leading-6 text-muted-foreground">
        {{ t('visagism.onboardingManualBody') }}
      </span>
    </button>
  </section>
</template>
