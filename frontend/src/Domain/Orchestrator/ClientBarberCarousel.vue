<script setup lang="ts">
import { RouterLink } from 'vue-router'
import type { RouteLocationRaw } from 'vue-router'
import type { Professional } from '@/Domain/Catalog/professionalModel'

defineProps<{
  professionals: Professional[]
  fallbackAvatarUrl: string
  bookingTo: RouteLocationRaw
}>()
</script>

<template>
  <div class="-mx-4 flex gap-4 overflow-x-auto px-4 pb-1 [scrollbar-width:none]">
    <RouterLink
      v-for="professional in professionals"
      :key="professional.id"
      :to="bookingTo"
      class="flex w-20 shrink-0 flex-col items-center gap-2"
    >
      <img
        :src="professional.avatar_url || fallbackAvatarUrl"
        :alt="professional.name"
        loading="lazy"
        class="size-16 shrink-0 rounded-full object-cover ring-2 ring-primary/40"
      />
      <span class="w-full truncate text-center text-xs font-medium text-foreground">
        {{ professional.name.split(' ')[0] }}
      </span>
    </RouterLink>
  </div>
</template>
