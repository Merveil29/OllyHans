import { ref, h, render } from 'vue'
import Toast from '@/components/common/Toast.vue'

const toasts = ref([])

export function useToast() {
  const show = (message, type = 'info', duration = 3000) => {
    const container = document.createElement('div')
    document.body.appendChild(container)

    const toast = {
      id: Date.now(),
      message,
      type,
      duration
    }

    const vnode = h(Toast, {
      ...toast,
      onClose: () => {
        render(null, container)
        document.body.removeChild(container)
      }
    })

    render(vnode, container)
  }

  const success = (message, duration = 3000) => {
    show(message, 'success', duration)
  }

  const error = (message, duration = 4000) => {
    show(message, 'error', duration)
  }

  const warning = (message, duration = 3500) => {
    show(message, 'warning', duration)
  }

  const info = (message, duration = 3000) => {
    show(message, 'info', duration)
  }

  return {
    show,
    success,
    error,
    warning,
    info
  }
}
