import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('@/views/Home.vue'),
      meta: { requiresAuth: false }
    },
    {
      path: '/products',
      name: 'products',
      component: () => import('@/views/AllProducts.vue'),
      meta: { requiresAuth: false }
    },
    {
      path: '/products/:id',
      name: 'product-detail',
      component: () => import('@/views/ProductDetail.vue'),
      meta: { requiresAuth: false }
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/views/auth/Register.vue'),
      meta: { requiresGuest: true }
    },
    {
      path: '/verify-otp',
      name: 'verify-otp',
      component: () => import('@/views/auth/VerifyOtp.vue'),
      meta: { requiresGuest: true }
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/auth/Login.vue'),
      meta: { requiresGuest: true }
    },
    {
      path: '/forgot-password',
      name: 'forgot-password',
      component: () => import('@/views/auth/ForgotPassword.vue'),
      meta: { requiresGuest: true }
    },
    {
      path: '/reset-password',
      name: 'reset-password',
      component: () => import('@/views/auth/ResetPassword.vue'),
      meta: { requiresGuest: true }
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('@/views/Profile.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/dashboard',
      name: 'client-dashboard',
      component: () => import('@/views/client/Dashboard.vue'),
      meta: { requiresAuth: true }
    },
    // Route À propos
    {
      path: '/about',
      name: 'about',
      component: () => import('@/views/About.vue'),
      meta: { requiresAuth: false }
    },
    // Route Aide / Assistant IA
    {
      path: '/help',
      name: 'help',
      component: () => import('@/views/Help.vue'),
      meta: { requiresAuth: false }
    },
    // Routes Produits Client
    {
      path: '/suivi/produits',
      name: 'my-products',
      component: () => import('@/views/client/products/ProductsList.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/publish/produit',
      name: 'create-product',
      component: () => import('@/views/client/products/PublishProduct.vue'),
      meta: { requiresAuth: true }
    },
    // Routes Admin
    {
      path: '/admin',
      component: () => import('@/layouts/AdminLayout.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
      children: [
        {
          path: '',
          redirect: '/admin/dashboard'
        },
        {
          path: 'dashboard',
          name: 'admin-dashboard',
          component: () => import('@/views/admin/AdminDashboard.vue')
        },
        {
          path: 'products',
          name: 'admin-products',
          component: () => import('@/views/admin/products/ProductsList.vue')
        },
        {
          path: 'products/create',
          name: 'admin-products-create',
          component: () => import('@/views/admin/products/ProductForm.vue')
        },
        {
          path: 'products/:id/edit',
          name: 'admin-products-edit',
          component: () => import('@/views/admin/products/ProductForm.vue')
        },
        {
          path: 'products/pending',
          name: 'admin-products-pending',
          component: () => import('@/views/admin/products/ProductsPending.vue')
        },
        {
          path: 'products/validated',
          name: 'admin-products-validated',
          component: () => import('@/views/admin/products/ProductsValidated.vue')
        },
        {
          path: 'products/rejected',
          name: 'admin-products-rejected',
          component: () => import('@/views/admin/products/ProductsRejected.vue')
        },
        {
          path: 'categories',
          name: 'admin-categories',
          component: () => import('@/views/admin/categories/CategoriesList.vue')
        },
        {
          path: 'test-categories',
          name: 'test-categories',
          component: () => import('@/views/TestCategories.vue')
        },
        {
          path: 'clients',
          name: 'admin-clients',
          component: () => import('@/views/admin/clients/ClientsList.vue')
        },
        {
          path: 'users',
          name: 'admin-users',
          component: () => import('@/views/admin/users/AdminUsersList.vue')
        },
        {
          path: 'profile',
          name: 'admin-profile',
          component: () => import('@/views/admin/profile/AdminProfile.vue')
        },
        {
          path: 'notifications',
          name: 'admin-notifications',
          component: () => import('@/views/admin/notifications/AdminNotifications.vue')
        }
      ]
    },
    // Route 404
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('@/views/NotFound.vue')
    }
  ],
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Navigation Guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const isAuthenticated = authStore.isAuthenticated
  const userType = authStore.userType

  // Routes nécessitant authentification
  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ 
      name: 'login', 
      query: { redirect: to.fullPath } 
    })
  }
  // Routes admin uniquement
  else if (to.meta.requiresAdmin && userType !== 'admin') {
    next({ name: 'home' })
  }
  // Routes pour invités uniquement (login, register, etc.)
  else if (to.meta.requiresGuest && isAuthenticated) {
    // Rediriger vers le dashboard approprié selon le type d'utilisateur
    if (userType === 'admin') {
      next({ name: 'admin-dashboard' })
    } else {
      next({ name: 'home' })
    }
  }
  // Route autorisée
  else {
    next()
  }
})

export default router
