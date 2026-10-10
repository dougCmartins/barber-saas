<script setup lang="ts">
import { onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { Menu } from '@lucide/vue'
import AiBanner from '@/Domain/Catalog/AiBanner.vue'
import ServiceMenu from '@/Domain/Catalog/ServiceMenu.vue'
import { publicServices } from '@/Domain/Catalog/serviceModel'
import Brand from '@/Domain/Tenant/Brand.vue'
import Hero from '@/Domain/Tenant/Hero.vue'
import LocationStrip from '@/Domain/Tenant/LocationStrip.vue'
import BottomNav from '@/ui/BottomNav.vue'
import Button from '@/ui/Button.vue'
import { buttonClasses } from '@/ui/buttonClasses'

const { t } = useI18n()

onMounted(() => {
  document.title = t('tenant.pageTitle')
})
</script>

<template>
  <main class="min-h-screen bg-background pb-24 text-foreground md:pb-12">
    <header class="mx-auto grid max-w-6xl grid-cols-[minmax(0,1fr)_auto] items-center gap-4 px-5 py-4 md:px-8">
      <Brand />
      <div class="flex items-center gap-2">
        <a
          href="#"
          :class="buttonClasses('ghost', 'default', 'hidden md:inline-flex')"
          @click.prevent
        >
          {{ t('tenant.ownerPanel') }}
        </a>
        <Button variant="ghost" size="icon" :aria-label="t('tenant.openMenu')">
          <Menu />
        </Button>
      </div>
    </header>

    <Hero />

    <div class="mx-auto max-w-6xl px-5 md:px-8">
      <LocationStrip />
      <section class="py-8">
        <AiBanner />
      </section>
      <ServiceMenu :services="publicServices" />
    </div>

    <BottomNav active="home" />
  </main>
</template>
