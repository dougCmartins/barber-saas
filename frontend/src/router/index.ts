import { createRouter, createWebHistory } from 'vue-router'
import PublicHome from '@/Domain/Tenant/PublicHome.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'public-home',
      component: PublicHome,
    },
  ],
})

export default router
