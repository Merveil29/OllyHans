<template>
  <AppLayout>
    <div class="bg-gray-50">
      <!-- Hero Section -->
      <section class="relative overflow-hidden bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 text-white">
        <!-- Decorative shapes -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
          <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary-600/20 rounded-full blur-3xl"></div>
          <div class="absolute -bottom-32 -left-32 w-[30rem] h-[30rem] bg-secondary-500/10 rounded-full blur-3xl"></div>
          <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[40rem] h-[40rem] bg-primary-500/5 rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 lg:py-36">
          <div class="text-center max-w-3xl mx-auto">
            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-white/10 backdrop-blur-sm border border-white/20 mb-6">
              <span class="w-2 h-2 bg-primary-400 rounded-full mr-2 animate-pulse"></span>
              Expert Cosmétique haut de gamme 🇧🇯🇫🇷
            </span>
            <h1 class="font-heading text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
              Sublimez votre peau😻
              <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-300 to-primary-500"> Révélez votre marque</span>
            </h1>
            <p class="text-lg sm:text-xl text-primary-100 leading-relaxed max-w-2xl mx-auto">
              Olly Hans Distribution — Expertise cosmétique haut de gamme. Formulation, vente en kilo &amp; détail, création de votre marque.
            </p>
          </div>
        </div>

        <!-- Wave separator -->
        <div class="absolute bottom-0 left-0 right-0">
          <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 60L60 50C120 40 240 20 360 15C480 10 600 20 720 25C840 30 960 30 1080 25C1200 20 1320 10 1380 5L1440 0V60H1380C1320 60 1200 60 1080 60C960 60 840 60 720 60C600 60 480 60 360 60C240 60 120 60 60 60H0Z" fill="#f9fafb"/>
          </svg>
        </div>
      </section>

      <!-- Mission & Vision -->
      <section class="py-16 sm:py-20 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">
            <!-- Image / Illustration -->
            <div class="relative order-2 lg:order-1">
              <div class="relative rounded-2xl overflow-hidden shadow-2xl aspect-[4/3] bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center">
                <div class="text-center p-8">
                  <div class="w-24 h-24 sm:w-32 sm:h-32 mx-auto mb-6 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center shadow-lg transform rotate-3">
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <p class="font-heading text-lg sm:text-xl font-bold text-primary-800">Cosmétique haut de gamme</p>
                  <p class="text-primary-600 mt-1">Made in Bénin & France</p>
                </div>
              </div>
              <!-- Floating accent -->
              <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-primary-400 rounded-xl opacity-20 blur-sm"></div>
              <div class="absolute -top-4 -left-4 w-16 h-16 bg-primary-400 rounded-full opacity-20 blur-sm"></div>
            </div>

            <!-- Text -->
            <div class="order-1 lg:order-2">
              <span class="inline-block text-sm font-semibold tracking-wider text-primary-600 uppercase mb-3">Qui sommes-nous ?</span>
              <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-6 leading-tight">
                Expert Cosmétique haut de gamme 🇧🇯🇫🇷
              </h2>
              <p class="text-base sm:text-lg text-gray-600 leading-relaxed mb-6">
                Olly Hans Distribution est votre boutique en ligne spécialisée dans la cosmétique haut de gamme.
                Nous proposons la formulation, la vente en kilo &amp; détail, et la création de votre marque de cosmétiques.
                Que vous soyez un particulier ou un professionnel, nous vous accompagnons dans tous vos projets beauté.
              </p>
              <div class="space-y-4">
                <div class="flex items-start gap-3">
                  <div class="flex-shrink-0 w-8 h-8 bg-success-50 rounded-lg flex items-center justify-center mt-0.5">
                    <svg class="w-5 h-5 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                  <p class="text-gray-600">Formulation cosmétique sur mesure pour votre marque</p>
                </div>
                <div class="flex items-start gap-3">
                  <div class="flex-shrink-0 w-8 h-8 bg-success-50 rounded-lg flex items-center justify-center mt-0.5">
                    <svg class="w-5 h-5 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                  <p class="text-gray-600">Vente en kilo &amp; détail pour particuliers et professionnels</p>
                </div>
                <div class="flex items-start gap-3">
                  <div class="flex-shrink-0 w-8 h-8 bg-success-50 rounded-lg flex items-center justify-center mt-0.5">
                    <svg class="w-5 h-5 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                  <p class="text-gray-600">Création de votre marque de cosmétiques clé en main</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Stats -->
      <section class="py-12 sm:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
            <div v-for="stat in stats" :key="stat.label" class="text-center group">
              <div class="inline-flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-primary-50 text-primary-600 mb-4 group-hover:bg-primary-100 transition-colors">
                <component :is="stat.icon" class="w-7 h-7 sm:w-8 sm:h-8" />
              </div>
              <div class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">{{ stat.value }}</div>
              <div class="text-sm sm:text-base text-gray-500 mt-1">{{ stat.label }}</div>
            </div>
          </div>
        </div>
      </section>

      <!-- Values -->
      <section class="py-16 sm:py-20 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12 sm:mb-16">
            <span class="inline-block text-sm font-semibold tracking-wider text-primary-600 uppercase mb-3">Nos Valeurs</span>
            <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">Ce qui nous guide au quotidien</h2>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <div 
              v-for="(value, index) in values" 
              :key="index" 
              class="relative bg-white rounded-2xl p-6 sm:p-8 shadow-md hover:shadow-xl transition-all duration-300 group border border-gray-100 hover:border-primary-200"
            >
              <div 
                class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl flex items-center justify-center mb-5 transition-colors duration-300"
                :class="value.bgColor"
              >
                <component :is="value.icon" class="w-6 h-6 sm:w-7 sm:h-7" :class="value.iconColor" />
              </div>
              <h3 class="font-heading text-lg sm:text-xl font-bold text-gray-900 mb-3">{{ value.title }}</h3>
              <p class="text-sm sm:text-base text-gray-600 leading-relaxed">{{ value.description }}</p>
              <!-- Hover accent line -->
              <div class="absolute bottom-0 left-6 right-6 h-0.5 rounded-full bg-gradient-to-r from-primary-500 to-secondary-500 scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
            </div>
          </div>
        </div>
      </section>

      <!-- How it works -->
      <section class="py-16 sm:py-20 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12 sm:mb-16">
            <span class="inline-block text-sm font-semibold tracking-wider text-primary-600 uppercase mb-3">Comment ça marche</span>
            <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">Simple, rapide et sécurisé</h2>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
            <div v-for="(step, index) in steps" :key="index" class="relative text-center">
              <!-- Step number -->
              <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white font-heading text-2xl sm:text-3xl font-bold mb-6 shadow-lg shadow-primary-500/25">
                {{ index + 1 }}
              </div>
              <!-- Connector line (hidden on mobile & last item) -->
              <div 
                v-if="index < steps.length - 1" 
                class="hidden md:block absolute top-8 sm:top-10 left-[60%] w-[80%] h-0.5 bg-gradient-to-r from-primary-300 to-primary-100"
              ></div>
              <h3 class="font-heading text-lg sm:text-xl font-bold text-gray-900 mb-3">{{ step.title }}</h3>
              <p class="text-sm sm:text-base text-gray-600 leading-relaxed max-w-xs mx-auto">{{ step.description }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Testimonials / Trust -->
      <section class="py-16 sm:py-20 lg:py-24 bg-gradient-to-br from-primary-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12 sm:mb-16">
            <span class="inline-block text-sm font-semibold tracking-wider text-primary-600 uppercase mb-3">Témoignages</span>
            <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">Ils nous font confiance</h2>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <div 
              v-for="(testimonial, index) in testimonials" 
              :key="index" 
              class="bg-white rounded-2xl p-6 sm:p-8 shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100"
            >
              <!-- Stars -->
              <div class="flex gap-1 mb-4">
                <svg v-for="n in 5" :key="n" class="w-5 h-5 text-secondary-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
              </div>
              <p class="text-gray-600 leading-relaxed mb-6 text-sm sm:text-base italic">"{{ testimonial.text }}"</p>
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-sm sm:text-base">
                  {{ testimonial.initials }}
                </div>
                <div>
                  <p class="font-semibold text-gray-900 text-sm sm:text-base">{{ testimonial.name }}</p>
                  <p class="text-xs sm:text-sm text-gray-500">{{ testimonial.role }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CTA -->
      <section class="py-16 sm:py-20 lg:py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <div class="bg-gradient-to-br from-primary-600 to-primary-800 rounded-3xl p-8 sm:p-12 lg:p-16 shadow-2xl shadow-primary-900/20 relative overflow-hidden">
            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            
            <div class="relative">
              <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-4 leading-tight">
                Prêt à sublimer votre peau ?
              </h2>
              <p class="text-primary-100 text-base sm:text-lg max-w-xl mx-auto mb-8">
                Rejoignez Olly Hans Distribution et découvrez la cosmétique haut de gamme. Formulation, vente en kilo &amp; détail, création de votre marque.
              </p>
              <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <router-link 
                  to="/register" 
                  class="inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-white text-primary-700 font-semibold hover:bg-gray-50 transition-colors shadow-lg shadow-black/10 text-sm sm:text-base"
                >
                  Créer un compte
                  <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                  </svg>
                </router-link>
                <router-link 
                  to="/products" 
                  class="inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-white/10 backdrop-blur-sm text-white font-semibold border border-white/20 hover:bg-white/20 transition-colors text-sm sm:text-base"
                >
                  Explorer les produits
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Contact Info -->
      <section class="py-12 sm:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <div class="flex items-start gap-4 p-5 sm:p-6 rounded-xl bg-gray-50 hover:bg-primary-50 transition-colors group">
              <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-primary-100 group-hover:bg-primary-200 flex items-center justify-center transition-colors">
                <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-900 mb-1">Adresse</h3>
                <p class="text-sm sm:text-base text-gray-600">Godomey Abomey-Calavi<br>Benin 229</p>
              </div>
            </div>
            <div class="flex items-start gap-4 p-5 sm:p-6 rounded-xl bg-gray-50 hover:bg-primary-50 transition-colors group">
              <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-primary-100 group-hover:bg-primary-200 flex items-center justify-center transition-colors">
                <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-900 mb-1">Téléphone</h3>
                <a href="tel:+2290151464165" class="text-sm sm:text-base text-gray-600 hover:text-primary-600 transition-colors">+229 01 51 46 41 65</a>
              </div>
            </div>
            <div class="flex items-start gap-4 p-5 sm:p-6 rounded-xl bg-gray-50 hover:bg-primary-50 transition-colors group sm:col-span-2 lg:col-span-1">
              <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-primary-100 group-hover:bg-primary-200 flex items-center justify-center transition-colors">
                <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-900 mb-1">Email</h3>
                <a href="mailto:ollyhanscosmetics@gmail.com" class="text-sm sm:text-base text-gray-600 hover:text-primary-600 transition-colors">ollyhanscosmetics@gmail.com</a>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </AppLayout>
</template>

<script setup>
import { h } from 'vue'
import AppLayout from '@/components/layout/AppLayout.vue'

// ---- Icon components (inline render functions) ----
const IconUsers = (props, { attrs }) => h('svg', { ...attrs, class: attrs.class, fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
])
const IconShoppingBag = (props, { attrs }) => h('svg', { ...attrs, class: attrs.class, fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' })
])
const IconShieldCheck = (props, { attrs }) => h('svg', { ...attrs, class: attrs.class, fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' })
])
const IconStar = (props, { attrs }) => h('svg', { ...attrs, class: attrs.class, fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z' })
])

// Icons for values
const IconHeart = (props, { attrs }) => h('svg', { ...attrs, class: attrs.class, fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' })
])
const IconLightBulb = (props, { attrs }) => h('svg', { ...attrs, class: attrs.class, fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z' })
])
const IconGlobe = (props, { attrs }) => h('svg', { ...attrs, class: attrs.class, fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
])
const IconLock = (props, { attrs }) => h('svg', { ...attrs, class: attrs.class, fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z' })
])
const IconChat = (props, { attrs }) => h('svg', { ...attrs, class: attrs.class, fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z' })
])
const IconRocket = (props, { attrs }) => h('svg', { ...attrs, class: attrs.class, fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M13 10V3L4 14h7v7l9-11h-7z' })
])

// Data
const stats = [
  { value: '500+', label: 'Marques créées', icon: IconStar },
  { value: '200+', label: 'Formules cosmétiques', icon: IconShoppingBag },
  { value: '100%', label: 'Produits haut de gamme', icon: IconShieldCheck },
  { value: '4.8/5', label: 'Satisfaction client', icon: IconUsers },
]

const values = [
  {
    icon: IconHeart,
    title: 'Excellence',
    description: 'Nous sélectionnons les meilleurs ingrédients et formulations pour des cosmétiques haut de gamme qui subliment votre peau.',
    bgColor: 'bg-red-50 group-hover:bg-red-100',
    iconColor: 'text-red-500',
  },
  {
    icon: IconLightBulb,
    title: 'Innovation',
    description: 'Nous créons des formules uniques adaptées aux peaux africaines, alliant savoir-faire français et expertise locale.',
    bgColor: 'bg-primary-50 group-hover:bg-primary-100',
    iconColor: 'text-primary-500',
  },
  {
    icon: IconGlobe,
    title: 'Accessibilité',
    description: 'Du kilo au détail, nous rendons la cosmétique haut de gamme accessible à tous, particuliers comme professionnels.',
    bgColor: 'bg-primary-50 group-hover:bg-primary-100',
    iconColor: 'text-primary-500',
  },
  {
    icon: IconLock,
    title: 'Qualité',
    description: 'Chaque produit est formulé avec des ingrédients soigneusement sélectionnés pour garantir une qualité irréprochable.',
    bgColor: 'bg-success-50 group-hover:bg-success-100',
    iconColor: 'text-success-500',
  },
  {
    icon: IconChat,
    title: 'Accompagnement',
    description: 'Nous vous guidons de la formulation à la création de votre marque, avec un support personnalisé à chaque étape.',
    bgColor: 'bg-info-50 group-hover:bg-info-100',
    iconColor: 'text-info-500',
  },
  {
    icon: IconRocket,
    title: 'Création de marque',
    description: 'Donnez vie à votre vision : nous concevons, formulons et produisons votre marque de cosmétiques clé en main.',
    bgColor: 'bg-purple-50 group-hover:bg-purple-100',
    iconColor: 'text-purple-500',
  },
]

const steps = [
  {
    title: 'Explorez notre gamme',
    description: 'Découvrez notre sélection de cosmétiques haut de gamme, des ingrédients bruts aux produits finis.',
  },
  {
    title: 'Commandez en ligne',
    description: 'Choisissez vos produits en kilo ou détail, passez commande et payez en toute sécurité depuis notre boutique.',
  },
  {
    title: 'Sublimez votre peau',
    description: 'Recevez vos produits et révélez votre éclat. Satisfait ou accompagné pour vos projets de marque !',
  },
]

const testimonials = [
  {
    text: 'Grâce à Olly Hans Distribution, j\'ai pu créer ma propre marque de cosmétiques. Leur accompagnement de la formulation au packaging a été exceptionnel !',
    name: 'Mariam D.',
    role: 'Créatrice de marque',
    initials: 'MD',
  },
  {
    text: 'Les produits sont d\'une qualité incroyable. Ma peau n\'a jamais été aussi belle. Je recommande vivement Olly Hans !',
    name: 'Aïcha T.',
    role: 'Cliente fidèle',
    initials: 'AT',
  },
  {
    text: 'Je suis professionnel et j\'achète mes ingrédients en kilo chez Olly Hans. Qualité constante et service irréprochable.',
    name: 'Benoît K.',
    role: 'Professionnel de la beauté',
    initials: 'BK',
  },
]
</script>
