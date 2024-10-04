<script setup>
import { ref, onMounted } from 'vue'
import SectionMain from '@/components/SectionMain.vue'
import CardBox from '@/components/CardBox.vue'
import apiClient from '@/services/http'
import Cookies from 'js-cookie'
import BaseButton from '@/components/BaseButton.vue'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'
import EditAppointmentModal from '@/views/Appointment/EditAppointmentModal.vue'
import AssignDoctorModal from '@/views/Doctor/AssignDoctorsView.vue'
import DeleteModal from '@/views/Appointment/DeleteModal.vue'

const appointmentsToday = ref(0)
const upcomingAppointments = ref(0)
const appointments = ref([])
const editModalRef = ref(null)
const assignDoctorModalRef = ref(null)
const deleteModalRef = ref(null)

// Função para buscar marcações
const fetchAppointments = async () => {
  const token = Cookies.get('access_token')

  try {
    const response = await apiClient.get('appointments', {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      },
    })

    if (response.data.success) {
      const today = new Date().toISOString().slice(0, 10) // Data de hoje no formato AAAA-MM-DD
      appointments.value = response.data.data

      // Filtrar consultas de hoje
      appointmentsToday.value = appointments.value.filter(app => app.appointment_date === today).length
      // Filtrar consultas futuras
      upcomingAppointments.value = appointments.value.filter(app => app.appointment_date > today).length
    }
  } catch (error) {
    console.error('Erro ao buscar marcações:', error)
  }
}

// Função para abrir a modal de edição
const openEditModal = (appointment) => {
  console.log("Abrindo modal para editar marcação:", appointment)
  editModalRef.value.openModal(appointment)
}

// Função para abrir a modal de atribuição de médico
const openAssignDoctorModal = (appointment) => {
  assignDoctorModalRef.value.openModal(appointment)
}

// Função para atualizar a lista de marcações após edição
const handleUpdateSuccess = () => {
  console.log("Atualização da consulta foi bem-sucedida, recarregando marcações...")
  fetchAppointments()
}

// Função para atualizar a lista de marcações após atribuição de médico
const handleDoctorAssigned = () => {
  fetchAppointments()
}

// Função para apagar uma marcação
const deleteAppointment = async (appointmentId) => {
  const token = Cookies.get('access_token')

  try {
    const response = await apiClient.delete(`appointments/${appointmentId}`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      }
    })

    if (response.data.success) {
      alert('Marcação apagada com sucesso!')
      fetchAppointments() // Atualizar a lista de marcações
    }
  } catch (error) {
    console.error('Erro ao apagar marcação:', error)
  }
}

onMounted(() => {
  fetchAppointments()
})
</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
      <h2 class="text-xl font-bold mb-4">Gestão de Marcações</h2>

      <!-- Indicadores -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-6">
        <CardBox>
          <h3 class="text-lg font-semibold">Consultas Agendadas Hoje</h3>
          <p class="text-3xl font-bold">{{ appointmentsToday }}</p>
        </CardBox>

        <CardBox>
          <h3 class="text-lg font-semibold">Próximas Consultas</h3>
          <p class="text-3xl font-bold">{{ upcomingAppointments }}</p>
        </CardBox>
      </div>

      <!-- Grid de marcações -->
      <div class="grid grid-cols-1 gap-6">
        <CardBox>
          <table class="w-full table-auto">
            <thead>
            <tr>
              <th class="text-left px-4 py-2">Nome do Animal</th>
              <th class="text-left px-4 py-2">Tipo</th>
              <th class="text-left px-4 py-2">Data</th>
              <th class="text-left px-4 py-2">Sintomas</th>
              <th class="text-left px-4 py-2">Médico</th>
              <th class="text-left px-4 py-2">Ações</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="appointment in appointments" :key="appointment.id">
              <td class="px-4 py-2">{{ appointment.animal_name }}</td>
              <td class="px-4 py-2">{{ appointment.animal_type }}</td>
              <td class="px-4 py-2">{{ appointment.appointment_date }}</td>
              <td class="px-4 py-2">{{ appointment.symptoms }}</td>
              <td class="px-4 py-2">{{ appointment.veterinarian ? appointment.veterinarian.name : 'Não atribuído' }}</td>
              <td class="px-4 py-2">
                <BaseButton @click="openEditModal(appointment)" label="Editar" color="info" small />
                <BaseButton @click="openAssignDoctorModal(appointment)" label="Atribuir Médico" color="success" small />
                <button @click="deleteModalRef.openDeleteModal(appointment)" class="bg-red-500 text-white px-2 py-1 rounded">Excluir</button>
              </td>
            </tr>
            </tbody>
          </table>
        </CardBox>
      </div>
    </SectionMain>
    <EditAppointmentModal ref="editModalRef" @updateSuccess="handleUpdateSuccess" />
    <AssignDoctorModal ref="assignDoctorModalRef" @doctorAssigned="handleDoctorAssigned" />
    <DeleteModal ref="deleteModalRef" @appointmentDeleted="fetchAppointments" />
  </LayoutAuthenticated>
</template>
