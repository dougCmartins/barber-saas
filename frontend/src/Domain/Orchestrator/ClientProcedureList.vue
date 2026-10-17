<script setup lang="ts">
import { Clock3 } from '@lucide/vue'
import { buttonClasses } from '@/ui/buttonClasses'

export interface HistoryEntry {
  id: number
  date: string
  serviceName: string
  professionalName: string
  serviceId: number | null
}

defineProps<{
  items: HistoryEntry[]
}>()

const emit = defineEmits<{
  rebook: [serviceId: number]
}>()

function rebook(serviceId: number | null): void {
  if (!serviceId) {
    return
  }

  emit('rebook', serviceId)
}
</script>

<template>
  <ul>
    <li
      v-for="item in items"
      :key="item.id"
      class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-3 border-b border-border py-4 last:border-0"
    >
      <div class="min-w-0">
        <p class="text-sm font-semibold leading-5 text-foreground">{{ item.serviceName }}</p>
        <p class="mt-1 flex items-start gap-1 text-xs leading-5 text-muted-foreground">
          <Clock3 class="mt-1 size-3 shrink-0" />
          <span>{{ item.date }} · {{ item.professionalName }}</span>
        </p>
      </div>
      <button
        type="button"
        :class="buttonClasses('outline', 'sm', 'shrink-0 text-xs')"
        @click="rebook(item.serviceId)"
      >
        Agendar novamente
      </button>
    </li>
  </ul>
</template>
