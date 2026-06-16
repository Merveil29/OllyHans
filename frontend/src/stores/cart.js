import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

const CART_STORAGE_KEY = 'shop_cart'

function loadCart() {
  try {
    const data = localStorage.getItem(CART_STORAGE_KEY)
    return data ? JSON.parse(data) : []
  } catch {
    return []
  }
}

export const useCartStore = defineStore('cart', () => {
  const items = ref(loadCart())

  const totalItems = computed(() =>
    items.value.reduce((sum, item) => sum + item.quantity, 0)
  )

  const totalPrice = computed(() =>
    items.value.reduce((sum, item) => sum + item.price * item.quantity, 0)
  )

  const isEmpty = computed(() => items.value.length === 0)

  function save() {
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(items.value))
  }

  function addItem(product, quantity = 1) {
    const existing = items.value.find(i => i.id === product.id_produits)
    if (existing) {
      existing.quantity += quantity
    } else {
      items.value.push({
        id: product.id_produits,
        name: product.nom_produits,
        price: parseFloat(product.prix_produits) || 0,
        image: product.image_produits || '',
        quantity,
      })
    }
    save()
  }

  function updateQuantity(productId, quantity) {
    const item = items.value.find(i => i.id === productId)
    if (item) {
      if (quantity <= 0) {
        removeItem(productId)
      } else {
        item.quantity = quantity
        save()
      }
    }
  }

  function removeItem(productId) {
    items.value = items.value.filter(i => i.id !== productId)
    save()
  }

  function clearCart() {
    items.value = []
    save()
  }

  function getWhatsAppMessage(phone) {
    if (items.value.length === 0) return ''

    let message = '*Nouvelle commande Olly Hans Distribution*\n\n'
    message += '*Articles commandes :*\n'
    items.value.forEach((item, index) => {
      message += `${index + 1}. *${item.name}* x ${item.quantity} = ${(item.price * item.quantity).toLocaleString('fr-FR')} FCFA\n`
    })
    message += `\n---\n`
    message += `*Total : ${totalPrice.value.toLocaleString('fr-FR')} FCFA*\n\n`
    message += 'Merci de me contacter pour finaliser la commande.'

    const cleaned = phone.replace(/\D/g, '')
    return `https://wa.me/${cleaned}?text=${encodeURIComponent(message)}`
  }

  return {
    items,
    totalItems,
    totalPrice,
    isEmpty,
    addItem,
    updateQuantity,
    removeItem,
    clearCart,
    getWhatsAppMessage,
  }
})
