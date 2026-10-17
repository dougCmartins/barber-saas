<script setup lang="ts">
import { Check, Plus } from '@lucide/vue'
import { useI18n } from 'vue-i18n'
import type { HaircutSuggestion, ProductRecommendation } from '@/Domain/Visagism/visagismModel'
import { buttonClasses } from '@/ui/buttonClasses'
import { cn } from '@/ui/cn'

defineProps<{
  open: boolean
  suggestion: HaircutSuggestion | null
  products: ProductRecommendation[]
  selectedProductIds: number[]
  totalLabel: string
}>()

const emit = defineEmits<{
  close: []
  'toggle-product': [productId: number]
  confirm: []
}>()

const { t } = useI18n()
</script>

<template>
  <Teleport to="body">
    <div
      v-if="open"
      class="fixed inset-0 z-50 flex items-end justify-center bg-background/70 backdrop-blur-sm"
      role="dialog"
      aria-modal="true"
      :aria-label="t('visagism.upsellTitle')"
      @click.self="emit('close')"
    >
      <div
        class="mx-auto w-full max-w-lg rounded-t-2xl border-t border-border bg-card px-4 pb-8 pt-5 shadow-lg"
      >
        <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-border" />

        <div class="flex flex-col items-center gap-2 text-center">
          <span class="grid size-10 place-items-center rounded-full bg-primary/15 text-primary">
            <Check class="size-5" />
          </span>
          <h2 class="font-display text-2xl font-semibold uppercase tracking-wide text-foreground">
            {{ t('visagism.upsellTitle') }}
          </h2>
          <p class="text-sm leading-6 text-muted-foreground">
            {{ t('visagism.upsellBody', { style: suggestion?.name ?? '' }) }}
          </p>
        </div>

        <ul class="mt-5 flex flex-col gap-3">
          <li
            v-for="product in products"
            :key="product.id"
            class="flex items-center gap-3 rounded-lg border border-border bg-background/40 p-3"
          >
            <div class="grid size-16 shrink-0 place-items-center overflow-hidden rounded-md bg-muted">
              <img
                v-if="product.image_url"
                :src="product.image_url"
                :alt="product.name"
                class="size-full object-cover"
                loading="lazy"
              />
              <span v-else class="font-display text-xs text-muted-foreground">{{ product.name.slice(0, 2) }}</span>
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-semibold text-foreground">{{ product.name }}</p>
              <p class="mt-0.5 text-xs text-primary">
                {{ t('visagism.productPrice', { price: product.price.toFixed(2).replace('.', ',') }) }}
              </p>
              <p class="mt-1 text-xs leading-5 text-muted-foreground">{{ product.microcopy }}</p>
            </div>
            <button
              type="button"
              :class="
                cn(
                  buttonClasses(
                    selectedProductIds.includes(product.id) ? 'default' : 'outline',
                    'sm',
                    'shrink-0',
                  ),
                )
              "
              @click="emit('toggle-product', product.id)"
            >
              <Plus v-if="!selectedProductIds.includes(product.id)" class="size-3.5" />
              <Check v-else class="size-3.5" />
              {{
                selectedProductIds.includes(product.id)
                  ? t('visagism.productAdded')
                  : t('visagism.addToBooking')
              }}
            </button>
          </li>
        </ul>

        <button
          type="button"
          :class="buttonClasses('default', 'lg', 'mt-6 h-12 w-full')"
          @click="emit('confirm')"
        >
          {{ t('visagism.confirmBooking', { total: totalLabel }) }}
        </button>
      </div>
    </div>
  </Teleport>
</template>
