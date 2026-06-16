import { h, render } from 'vue'
import ConfirmModal from '@/components/common/ConfirmModal.vue'

export function useConfirm() {
  const confirm = (options) => {
    return new Promise((resolve) => {
      const container = document.createElement('div')
      document.body.appendChild(container)

      const defaultOptions = {
        title: 'Confirmation',
        message: 'Êtes-vous sûr de vouloir continuer ?',
        confirmText: 'Confirmer',
        cancelText: 'Annuler',
        type: 'warning'
      }

      const mergedOptions = { ...defaultOptions, ...options }

      const cleanup = () => {
        render(null, container)
        document.body.removeChild(container)
      }

      const vnode = h(ConfirmModal, {
        ...mergedOptions,
        onConfirm: () => {
          cleanup()
          resolve(true)
        },
        onCancel: () => {
          cleanup()
          resolve(false)
        }
      })

      render(vnode, container)
    })
  }

  return {
    confirm
  }
}
