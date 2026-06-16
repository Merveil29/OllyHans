import axios from '@/api/axios'

/**
 * API Admin - Dashboard & Statistiques globales
 */

/** Statistiques globales (clients, produits, tickets, sponsors, blog) */
export const getDashboard = () => axios.get('/admin/dashboard')

/** Statistiques détaillées (top clients, jetons) */
export const getDashboardStats = () => axios.get('/admin/stats')
