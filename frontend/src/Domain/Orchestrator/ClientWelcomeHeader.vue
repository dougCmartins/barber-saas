<script setup lang="ts">
defineProps<{
  firstName: string
  avatarUrl: string
}>()
</script>

<template>
  <header class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-4">
    <div class="min-w-0">
      <p class="text-sm text-muted-foreground">Bem-vindo de volta</p>
      <h1 class="truncate font-display text-3xl font-bold text-foreground">Olá, {{ firstName }}</h1>
    </div>
    <img
      :src="avatarUrl"
      :alt="`Foto de ${firstName}`"
      loading="lazy"
      class="size-12 shrink-0 rounded-full object-cover object-center ring-2 ring-primary/40"
    />
  </header>
</template>
