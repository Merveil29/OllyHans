<template>
  <AppLayout>
    <div>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-black via-gray-900 to-black text-white overflow-hidden">
      <div class="absolute inset-0 bg-[url('/images/hero-bg.jpg')] bg-cover bg-center opacity-20"></div>
      <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/60 to-black"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 lg:py-36 text-center">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-6 leading-tight tracking-tight">
          Bienvenue chez <span class="text-primary-400">Olly Hans Distribution</span>
        </h1>
        <p class="text-lg sm:text-xl text-white/70 max-w-2xl mx-auto mb-10 leading-relaxed">
          Votre destination premium pour les produits de beauté et cosmétiques de qualité. 
          Découvrez une sélection exclusive de soins pour sublimer votre beauté naturelle.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
          <router-link
            to="/products"
            class="inline-flex items-center gap-2 bg-primary-400 text-black px-8 py-3.5 rounded-lg hover:bg-primary-500 transition-all duration-200 font-semibold text-lg shadow-xl shadow-primary-400/25 hover:shadow-primary-400/40"
          >
            Découvrir nos produits
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
          </router-link>
          <router-link
            to="/about"
            class="inline-flex items-center gap-2 text-white/80 hover:text-primary-400 px-8 py-3.5 rounded-lg border border-white/20 hover:border-primary-400/50 transition-all duration-200 font-medium text-lg"
          >
            En savoir plus
          </router-link>
        </div>
      </div>
      <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-white to-transparent"></div>
    </section>

    <!-- Carrousel des Catégories -->
    <CategoriesCarousel />

    <!-- Carrousel des Produits Récents -->
    <ProductsCarousel />

    <!-- Features -->
    <section class="py-12 sm:py-16 lg:py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-center mb-8 sm:mb-10 lg:mb-12 text-gray-900">Pourquoi choisir Olly Hans Distribution ?</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
          <div class="bg-white p-5 sm:p-6 rounded-lg shadow-md transform hover:-translate-y-1 transition-all duration-300 hover:shadow-xl">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-3 sm:mb-4">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold mb-2 text-gray-900">Qualité garantie</h3>
            <p class="text-sm sm:text-base text-gray-600">Tous nos produits sont soigneusement sélectionnés pour leur qualité.</p>
          </div>
          
          <div class="bg-white p-5 sm:p-6 rounded-lg shadow-md transform hover:-translate-y-1 transition-all duration-300 hover:shadow-xl">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-3 sm:mb-4">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold mb-2 text-gray-900">Livraison rapide</h3>
            <p class="text-sm sm:text-base text-gray-600">Recevez vos commandes rapidement partout au pays.</p>
          </div>
          
          <div class="bg-white p-5 sm:p-6 rounded-lg shadow-md transform hover:-translate-y-1 transition-all duration-300 hover:shadow-xl sm:col-span-2 lg:col-span-1">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-success-100 rounded-lg flex items-center justify-center mb-3 sm:mb-4">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold mb-2 text-gray-900">Paiement sécurisé</h3>
            <p class="text-sm sm:text-base text-gray-600">Vos transactions sont protégées et sécurisées.</p>
          </div>
        </div>
      </div>
    </section>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/components/layout/AppLayout.vue'
import CategoriesCarousel from '@/components/home/CategoriesCarousel.vue'
import ProductsCarousel from '@/components/home/ProductsCarousel.vue'
</script>
