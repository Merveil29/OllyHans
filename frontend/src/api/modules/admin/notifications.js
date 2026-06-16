import axios from '@/api/axios'

/**
 * API Admin — Notifications agrégées (sans nouvelle table)
 */

/** Compteurs rapides pour le badge de l'en-tête */
export const getNotificationStats = () => axios.get('/admin/notifications/stats')

/**
 * Feed paginé de notifications
 * @param {Object} params - { type: 'all'|'product'|'sponsor'|'ticket', page, per_page }
 */
export const getNotifications = (params = {}) =>
  axios.get('/admin/notifications', { params })
