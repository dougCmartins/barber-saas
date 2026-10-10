<script setup lang="ts">
import { computed } from 'vue'
import { buttonClasses, type ButtonSize, type ButtonVariant } from '@/ui/buttonClasses'

const props = withDefaults(
  defineProps<{
    variant?: ButtonVariant
    size?: ButtonSize
    type?: 'button' | 'submit' | 'reset'
  }>(),
  {
    variant: 'default',
    size: 'default',
    type: 'button',
  },
)

const classes = computed(() => buttonClasses(props.variant, props.size))
</script>

<template>
  <button :type="type" :class="classes">
    <slot />
  </button>
</template>
