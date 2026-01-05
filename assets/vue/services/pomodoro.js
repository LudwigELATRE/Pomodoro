import api from './api'

export const pomodoroService = {
  async createSession(session) {
    const response = await api.post('/pomodoros', session)
    return response.data
  },

  async getSessions() {
    const response = await api.get('/pomodoros')
    return response.data
  },

  async getTodaySessions() {
    const response = await api.get('/pomodoros/today')
    return response.data
  },

  async updateSession(id, data) {
    const response = await api.put(`/api/pomodoros/${id}`, data)
    return response.data
  },

  async deleteSession(id) {
    const response = await api.delete(`/api/pomodoros/${id}`)
    return response.data
  },

  async getStatistics() {
    const response = await api.get('/statistics')
    return response.data
  },

  async getSettings() {
    const response = await api.get('/settings')
    return response.data
  },

  async updateSettings(settings) {
    const response = await api.put('/settings', settings)
    return response.data
  },
}
