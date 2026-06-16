<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.self="cancel"
      >
        <div
          class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all"
          @click.stop
        >
          <!-- Header -->
          <div class="flex items-start gap-4 p-6 border-b border-gray-200">
            <!-- Icon -->
            <div
              class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center"
              :class="typeConfig[type].bgClass"
            >
              <svg class="w-6 h-6" :class="typeConfig[type].iconClass" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path v-if="type === 'danger'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                <path v-else-if="type === 'warning'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>

            <!-- Title & Message -->
            <div class="flex-1 min-w-0">
              <h3 class="text-lg font-semibold text-gray-900 mb-1">
                {{ title }}
              </h3>
              <p class="text-sm text-gray-600">
                {{ message }}
              </p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex flex-col-reverse sm:flex-row gap-3 p-6 bg-gray-50">
            <button
              @click="cancel"
              class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
            >
              {{ cancelText }}
            </button>
            <button
              @click="confirm"
              class="flex-1 px-4 py-2.5 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors"
              :class="typeConfig[type].btnClass"
            >
              {{ confirmText }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  title: {
    type: String,
    default: 'Confirmation'
  },
  message: {
    type: String,
    required: true
  },
  confirmText: {
    type: String,
    default: 'Confirmer'
  },
  cancelText: {
    type: String,
    default: 'Annuler'
  },
  type: {
    type: String,
    default: 'warning',
    validator: (value) => ['danger', 'warning', 'info'].includes(value)
  }
})

const emit = defineEmits(['confirm', 'cancel'])

const show = ref(true)

const typeConfig = {
  danger: {
    bgClass: 'bg-error-100',
    iconClass: 'text-error-600',
    btnClass: 'bg-error-600 hover:bg-error-700 focus:ring-error-500'
  },
  warning: {
    bgClass: 'bg-warning-100',
    iconClass: 'text-warning-600',
    btnClass: 'bg-warning-600 hover:bg-warning-700 focus:ring-warning-500'
  },
  info: {
    bgClass: 'bg-primary-100',
    iconClass: 'text-primary-600',
    btnClass: 'bg-primary-600 hover:bg-primary-700 focus:ring-primary-500'
  }
}

const confirm = () => {
  show.value = false
  setTimeout(() => {
    emit('confirm')
  }, 200)
}

const cancel = () => {
  show.value = false
  setTimeout(() => {
    emit('cancel')
  }, 200)
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-active > div,
.modal-leave-active > div {
  transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from > div {
  transform: scale(0.9) translateY(-20px);
}

.modal-leave-to > div {
  transform: scale(0.9) translateY(20px);
}
</style>
