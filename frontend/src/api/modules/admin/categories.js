import axios from '@/api/axios'

export const getCategories = () => {
  return axios.get('/admin/categories')
}

export const getCategory = (id) => {
  return axios.get(`/admin/categories/${id}`)
}

export const createCategory = (data) => {
  return axios.post('/admin/categories', data)
}

export const updateCategory = (id, data) => {
  return axios.put(`/admin/categories/${id}`, data)
}

export const deleteCategory = (id) => {
  return axios.delete(`/admin/categories/${id}`)
}
