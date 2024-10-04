<script setup>
import { ref } from 'vue'
import apiClient from '@/services/http'
import Cookies from 'js-cookie'

const appointmentToDelete = ref(null)
const isDeleteModalOpen = ref(false)

// Função para abrir a modal de confirmação
const openDeleteModal = (appointment) => {
  appointmentToDelete.value = appointment
  isDeleteModalOpen.value = true
}

// Função para fechar a modal de confirmação
const closeDeleteModal = () => {
  isDeleteModalOpen.value = false
  appointmentToDelete.value = null
}

// Função para deletar a consulta
const deleteAppointment = async () => {
  const token = Cookies.get('access_token')

  try {
    const response = await apiClient.delete(`appointments/${appointmentToDelete.value.id}`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      }
    })

    if (response.status === 204) {
      alert('Consulta excluída com sucesso!')
      // eslint-disable-next-line no-undef
      emit('appointmentDeleted')
      closeDeleteModal()
    }
  } catch (error) {
    console.error('Erro ao excluir consulta:', error)
  }
}

defineExpose({
  openDeleteModal,
})
</script>

<template>
  <div v-if="isDeleteModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
      <h2 class="text-xl font-bold mb-4">Confirmar Exclusão</h2>
      <p>Tem certeza que deseja excluir a consulta de <strong>{{ appointmentToDelete?.person_name }}</strong>?</p>

      <div class="flex justify-end mt-4">
        <button @click="deleteAppointment" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Excluir</button>
        <button @click="closeDeleteModal" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
      </div>
    </div>
  </div>
</template>
