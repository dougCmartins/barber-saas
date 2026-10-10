<script setup lang="ts">
import { Star } from '@lucide/vue'
import { useI18n } from 'vue-i18n'
import heroImage from '@/assets/barbershop-hero.jpg'
import { buttonClasses } from '@/ui/buttonClasses'

const { t } = useI18n()
</script>

<template>
  <section class="relative mx-auto min-h-[440px] max-w-7xl overflow-hidden md:min-h-[540px] md:rounded-md">
    <img
      :src="heroImage"
      :alt="t('tenant.heroAlt')"
      width="1536"
      height="864"
      fetchpriority="high"
      class="absolute inset-0 h-full w-full object-cover"
    />
    <div class="absolute inset-0 bg-gradient-to-r from-background via-background/80 to-background/15" />
    <div
      class="relative flex min-h-[440px] max-w-2xl flex-col justify-end px-5 py-10 md:min-h-[540px] md:justify-center md:px-14"
    >
      <div class="mb-4 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-primary">
        <span class="h-px w-8 bg-primary" />
        {{ t('tenant.heroKicker') }}
      </div>
      <h1 class="font-display text-5xl font-semibold uppercase leading-[0.94] text-foreground sm:text-6xl md:text-7xl">
        {{ t('tenant.heroTitleLine1') }}<br />
        {{ t('tenant.heroTitleLine2') }}
      </h1>
      <p class="mt-5 max-w-md text-sm leading-6 text-muted-foreground md:text-base">
        {{ t('tenant.heroBody') }}
      </p>
      <div class="mt-7 flex flex-wrap items-center gap-3">
        <a href="#" :class="buttonClasses('default', 'lg')" @click.prevent>
          {{ t('tenant.book') }}
        </a>
        <span class="flex items-center gap-1.5 text-xs text-foreground">
          <Star class="size-4 fill-primary text-primary" />
          {{ t('tenant.rating') }}
        </span>
      </div>
    </div>
  </section>
</template>
