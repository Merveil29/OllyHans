<template>
  <div class="min-h-screen bg-gradient-to-br from-black via-gray-900 to-black flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Logo et Titre -->
      <div class="text-center">
        <router-link to="/" class="inline-block">
          <img src="/images/logo.png" alt="Olly Hans" class="h-16 mx-auto" />
        </router-link>
        <h2 class="mt-6 text-3xl font-bold text-white">
          Vérification de votre email
        </h2>
        <p class="mt-2 text-sm text-gray-400">
          Un code à 6 chiffres a été envoyé à<br />
          <span class="font-semibold text-primary-600">{{ email }}</span>
        </p>
      </div>

      <!-- Formulaire OTP -->
      <div class="bg-white rounded-lg shadow-xl p-8 space-y-6">
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
            </div>
          </div>
        </div>

        <!-- Succès -->
        <div v-if="successMessage" class="bg-success-50 border-l-4 border-success-500 p-4 rounded">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-success-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm text-success-700">{{ successMessage }}</p>
            </div>
          </div>
        </div>

        <!-- Champs OTP -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-3 text-center">
            Entrez le code de vérification
          </label>
          <div class="flex justify-center gap-2 sm:gap-3">
            <input
              v-for="(digit, index) in otp"
              :key="index"
              :ref="el => otpInputs[index] = el"
              v-model="otp[index]"
              type="text"
              maxlength="1"
              inputmode="numeric"
              pattern="[0-9]"
              @input="handleInput(index, $event)"
              @keydown="handleKeydown(index, $event)"
              @paste="handlePaste"
              class="w-12 h-14 sm:w-14 sm:h-16 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all"
              :class="{ 'border-error-500': errorMessage }"
            />
          </div>
        </div>

        <!-- Timer et Renvoyer -->
        <div class="text-center space-y-2">
          <p v-if="timeRemaining > 0" class="text-sm text-gray-600">
            Code expire dans <span class="font-semibold text-primary-600">{{ formatTime(timeRemaining) }}</span>
          </p>
          <p v-else class="text-sm text-error-600 font-semibold">
            Code expiré
          </p>
          
          <button
            v-if="timeRemaining === 0"
            @click="resendCode"
            :disabled="loading"
            class="text-sm text-primary-600 hover:text-primary-700 font-medium disabled:opacity-50"
          >
            Renvoyer un nouveau code
          </button>
        </div>

        <!-- Bouton Vérifier -->
        <button
          @click="handleVerify"
          :disabled="loading || !isOtpComplete || timeRemaining === 0"
          class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
        >
          <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ loading ? 'Vérification...' : 'Vérifier le code' }}
        </button>

        <!-- Retour -->
        <div class="text-center">
          <router-link to="/register" class="text-sm text-gray-400 hover:text-primary-400 transition-colors">
            ← Retour à l'inscription
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const email = ref(route.query.email || '')
const otp = ref(['', '', '', '', '', ''])
const otpInputs = ref([])
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const timeRemaining = ref(600) // 10 minutes en secondes
let timer = null

const isOtpComplete = computed(() => {
  return otp.value.every(digit => digit !== '')
})

const formatTime = (seconds) => {
  const minutes = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${minutes}:${secs.toString().padStart(2, '0')}`
}

const startTimer = () => {
  timer = setInterval(() => {
    if (timeRemaining.value > 0) {
      timeRemaining.value--
    } else {
      clearInterval(timer)
    }
  }, 1000)
}

const handleInput = (index, event) => {
  const value = event.target.value
  
  // Autoriser seulement les chiffres
  if (!/^\d*$/.test(value)) {
    otp.value[index] = ''
    return
  }

  // Passer au champ suivant si une valeur est entrée
  if (value && index < 5) {
    otpInputs.value[index + 1]?.focus()
  }

  errorMessage.value = ''
}

const handleKeydown = (index, event) => {
  // Retour arrière : passer au champ précédent
  if (event.key === 'Backspace' && !otp.value[index] && index > 0) {
    otpInputs.value[index - 1]?.focus()
  }
}

const handlePaste = (event) => {
  event.preventDefault()
  const pasteData = event.clipboardData.getData('text').trim()
  
  if (/^\d{6}$/.test(pasteData)) {
    otp.value = pasteData.split('')
    otpInputs.value[5]?.focus()
  }
}

const handleVerify = async () => {
  if (!isOtpComplete.value) return

  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  const otpCode = otp.value.join('')

  try {
    await authStore.verifyOtp(email.value, otpCode)
    
    successMessage.value = 'Email vérifié avec succès ! Redirection...'
    
    setTimeout(() => {
      router.push('/')
    }, 1500)
  } catch (error) {
    console.error('OTP verification failed:', error)
    console.error('Response data:', error.response?.data)
    errorMessage.value = error.response?.data?.message || 'Code invalide ou expiré'
    // Réinitialiser les champs
    otp.value = ['', '', '', '', '', '']
    otpInputs.value[0]?.focus()
  } finally {
    loading.value = false
  }
}

const resendCode = async () => {
  // TODO: Implémenter l'API pour renvoyer le code
  timeRemaining.value = 600
  startTimer()
  errorMessage.value = ''
  successMessage.value = 'Un nouveau code a été envoyé à votre email'
  setTimeout(() => {
    successMessage.value = ''
  }, 3000)
}

onMounted(() => {
  if (!email.value) {
    router.push('/register')
    return
  }
  
  otpInputs.value[0]?.focus()
  startTimer()
})

onUnmounted(() => {
  if (timer) {
    clearInterval(timer)
  }
})
</script>

<style scoped>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type=number] {
  -moz-appearance: textfield;
}
</style>
