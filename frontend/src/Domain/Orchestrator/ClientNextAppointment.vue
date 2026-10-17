<script setup lang="ts">
import { CalendarDays, RotateCcw } from '@lucide/vue'
import { RouterLink } from 'vue-router'
import type { RouteLocationRaw } from 'vue-router'
import { buttonClasses } from '@/ui/buttonClasses'

defineProps<{
  time: string
  weekday: string
  date: string
  professionalName: string
  avatarUrl: string
  servicesLabel: string
  price: number
  bookingTo: RouteLocationRaw
}>()
</script>

<template>
  <section
    class="rounded-lg border border-primary/40 bg-gradient-to-br from-primary/15 to-card p-5"
    aria-label="Próximo agendamento"
  >
    <p class="text-xs font-semibold uppercase text-primary">Próximo agendamento</p>
    <div class="mt-3 flex items-end justify-between gap-4">
      <div class="min-w-0">
        <p class="font-display text-4xl font-bold leading-none text-foreground">{{ time }}</p>
        <p class="mt-2 flex items-center gap-1.5 text-sm text-muted-foreground">
          <CalendarDays class="size-4 shrink-0" />{{ weekday }}, {{ date }}
        </p>
      </div>
      <p class="shrink-0 text-lg font-semibold text-foreground">R$ {{ price }}</p>
    </div>
    <div class="mt-4 flex items-center gap-3 border-t border-border pt-4">
      <img
        :src="avatarUrl"
        :alt="professionalName"
        loading="lazy"
        class="size-10 shrink-0 rounded-full object-cover object-center ring-2 ring-primary/40"
      />
      <div class="min-w-0">
        <p class="text-sm font-semibold text-foreground">{{ professionalName }}</p>
        <p class="text-xs leading-5 text-muted-foreground">{{ servicesLabel }}</p>
      </div>
    </div>
    <RouterLink
      :to="bookingTo"
      :class="buttonClasses('outline', 'sm', 'mt-4 w-full text-muted-foreground hover:text-foreground')"
    >
      <RotateCcw class="size-3.5" /> Cancelar / Reagendar
    </RouterLink>
  </section>
</template>
