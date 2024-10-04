<script setup>
import { reactive, ref, onMounted } from 'vue'
import { mdiCalendar, mdiAccount, mdiMail, mdiPaw, mdiDog } from '@mdi/js'
import SectionMain from '@/components/SectionMain.vue'
import CardBox from '@/components/CardBox.vue'
import FormField from '@/components/FormField.vue'
import FormControl from '@/components/FormControl.vue'
import BaseButton from '@/components/BaseButton.vue'
import BaseButtons from '@/components/BaseButtons.vue'
import SectionTitleLineWithButton from '@/components/SectionTitleLineWithButton.vue'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'
import apiClient from '@/services/http'
import Cookies from 'js-cookie'

// Formulário de agendamento
const form = reactive({
  person_name: '',
  email: '',
  animal_name: '',
  animal_type: '',
  age: '',
  symptoms: '',
  appointment_date: '',
  period: 'morning', // Valor padrão como string
  veterinarian_id: '', // Veterinário id como string
})

// Estado para armazenar a lista de veterinários
const veterinarians = ref([])

// Função para buscar os veterinários
const fetchVeterinarians = async () => {
  const token = Cookies.get('access_token')

  try {
    const response = await apiClient.get('veterinarians', {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      }
    })

    if (response.data.success) {
      veterinarians.value = response.data.data
    }
  } catch (error) {
    console.error('Erro ao buscar veterinários:', error)
  }
}

// Chama a função para buscar os veterinários quando o componente é montado
onMounted(() => {
  fetchVeterinarians()
})

// Função para validar campos obrigatórios
const validateForm = () => {
  if (
    !form.person_name ||
    !form.email ||
    !form.animal_name ||
    !form.animal_type ||
    !form.age ||
    !form.symptoms ||
    !form.appointment_date ||
    !form.veterinarian_id
  ) {
    alert('Por favor, preencha todos os campos obrigatórios.')
    return false
  }
  return true
}

// Função para enviar o formulário de agendamento
const submit = async () => {
  if (!validateForm()) return // Valida antes de submeter

  const token = Cookies.get('access_token')

  const payload = {
    ...form,
    period: form.period,
    veterinarian_id: form.veterinarian_id.id,
  }

  try {
    console.log('Enviando agendamento...', payload)

    const response = await apiClient.post('appointments', payload, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      }
    })

    if (response.data.success) {
      alert('Agendamento criado com sucesso!')
    } else {
      alert('Erro ao criar agendamento.')
    }
  } catch (error) {
    console.error('Erro ao criar agendamento:', error)
    alert('Erro ao criar agendamento.')
  }
}
</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiCalendar" title="Agendar Consulta" main />

      <CardBox is-form @submit.prevent="submit">
        <!-- Nome da pessoa -->
        <FormField label="Nome da pessoa" help="Digite o seu nome completo">
          <FormControl v-model="form.person_name" :icon="mdiAccount" required />
        </FormField>

        <!-- E-mail -->
        <FormField label="E-mail" help="Digite o seu e-mail">
          <FormControl v-model="form.email" type="email" :icon="mdiMail" required />
        </FormField>

        <!-- Nome do animal -->
        <FormField label="Nome do animal" help="Digite o nome do seu animal">
          <FormControl v-model="form.animal_name" :icon="mdiPaw" required />
        </FormField>

        <!-- Tipo de animal -->
        <FormField label="Tipo de animal" help="Digite o tipo do animal (ex: Cão, Gato)">
          <FormControl v-model="form.animal_type" :icon="mdiDog" required />
        </FormField>

        <!-- Idade do animal -->
        <FormField label="Idade do animal" help="Digite a idade do animal">
          <FormControl v-model="form.age" type="number" required />
        </FormField>

        <!-- Sintomas -->
        <FormField label="Sintomas" help="Descreva os sintomas do animal">
          <FormControl v-model="form.symptoms" type="textarea" required />
        </FormField>

        <!-- Data da consulta -->
        <FormField label="Data da consulta" help="Escolha a data da consulta">
          <FormControl v-model="form.appointment_date" type="date" :icon="mdiCalendar" required />
        </FormField>

        <!-- Período da consulta -->
        <FormField label="Período da consulta" help="Escolha o período">
          <FormControl v-model="form.period" :options="[
            { id: 'morning', label: 'Manhã' },
            { id: 'afternoon', label: 'Tarde' }
          ]" type="radio" />
        </FormField>

        <!-- Veterinário -->
        <FormField label="Veterinário" help="Escolha o veterinário">
          <FormControl v-model="form.veterinarian_id" :options="veterinarians.map(vet => ({ id: vet.id, label: vet.name }))" option-label="label" option-value="id" type="select" />
        </FormField>

        <template #footer>
          <BaseButtons>
            <BaseButton type="submit" color="info" label="Agendar" />
            <BaseButton type="reset" color="info" outline label="Resetar" />
          </BaseButtons>
        </template>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>
