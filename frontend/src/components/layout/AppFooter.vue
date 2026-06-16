<template>
  <footer class="bg-gray-900 text-white">
    <!-- Main Footer -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Réseaux Sociaux -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Réseaux Sociaux</h3>
          <div class="mb-5">
            <router-link to="/">
              <img src="/images/logo-sm.png" alt="Olly Hans Distribution" class="h-12 w-auto" />
            </router-link>
          </div>
          <div class="flex space-x-4">
            <a 
              href="https://www.instagram.com/ollyhansdistribution" 
              target="_blank"
              rel="noopener noreferrer"
              class="text-gray-400 hover:text-white transition-colors"
              aria-label="Instagram"
            >
              <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
              </svg>
            </a>
            <a 
              href="https://www.facebook.com/ollyhansdistribution" 
              target="_blank"
              rel="noopener noreferrer"
              class="text-gray-400 hover:text-white transition-colors"
              aria-label="Facebook"
            >
              <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </a>
            <a 
              href="https://wa.me/+2290151464165" 
              target="_blank"
              rel="noopener noreferrer"
              class="text-gray-400 hover:text-white transition-colors"
              aria-label="WhatsApp"
            >
              <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
              </svg>
            </a>
          </div>
        </div>

        <!-- Newsletter -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Newsletter</h3>
          <p class="text-gray-400 text-sm mb-4">
            Recevez les dernières annonces de produits et articles de blog.
          </p>

          <!-- Message de succès -->
          <div v-if="newsletterSuccess" class="mb-3 p-3 bg-green-600/20 border border-green-500 rounded-lg text-green-400 text-sm">
            {{ newsletterSuccess }}
          </div>

          <!-- Message d'erreur -->
          <div v-if="newsletterError" class="mb-3 p-3 bg-red-600/20 border border-red-500 rounded-lg text-red-400 text-sm">
            {{ newsletterError }}
          </div>

          <form v-if="!newsletterSuccess" @submit.prevent="subscribeNewsletter" class="space-y-3">
            <input
              v-model="newsletterEmail"
              type="email"
              placeholder="Votre adresse email"
              required
              class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
            />
            <input
              v-model="newsletterNom"
              type="text"
              placeholder="Votre nom (optionnel)"
              class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
            />
            <div class="flex items-center gap-4 text-sm text-gray-400">
              <label class="flex items-center gap-1.5 cursor-pointer">
                <input v-model="notifyProducts" type="checkbox" class="accent-primary-500" />
                Produits
              </label>
              <label class="flex items-center gap-1.5 cursor-pointer">
                <input v-model="notifyBlog" type="checkbox" class="accent-primary-500" />
                Blog
              </label>
            </div>
            <button
              type="submit"
              :disabled="newsletterLoading"
              class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg text-sm font-medium transition-colors"
            >
              <span v-if="newsletterLoading">Inscription...</span>
              <span v-else>S'abonner</span>
            </button>
          </form>
        </div>

        <!-- Navigation -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Navigation</h3>
          <ul class="space-y-2">
            <li>
              <router-link to="/" class="text-gray-400 hover:text-white transition-colors">
                Accueil
              </router-link>
            </li>
            <li>
              <router-link to="/products" class="text-gray-400 hover:text-white transition-colors">
                Shopping
              </router-link>
            </li>
            <li>
              <router-link to="/about" class="text-gray-400 hover:text-white transition-colors">
                À propos
              </router-link>
            </li>
            <li>
              <router-link to="/contact" class="text-gray-400 hover:text-white transition-colors">
                Contactez-nous
              </router-link>
            </li>
            <li>
              <router-link to="/help" class="text-gray-400 hover:text-white transition-colors">
                Aide
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Localisation -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Localisation</h3>
          <ul class="space-y-3 text-gray-400">
            <li class="flex items-start">
              <svg class="w-5 h-5 mr-2 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <address class="not-italic">
                Godomey Abomey-Calavi<br>
                Benin 229
              </address>
            </li>
            <li>
              <a href="tel:+22995145050" class="flex items-center hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                +229 01 51 46 41 65
              </a>
            </li>
            <li>
              <a href="mailto:ollyhanscosmetics@gmail.com" class="flex items-center hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                ollyhanscosmetics@gmail.com
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Bottom Footer -->
    <div class="border-t border-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
          <p class="text-gray-400 text-sm text-center md:text-left">
            © {{ currentYear }} 
            <a href="https://ollyhansdistribution.com" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors">
              Olly Hans Distribution
            </a>
            . Tous droits réservés
          </p>
          <div class="flex items-center space-x-3">
            <span class="text-gray-400 text-sm">Paiements sécurisés</span>
            <div class="flex items-center space-x-2">
              <!-- Visa -->
              <svg class="h-8 w-12" viewBox="0 0 48 32" fill="none">
                <rect width="48" height="32" rx="4" fill="#1A1F71"/>
                <path d="M19.5 11L16.5 21H14L12 13.5C11.9 13.1 11.7 12.8 11.4 12.6C10.7 12.2 9.7 11.9 8.8 11.7L8.9 11H13.5C14.1 11 14.6 11.4 14.7 12L15.5 16.5L17.5 11H19.5ZM27 17C27 19 25.2 20 23.5 20C21.8 20 21 19 21 18C21 16.5 22.5 15.5 24.5 15.5C25.2 15.5 25.8 15.6 26.3 15.8L26.5 14.5C25.9 14.3 25.2 14 24.5 14C22.5 14 20.5 15 20.5 17.5C20.5 19.5 22.5 21 24.5 21C26.5 21 27.5 20 27.5 18.5C27.5 17.5 27 17 27 17ZM35 21L33.5 11H32L28 21H30L30.5 19.5H33L33.3 21H35ZM31 18L32 14L32.5 18H31ZM26 11L24.5 21H22.5L24 11H26Z" fill="white"/>
              </svg>
              <!-- Mastercard -->
              <svg class="h-8 w-12" viewBox="0 0 48 32" fill="none">
                <rect width="48" height="32" rx="4" fill="#252525"/>
                <circle cx="18" cy="16" r="8" fill="#EB001B"/>
                <circle cx="30" cy="16" r="8" fill="#F79E1B"/>
                <path d="M24 10C25.5 11.2 26.5 13 26.5 15C26.5 17 25.5 18.8 24 20C22.5 18.8 21.5 17 21.5 15C21.5 13 22.5 11.2 24 10Z" fill="#FF5F00"/>
              </svg>
              <!-- Mobile Money -->
              <div class="h-8 px-3 bg-success-600 rounded flex items-center">
                <span class="text-white text-xs font-bold">Mobile Money</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref, computed } from 'vue'
import { newsletterAPI } from '@/api/modules/newsletter'

const currentYear = computed(() => new Date().getFullYear())

// Newsletter
const newsletterEmail = ref('')
const newsletterNom = ref('')
const notifyProducts = ref(true)
const notifyBlog = ref(true)
const newsletterLoading = ref(false)
const newsletterSuccess = ref('')
const newsletterError = ref('')

async function subscribeNewsletter() {
  newsletterLoading.value = true
  newsletterError.value = ''
  newsletterSuccess.value = ''

  try {
    const response = await newsletterAPI.subscribe({
      email: newsletterEmail.value,
      nom: newsletterNom.value || undefined,
      notify_products: notifyProducts.value,
      notify_blog: notifyBlog.value
    })
    newsletterSuccess.value = response.message || 'Inscription réussie !'
    newsletterEmail.value = ''
    newsletterNom.value = ''
  } catch (error) {
    const data = error.response?.data
    if (data?.errors) {
      newsletterError.value = Object.values(data.errors).flat().join(', ')
    } else {
      newsletterError.value = data?.message || 'Une erreur est survenue. Réessayez.'
    }
  } finally {
    newsletterLoading.value = false
  }
}
</script>
