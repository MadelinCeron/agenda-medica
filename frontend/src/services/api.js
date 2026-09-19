import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

export const obtenerCitas = (params = {}) => api.get('/citas', { params })
export const obtenerCita = (id) => api.get(`/citas/${id}`)
export const crearCita = (data) => api.post('/citas', data)
export const actualizarCita = (id, data) => api.put(`/citas/${id}`, data)
export const cambiarEstadoCita = (id, estado) => api.patch(`/citas/${id}/estado`, { estado })
export const obtenerDoctores = () => api.get('/doctores')
export const obtenerPacientes = () => api.get('/pacientes')

export default api
