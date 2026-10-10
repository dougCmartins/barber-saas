<script setup lang="ts">
import { MapPin } from '@lucide/vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>

<template>
  <div class="flex items-center gap-2 border-b border-border py-5 text-xs text-muted-foreground">
    <MapPin class="size-4 text-primary" />
    {{ t('tenant.location') }}
  </div>
</template>
