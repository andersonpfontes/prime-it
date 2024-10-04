<script setup>
import { ref, onMounted } from 'vue'
import { useAppointmentsStore } from '@/stores/appointments' // Importa a store de marcações
import { mdiCalendarCheck, mdiDog, mdiClockOutline, mdiStethoscope } from '@mdi/js'
import SectionMain from '@/components/SectionMain.vue'
import CardBox from '@/components/CardBox.vue'
import SectionTitleLineWithButton from '@/components/SectionTitleLineWithButton.vue'
import EditAppointmentModal from '@/views/Appointment/EditAppointmentModal.vue'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'

// Usa a store de marcações
const appointmentsStore = useAppointmentsStore()

// Filtros
const animalTypeFilter = ref('')
const appointmentDateFilter = ref('')

// Modal de edição
const editModalRef = ref(null)

// Função para abrir o modal de edição
const openEditModal = (appointment) => {
  editModalRef.value.openModal(appointment)
}

// Função para buscar marcações com filtros aplicados
const fetchAppointments = () => {
  const filters = {
    appointment_date: appointmentDateFilter.value,
    animal_type: animalTypeFilter.value.trim(),
  }
  appointmentsStore.fetchAppointments(filters)
}

// Buscar as marcações quando o componente for montado
onMounted(() => {
  fetchAppointments()
})
</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
      <!-- Título da página -->
      <SectionTitleLineWithButton :icon="mdiStethoscope" title="Marcações Atribuídas" main />

      <!-- Filtros -->
      <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
          <label for="animalType" class="block mb-2">Filtrar por tipo de animal</label>
          <input
            id="animalType"
            type="text"
            v-model="animalTypeFilter"
            placeholder="Digite o tipo de animal"
            class="border rounded px-2 py-1 w-full"
          />
        </div>

        <div>
          <label for="appointmentDate" class="block mb-2">Filtrar por data</label>
          <input
            id="appointmentDate"
            type="date"
            v-model="appointmentDateFilter"
            class="border rounded px-2 py-1 w-full"
          />
        </div>
      </div>

      <button @click="fetchAppointments" class="bg-blue-500 text-white px-4 py-2 rounded mb-6">Filtrar</button>

      <!-- Lista de marcações -->
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 mb-6">
        <CardBox
          v-for="appointment in appointmentsStore.appointments"
          :key="appointment.id"
          class="hover:shadow-lg transition-shadow"
        >
          <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-blue-100 text-blue-500 rounded-full flex items-center justify-center">
              <span class="text-2xl">{{ appointment.animal_type.charAt(0) }}</span> <!-- Inicial do tipo de animal -->
            </div>
            <div>
              <h3 class="text-xl font-semibold">{{ appointment.animal_name }}</h3>
              <p class="text-sm text-gray-500">{{ appointment.symptoms }}</p>
            </div>
          </div>

          <div class="flex justify-between mt-4 text-gray-600">
            <div class="flex items-center space-x-2">
              <mdi-icon :icon="mdiCalendarCheck" size="18" />
              <span>{{ appointment.appointment_date }}</span>
            </div>
            <div class="flex items-center space-x-2">
              <mdi-icon :icon="mdiClockOutline" size="18" />
              <span>{{ appointment.period === 'morning' ? 'Manhã' : 'Tarde' }}</span>
            </div>
          </div>

          <!-- Botão para abrir modal de edição -->
          <button
            @click="openEditModal(appointment)"
            class="bg-yellow-500 text-white px-4 py-2 rounded mt-4"
          >
            Editar
          </button>
        </CardBox>
      </div>

      <EditAppointmentModal ref="editModalRef" @appointmentUpdated="fetchAppointments" />
    </SectionMain>
  </LayoutAuthenticated>
</template>
