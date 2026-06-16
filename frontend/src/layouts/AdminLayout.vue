<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <AdminSidebar :is-open="sidebarOpen" @close="sidebarOpen = false" />

    <!-- Main Content Area -->
    <div class="md:pl-64">
      <!-- Header -->
      <AdminHeader @toggle-sidebar="sidebarOpen = !sidebarOpen" />

      <!-- Page Content -->
      <main class="p-6">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import AdminHeader from '@/components/admin/AdminHeader.vue'
import AdminSidebar from '@/components/admin/AdminSidebar.vue'

// Initialiser en fonction de la taille de l'écran
const sidebarOpen = ref(window.innerWidth >= 768)

// Gérer le resize
const handleResize = () => {
  // Sur desktop (md et plus), toujours ouvert
  if (window.innerWidth >= 768) {
    sidebarOpen.value = true
  } else {
    // Sur mobile, garder l'état actuel (probablement fermé)
    // ou le fermer si l'utilisateur réduit la fenêtre
    sidebarOpen.value = false
  }
}

onMounted(() => {
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})
</script>
