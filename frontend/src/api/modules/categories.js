import axios from '../axios'

export const categoriesAPI = {
  getAll() {
    return axios.get('/categories')
  },

  getById(id) {
    return axios.get(`/categories/${id}`)
  },

  getProducts(categoryId, params) {
    return axios.get(`/categories/${categoryId}/products`, { params })
  }
}
