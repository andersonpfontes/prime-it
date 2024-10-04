<script setup>
import { ref, onMounted } from 'vue'
import SectionMain from '@/components/SectionMain.vue'
import CardBox from '@/components/CardBox.vue'
import apiClient from '@/services/http'
import Cookies from 'js-cookie'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'

const appointments = ref([])

// Função para buscar os agendamentos do cliente
const fetchAppointments = async () => {
  const token = Cookies.get('access_token')

  try {
    const response = await apiClient.get('appointments/my', {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      }
    })

    if (response.data.success) {
      appointments.value = response.data.data
    }
  } catch (error) {
    console.error('Erro ao buscar agendamentos:', error)
  }
}

onMounted(() => {
  fetchAppointments()
})
</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
    <h2 class="text-xl font-bold mb-4">Meus Agendamentos</h2>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <CardBox v-for="appointment in appointments" :key="appointment.id">
        <h3 class="text-lg font-semibold">{{ appointment.animal_name }} ({{ appointment.animal_type }})</h3>
        <p><strong>Data:</strong> {{ appointment.appointment_date }}</p>
        <p><strong>Período:</strong> {{ appointment.period === 'morning' ? 'Manhã' : 'Tarde' }}</p>
        <p><strong>Sintomas:</strong> {{ appointment.symptoms }}</p>
      </CardBox>
    </div>
  </SectionMain>
  </LayoutAuthenticated>
</template>
