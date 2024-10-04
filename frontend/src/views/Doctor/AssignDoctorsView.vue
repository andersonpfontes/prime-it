<script setup>
import { ref } from 'vue'
import apiClient from '@/services/http'
import Cookies from 'js-cookie'

const appointment = ref({})
const isModalOpen = ref(false)
const doctors = ref([])
const selectedDoctor = ref(null)

// Função para abrir a modal com os dados da consulta e carregar médicos
const openModal = async (appointmentData) => {
  appointment.value = { ...appointmentData }
  selectedDoctor.value = appointmentData.veterinarian_id
  isModalOpen.value = true
  await fetchDoctors()
}

// Função para buscar médicos da API
const fetchDoctors = async () => {
  const token = Cookies.get('access_token')

  try {
    const response = await apiClient.get('veterinarians', {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      }
    })

    if (response.data.success) {
      doctors.value = response.data.data
    }
  } catch (error) {
    console.error('Erro ao buscar médicos:', error)
    alert('Erro ao buscar médicos.')
  }
}

// Função para salvar a atribuição do médico
const saveDoctorAssignment = async () => {
  const token = Cookies.get('access_token')

  try {
    const response = await apiClient.put(`appointments/${appointment.value.id}/assign-veterinarian`, {
      veterinarian_id: selectedDoctor.value,
    }, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      }
    })

    if (response.data.success) {
      alert('Médico atribuído com sucesso!')
      closeModal()
      emit('doctorAssigned')
    } else {
    }
  } catch (error) {
    console.error('Erro ao atribuir médico:', error)
  }
}

// Função para fechar a modal
const closeModal = () => {
  isModalOpen.value = false
  selectedDoctor.value = null
}

defineExpose({
  openModal,
})
</script>

<template>
  <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
      <h2 class="text-xl font-bold mb-4">Atribuir Médico</h2>

      <!-- Selecionar médico -->
      <div>
        <label class="block mb-2">Selecione um médico</label>
        <select v-model="selectedDoctor" class="border rounded w-full px-2 py-1 mb-4">
          <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">{{ doctor.name }}</option>
        </select>
      </div>

      <div class="flex justify-end mt-4">
        <button @click="saveDoctorAssignment" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Salvar</button>
        <button @click="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
      </div>
    </div>
  </div>
</template>
