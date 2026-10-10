<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import type { Service } from '@/Domain/Catalog/serviceModel'
import ServiceCard from '@/Domain/Catalog/ServiceCard.vue'
import Button from '@/ui/Button.vue'

defineProps<{
  services: Service[]
}>()

const { t } = useI18n()

const categories = ['catalog.categoryCut', 'catalog.categoryBeard', 'catalog.categoryCombos'] as const
</script>

<template>
  <section>
    <div class="grid grid-cols-[minmax(0,1fr)_auto] items-end gap-4 border-b border-border pb-4">
      <div class="min-w-0">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary">
          {{ t('catalog.eyebrow') }}
        </p>
        <h2 class="mt-2 font-display text-3xl font-semibold uppercase">
          {{ t('catalog.heading') }}
        </h2>
      </div>
    </div>
    <div class="flex gap-2 overflow-x-auto py-5" :aria-label="t('catalog.categoriesLabel')">
      <Button size="sm">{{ t('catalog.all') }}</Button>
      <Button v-for="category in categories" :key="category" variant="outline" size="sm">
        {{ t(category) }}
      </Button>
    </div>
    <div class="grid gap-x-10 md:grid-cols-2">
      <ServiceCard v-for="service in services" :key="service.id" :service="service" />
    </div>
  </section>
</template>
