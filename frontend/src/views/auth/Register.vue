<template>
  <div class="min-h-screen bg-gradient-to-br from-black via-gray-900 to-black flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Logo et Titre -->
      <div class="text-center">
        <router-link to="/" class="inline-block">
          <img src="/images/logo.png" alt="Olly Hans" class="h-16 mx-auto" />
        </router-link>
        <h2 class="mt-6 text-3xl font-bold text-white">
          Créer un compte
        </h2>
        <p class="mt-2 text-sm text-gray-400">
          Rejoignez Olly Hans Distribution et commencez à vendre
        </p>
      </div>

      <!-- Formulaire -->
      <form @submit.prevent="handleSubmit" class="mt-8 space-y-6 bg-white rounded-lg shadow-xl p-8">
        <!-- Alerte d'erreur -->
        <div v-if="errorMessage" class="bg-error-50 border-l-4 border-error-500 p-4 rounded">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-error-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm text-error-700">{{ errorMessage }}</p>
              <ul v-if="validationErrors" class="mt-2 text-sm text-error-600 list-disc list-inside">
                <li v-for="(errors, field) in validationErrors" :key="field">
                  {{ errors[0] }}
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Nom et Prénom -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <div>
            <label for="nom" class="block text-sm font-medium text-gray-700">
              Nom <span class="text-error-500">*</span>
            </label>
            <input
              id="nom"
              v-model="form.nom"
              type="text"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              :class="{ 'border-error-500': validationErrors?.nom }"
              placeholder="Dupont"
            />
          </div>
          <div>
            <label for="prenom" class="block text-sm font-medium text-gray-700">
              Prénom <span class="text-error-500">*</span>
            </label>
            <input
              id="prenom"
              v-model="form.prenom"
              type="text"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              :class="{ 'border-error-500': validationErrors?.prenom }"
              placeholder="Jean"
            />
          </div>
        </div>

        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">
            Email <span class="text-error-500">*</span>
          </label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
            :class="{ 'border-error-500': validationErrors?.email }"
            placeholder="email@gmail.com"
          />
          <p class="mt-1 text-xs text-gray-500">
            Utilisez un email valide avec un vrai domaine (gmail.com, yahoo.com, hotmail.com, etc.)
          </p>
          <p v-if="validationErrors?.email" class="mt-1 text-sm text-error-600">
            {{ validationErrors.email[0] }}
          </p>
        </div>

        <!-- Login -->
        <div>
          <label for="login" class="block text-sm font-medium text-gray-700">
            Nom d'utilisateur <span class="text-error-500">*</span>
          </label>
          <input
            id="login"
            v-model="form.login"
            type="text"
            required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
            :class="{ 'border-error-500': validationErrors?.login }"
            placeholder="jeandupont (min 4 caractères)"
          />
          <p v-if="validationErrors?.login" class="mt-1 text-sm text-error-600">
            {{ validationErrors.login[0] }}
          </p>
        </div>

        <!-- Téléphone -->
        <div>
          <label for="telephone" class="block text-sm font-medium text-gray-700">
            Téléphone <span class="text-error-500">*</span>
          </label>
          <input
            id="telephone"
            v-model="form.telephone"
            type="tel"
            required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
            :class="{ 'border-error-500': validationErrors?.telephone }"
            placeholder="+221771234567"
          />
          <p v-if="validationErrors?.telephone" class="mt-1 text-sm text-error-600">
            {{ validationErrors.telephone[0] }}
          </p>
        </div>

        <!-- Adresse -->
        <div>
          <label for="adresse" class="block text-sm font-medium text-gray-700">
            Adresse <span class="text-error-500">*</span>
          </label>
          <textarea
            id="adresse"
            v-model="form.adresse"
            required
            rows="2"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
            :class="{ 'border-error-500': validationErrors?.adresse }"
            placeholder="123 Rue Example, Ville"
          ></textarea>
        </div>

        <!-- Mot de passe -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">
            Mot de passe <span class="text-error-500">*</span>
          </label>
          <div class="relative">
            <input
              id="password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              required
              class="mt-1 block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              :class="{ 'border-error-500': validationErrors?.password }"
              placeholder="••••••••"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute inset-y-0 right-0 flex items-center pr-3 mt-1"
            >
              <svg v-if="!showPassword" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <svg v-else class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
              </svg>
            </button>
          </div>
          <p class="mt-1 text-xs text-gray-500">
            Min 8 caractères, majuscules, minuscules, chiffres et symboles
          </p>
          <ul v-if="validationErrors?.password" class="mt-1 text-sm text-error-600 list-disc list-inside">
            <li v-for="(error, index) in validationErrors.password" :key="index">
              {{ error }}
            </li>
          </ul>
        </div>

        <!-- Confirmation mot de passe -->
        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
            Confirmer le mot de passe <span class="text-error-500">*</span>
          </label>
           <div class="relative">
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            :type="showPassword ? 'text' : 'password'"
            required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
            :class="{ 'border-error-500': validationErrors?.password_confirmation }"
            placeholder="••••••••"
          />
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 mt-1">
                        <svg v-if="!showPassword" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg v-else class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Min 8 caractères, majuscules, minuscules, chiffres et symboles
                </p>
                <ul v-if="validationErrors?.password_confirmation" class="mt-1 text-sm text-error-600 list-disc list-inside">
                    <li v-for="(error, index) in validationErrors.password_confirmation" :key="index">
                        {{ error }}
                    </li>
                </ul>
        </div>

        <!-- Bouton Submit -->
        <div>
          <button
            type="submit"
            :disabled="loading"
            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loading ? 'Inscription en cours...' : 'S\'inscrire' }}
          </button>
        </div>

        <!-- Lien connexion -->
        <div class="text-center">
          <p class="text-sm text-gray-600">
            Vous avez déjà un compte ?
            <router-link to="/login" class="font-medium text-primary-600 hover:text-primary-500 transition-colors">
              Se connecter
            </router-link>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const showPassword = ref(false)
const errorMessage = ref('')
const validationErrors = ref(null)

const form = reactive({
  nom: '',
  prenom: '',
  email: '',
  login: '',
  telephone: '',
  adresse: '',
  password: '',
  password_confirmation: '',
})

const handleSubmit = async () => {
  loading.value = true
  errorMessage.value = ''
  validationErrors.value = null

  try {
    await authStore.register(form)
    
    // Rediriger vers la page OTP avec l'email
    router.push({
      name: 'verify-otp',
      query: { email: form.email }
    })
  } catch (error) {
    console.error('Erreur d\'inscription:', error)
    
    // L'erreur peut être soit error.response (axios) soit error directement
    const errorData = error.response || error
    
    if (errorData.status === 422 || errorData.data?.errors) {
      validationErrors.value = errorData.data?.errors || {}
      errorMessage.value = errorData.data?.message || 'Erreur de validation'
    } else if (error.code === 'ERR_NETWORK') {
      errorMessage.value = 'Impossible de se connecter au serveur. Vérifiez que le backend est démarré sur http://localhost:8000'
    } else {
      errorMessage.value = errorData.data?.message || errorData.message || 'Une erreur est survenue lors de l\'inscription'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Animations personnalisées si nécessaire */
</style>
