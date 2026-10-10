<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Clock3 } from '@lucide/vue'
import type { Service } from '@/Domain/Catalog/serviceModel'
import { buttonClasses } from '@/ui/buttonClasses'

const props = defineProps<{
  service: Service
}>()

const { t } = useI18n()

const duration = computed(() => {
  const minutes = props.service.duration_in_minutes

  if (minutes < 60) {
    return t('catalog.durationMinutes', { count: minutes })
  }

  const hours = Math.floor(minutes / 60)
  const rest = minutes % 60

  if (!rest) {
    return t('catalog.durationHours', { hours })
  }

  return t('catalog.durationHoursMinutes', { hours, minutes: rest })
})
</script>

<template>
  <article class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-4 border-b border-border py-5 last:border-0">
    <div class="min-w-0">
      <p class="font-display text-xl font-semibold text-foreground">{{ service.name }}</p>
      <p v-if="service.description" class="mt-1 text-sm leading-6 text-muted-foreground">
        {{ service.description }}
      </p>
      <p class="mt-3 flex items-center gap-1.5 text-xs font-medium text-muted-foreground">
        <Clock3 class="size-3.5" aria-hidden="true" />
        {{ duration }}
      </p>
    </div>
    <div class="shrink-0 text-right">
      <p class="mb-3 text-sm font-semibold text-foreground">
        {{ t('catalog.price', { price: service.price }) }}
      </p>
      <a href="#" :class="buttonClasses('default', 'sm')" @click.prevent>
        {{ t('catalog.choose') }}
      </a>
    </div>
  </article>
</template>
