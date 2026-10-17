<script setup lang="ts">
import { computed, watch } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { Menu } from '@lucide/vue'
import { useI18n } from 'vue-i18n'
import heroImage from '@/assets/barbershop-hero.jpg'
import AiBanner from '@/Domain/Catalog/AiBanner.vue'
import { usePublicCatalogStore } from '@/Domain/Catalog/publicCatalogStore'
import type { Service } from '@/Domain/Catalog/serviceModel'
import ServiceMenu from '@/Domain/Catalog/ServiceMenu.vue'
import Brand from '@/Domain/Tenant/Brand.vue'
import Hero from '@/Domain/Tenant/Hero.vue'
import LocationStrip from '@/Domain/Tenant/LocationStrip.vue'
import { usePublicTenantStore } from '@/Domain/Tenant/publicTenantStore'
import BottomNav from '@/ui/BottomNav.vue'
import Button from '@/ui/Button.vue'
import { buttonClasses } from '@/ui/buttonClasses'

const route = useRoute()
const router = useRouter()
const tenantStore = usePublicTenantStore()
const catalogStore = usePublicCatalogStore()
const { t } = useI18n()

const slug = computed(() => {
  const value = route.params.slug

  return typeof value === 'string' ? value : ''
})

const shop = computed(() => tenantStore.barbershop)

const status = computed(() => {
  if (!slug.value || tenantStore.notFound || catalogStore.notFound) {
    return 'missing'
  }

  if (tenantStore.loading || catalogStore.loading || !shop.value) {
    return 'loading'
  }

  return 'ready'
})

const heroSrc = computed(() => shop.value?.banner_url || heroImage)

watch(
  slug,
  (value) => {
    if (!value) {
      return
    }

    void tenantStore.fetchBarbershop(value)
    void catalogStore.fetchPublicCatalog(value)
  },
  { immediate: true },
)

watch(
  status,
  (value) => {
    if (value === 'ready' && shop.value) {
      document.title = shop.value.name
      return
    }

    if (value === 'missing') {
      document.title = t('tenant.notFoundTitle')
      return
    }

    document.title = t('tenant.pageTitle')
  },
  { immediate: true },
)

function openBooking(service: Service): void {
  void router.push({
    name: 'public-booking',
    params: { slug: slug.value },
    query: { service: String(service.id) },
  })
}
</script>

<template>
  <main class="min-h-screen bg-background pb-24 text-foreground md:pb-12">
    <p v-if="status === 'loading'" class="px-5 py-16 text-sm text-muted-foreground">
      {{ t('tenant.loading') }}
    </p>

    <section v-else-if="status === 'missing'" class="mx-auto max-w-6xl px-5 py-16 md:px-8">
      <h1 class="font-display text-4xl font-semibold uppercase">
        {{ t('tenant.notFoundTitle') }}
      </h1>
      <p class="mt-3 max-w-md text-sm leading-6 text-muted-foreground">
        {{ t('tenant.notFoundBody') }}
      </p>
    </section>

    <template v-else-if="shop">
      <header class="mx-auto grid max-w-6xl grid-cols-[minmax(0,1fr)_auto] items-center gap-4 px-5 py-4 md:px-8">
        <Brand :name="shop.name" />
        <div class="flex items-center gap-2">
          <RouterLink
            :to="{ name: 'login' }"
            :class="buttonClasses('ghost', 'default', 'hidden md:inline-flex')"
          >
            {{ t('tenant.ownerPanel') }}
          </RouterLink>
          <Button variant="ghost" size="icon" :aria-label="t('tenant.openMenu')">
            <Menu />
          </Button>
        </div>
      </header>

      <Hero :image-src="heroSrc" :rating="shop.rating" :total-reviews="shop.total_reviews" />

      <div class="mx-auto max-w-6xl px-5 md:px-8">
        <LocationStrip
          v-if="shop.city && shop.closing_time"
          :city="shop.city"
          :closing-time="shop.closing_time"
        />
        <section class="py-8">
          <AiBanner :to="{ name: 'visagism', params: { slug } }" />
        </section>
        <ServiceMenu :services="catalogStore.services" @choose="openBooking" />
      </div>
    </template>

    <BottomNav active="home" :slug="slug" />
  </main>
</template>
