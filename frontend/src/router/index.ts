import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/Domain/Identity/Login.vue'
import { useSessionStore } from '@/Domain/Identity/sessionStore'
import BookingView from '@/Domain/Orchestrator/BookingView.vue'
import ClientAppointments from '@/Domain/Orchestrator/ClientAppointments.vue'
import ClientHub from '@/Domain/Orchestrator/ClientHub.vue'
import Onboarding from '@/Domain/Orchestrator/Onboarding.vue'
import Dashboard from '@/Domain/Schedule/Dashboard.vue'
import PublicHome from '@/Domain/Tenant/PublicHome.vue'
import VisagismFlow from '@/Domain/Visagism/VisagismFlow.vue'

declare module 'vue-router' {
  interface RouteMeta {
    requiresAuth?: boolean
  }
}

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: Login,
    },
    {
      path: '/registo',
      name: 'onboarding',
      component: Onboarding,
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: Dashboard,
      meta: { requiresAuth: true },
    },
    {
      path: '/:slug/agendamento',
      name: 'public-booking',
      component: BookingView,
    },
    {
      path: '/:slug/visagism',
      name: 'visagism',
      component: VisagismFlow,
      meta: { requiresAuth: true },
    },
    {
      path: '/',
      name: 'public-home-missing',
      component: ClientHub,
    },
    {
      path: '/meus-agendamentos',
      name: 'client-appointments',
      component: ClientAppointments,
    },
    {
      path: '/:slug',
      name: 'public-home',
      component: PublicHome,
    },
  ],
})

router.beforeEach((to) => {
  if (to.meta.requiresAuth !== true) {
    return
  }

  const session = useSessionStore()

  if (session.token) {
    return
  }

  return { name: 'login', query: { redirect: to.fullPath } }
})

export default router
