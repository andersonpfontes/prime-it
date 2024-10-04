import { defineStore } from 'pinia'
import apiClient from '@/services/http'
import Cookies from 'js-cookie'

export const useAppointmentsStore = defineStore('appointments', {
  state: () => ({
    appointments: [],
  }),

  actions: {
    async fetchAppointments() {
      const token = Cookies.get('access_token')

      try {
        const response = await apiClient.get('veterinarian/appointments', {
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: 'application/json',
          },
        })

        if (response.data.success) {
          this.appointments = response.data.data
        }
      } catch (error) {
        console.error('Erro ao buscar as marcações:', error)
      }
    },

  },
})
