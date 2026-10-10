<script setup lang="ts">
import { Scissors } from '@lucide/vue'
import { useI18n } from 'vue-i18n'

withDefaults(
  defineProps<{
    compact?: boolean
  }>(),
  {
    compact: false,
  },
)

const { t } = useI18n()
</script>

<template>
  <div class="flex min-w-0 items-center gap-3">
    <span
      class="grid size-10 shrink-0 place-items-center rounded-md border border-primary/40 bg-primary/10 text-primary"
    >
      <Scissors class="size-5" aria-hidden="true" />
    </span>
    <div class="min-w-0 leading-none">
      <p class="truncate font-display text-lg font-semibold uppercase text-foreground">
        {{ t('tenant.brandName') }}
      </p>
      <p
        v-if="!compact"
        class="mt-1 text-[10px] font-semibold uppercase tracking-[0.22em] text-primary"
      >
        {{ t('tenant.brandClub') }}
      </p>
    </div>
  </div>
</template>
