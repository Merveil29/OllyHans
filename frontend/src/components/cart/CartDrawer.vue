<template>
  <Teleport to="body">
    <Transition name="cart-overlay">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50"
      >
        <div
          class="absolute inset-0 bg-black/50 backdrop-blur-sm"
          @click="$emit('update:modelValue', false)"
        />
        <div class="absolute top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col transform transition-transform duration-300"
             :class="modelValue ? 'translate-x-0' : 'translate-x-full'">
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-primary-600 to-secondary-600">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              Mon Panier
              <span class="text-sm font-normal text-white/70">({{ cartStore.totalItems }} article{{ cartStore.totalItems > 1 ? 's' : '' }})</span>
            </h2>
            <button
              @click="$emit('update:modelValue', false)"
              class="text-white/80 hover:text-white transition-colors"
            >
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div v-if="cartStore.isEmpty" class="flex-1 flex flex-col items-center justify-center text-gray-500 p-8">
            <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="text-lg font-medium mb-2">Votre panier est vide</p>
            <p class="text-sm text-center">Ajoutez des produits depuis la boutique pour commencer</p>
          </div>

          <div v-else class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
            <div
              v-for="item in cartStore.items"
              :key="item.id"
              class="flex gap-4 bg-gray-50 rounded-xl p-4 border border-gray-100"
            >
              <img
                :src="item.image || '/images/default-product.png'"
                :alt="item.name"
                class="w-20 h-20 object-contain rounded-lg bg-white border border-gray-200 flex-shrink-0"
                @error="e => e.target.src = '/images/default-product.png'"
              />
              <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 text-sm truncate">{{ item.name }}</h3>
                <p class="text-primary-600 font-bold mt-1">{{ (item.price * item.quantity).toLocaleString('fr-FR') }} FCFA</p>
                <div class="flex items-center gap-3 mt-2">
                  <div class="flex items-center border border-gray-300 rounded-lg">
                    <button
                      @click="cartStore.updateQuantity(item.id, item.quantity - 1)"
                      class="px-2.5 py-1 text-gray-600 hover:text-primary-600 hover:bg-gray-100 transition-colors rounded-l-lg"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                      </svg>
                    </button>
                    <span class="px-3 py-1 font-semibold text-sm min-w-[2rem] text-center border-x border-gray-300">{{ item.quantity }}</span>
                    <button
                      @click="cartStore.updateQuantity(item.id, item.quantity + 1)"
                      class="px-2.5 py-1 text-gray-600 hover:text-primary-600 hover:bg-gray-100 transition-colors rounded-r-lg"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                    </button>
                  </div>
                  <button
                    @click="cartStore.removeItem(item.id)"
                    class="text-red-400 hover:text-red-600 transition-colors p-1"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div v-if="!cartStore.isEmpty" class="border-t border-gray-200 px-6 py-4 space-y-3 bg-gray-50">
            <div class="flex items-center justify-between text-lg font-bold text-gray-900">
              <span>Total</span>
              <span class="text-primary-600">{{ cartStore.totalPrice.toLocaleString('fr-FR') }} FCFA</span>
            </div>

            <button
              v-if="whatsappNumber"
              @click="orderViaWhatsApp"
              class="w-full flex items-center justify-center gap-3 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
            >
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
              </svg>
              Commander via WhatsApp
            </button>
            <p v-else class="text-center text-sm text-gray-500">
              Numero WhatsApp non configure
            </p>

            <button
              @click="cartStore.clearCart()"
              class="w-full text-center text-sm text-gray-500 hover:text-red-500 transition-colors py-2"
            >
              Vider le panier
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue'
import { useCartStore } from '@/stores/cart'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  whatsappNumber: { type: String, default: '' },
})

defineEmits(['update:modelValue'])

const cartStore = useCartStore()

const adminPhone = computed(() => {
  return props.whatsappNumber || '+221771234567'
})

function orderViaWhatsApp() {
  const url = cartStore.getWhatsAppMessage(adminPhone.value)
  if (url) {
    window.open(url, '_blank')
  }
}
</script>
