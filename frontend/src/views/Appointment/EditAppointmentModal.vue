<script setup>
import { ref } from 'vue'
import apiClient from '@/services/http'
import Cookies from 'js-cookie'

const appointment = ref({}) // Consulta a ser editada
const isModalOpen = ref(false) // Estado da modal

// Função para abrir a modal com os dados da consulta
const openModal = (appointmentData) => {
  console.log("Abrindo modal para editar:", appointmentData)
  appointment.value = { ...appointmentData }
  isModalOpen.value = true
}

// Função para salvar as alterações
const saveAppointment = async () => {
  const token = Cookies.get('access_token')

  try {
    console.log("Enviando dados para API...", appointment.value)
    const response = await apiClient.put(`appointments/${appointment.value.id}`, {
      person_name: appointment.value.person_name,
      email: appointment.value.email,
      animal_name: appointment.value.animal_name,
      animal_type: appointment.value.animal_type,
      age: appointment.value.age,
      symptoms: appointment.value.symptoms,
      appointment_date: appointment.value.appointment_date,
      period: appointment.value.period,
      veterinarian_id: appointment.value.veterinarian_id,
    }, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      }
    })

    if (response.data.success) {
      console.log("Atualização bem-sucedida:", response.data)
      alert('Consulta atualizada com sucesso!')
      closeModal() // Fechar a modal após o sucesso
    } else {
      console.error("Erro no response da API:", response.data.message)
    }
  } catch (error) {
    console.error('Erro ao atualizar a consulta:', error.response?.data ?? error.message)
  }
  emit('updateSuccess')
}

// Função para fechar a modal
const closeModal = () => {
  console.log("Fechando modal")
  isModalOpen.value = false
}

// Exportar a função `openModal` para ser acessada pelo componente pai
defineExpose({
  openModal,
})
</script>

<template>
  <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
      <h2 class="text-xl font-bold mb-4">Editar Consulta</h2>

      <!-- Formulário para editar consulta -->
      <div>
        <label class="block mb-2">Nome da Pessoa</label>
        <input v-model="appointment.person_name" type="text" class="border rounded w-full px-2 py-1 mb-4" />

        <label class="block mb-2">E-mail</label>
        <input v-model="appointment.email" type="email" class="border rounded w-full px-2 py-1 mb-4" />

        <label class="block mb-2">Nome do Animal</label>
        <input v-model="appointment.animal_name" type="text" class="border rounded w-full px-2 py-1 mb-4" />

        <label class="block mb-2">Tipo de Animal</label>
        <input v-model="appointment.animal_type" type="text" class="border rounded w-full px-2 py-1 mb-4" />

        <label class="block mb-2">Idade do Animal</label>
        <input v-model="appointment.age" type="number" class="border rounded w-full px-2 py-1 mb-4" />

        <label class="block mb-2">Sintomas</label>
        <textarea v-model="appointment.symptoms" class="border rounded w-full px-2 py-1 mb-4"></textarea>

        <label class="block mb-2">Data da Consulta</label>
        <input v-model="appointment.appointment_date" type="date" class="border rounded w-full px-2 py-1 mb-4" />

        <label class="block mb-2">Período</label>
        <select v-model="appointment.period" class="border rounded w-full px-2 py-1 mb-4">
          <option value="morning">Manhã</option>
          <option value="afternoon">Tarde</option>
        </select>
      </div>

      <div class="flex justify-end mt-4">
        <button @click="saveAppointment" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Salvar</button>
        <button @click="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
      </div>
    </div>
  </div>
</template>
