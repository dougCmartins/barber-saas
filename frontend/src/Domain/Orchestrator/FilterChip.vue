<script setup lang="ts">
import { ChevronDown } from '@lucide/vue'
import { cn } from '@/ui/cn'

withDefaults(
  defineProps<{
    label: string
    active?: boolean
    caret?: boolean
  }>(),
  {
    active: false,
    caret: true,
  },
)

const emit = defineEmits<{
  click: []
}>()
</script>

<template>
  <button
    type="button"
    :aria-pressed="active"
    :class="
      cn(
        'inline-flex h-9 shrink-0 items-center gap-1.5 rounded-full border px-4 text-sm font-medium transition-colors',
        active
          ? 'border-primary bg-primary text-primary-foreground'
          : 'border-border bg-card text-foreground hover:bg-secondary',
      )
    "
    @click="emit('click')"
  >
    {{ label }}
    <ChevronDown v-if="caret" class="size-4" />
  </button>
</template>
