<script setup>
import { reactive, onMounted } from 'vue'
import { useMainStore } from '@/stores/main'
import { mdiAccount, mdiMail, mdiAsterisk, mdiFormTextboxPassword, mdiGithub } from '@mdi/js'
import SectionMain from '@/components/SectionMain.vue'
import CardBox from '@/components/CardBox.vue'
import BaseDivider from '@/components/BaseDivider.vue'
import FormField from '@/components/FormField.vue'
import FormControl from '@/components/FormControl.vue'
import FormFilePicker from '@/components/FormFilePicker.vue'
import BaseButton from '@/components/BaseButton.vue'
import BaseButtons from '@/components/BaseButtons.vue'
import UserCard from '@/components/UserCard.vue'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'
import SectionTitleLineWithButton from '@/components/SectionTitleLineWithButton.vue'
import apiClient from '@/services/http' // Cliente HTTP configurado para chamadas API
import Cookies from 'js-cookie' // Para pegar o token

const mainStore = useMainStore()

// Formulário de perfil
const profileForm = reactive({
  name: '',
  email: ''
})

// Formulário de senha
const passwordForm = reactive({
  password_current: '',
  password: '',
  password_confirmation: ''
})

// Função para buscar o perfil do cliente ao carregar a página
const fetchProfile = async () => {
  const token = Cookies.get('access_token')

  try {
    const response = await apiClient.post('auth/profile', {}, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      }
    })

    if (response.data.success) {
      const { name, email } = response.data.data
      profileForm.name = name
      profileForm.email = email
    }
  } catch (error) {
    console.error('Erro ao buscar perfil:', error)
  }
}

// Chama a função para buscar o perfil quando o componente é montado
onMounted(() => {
  fetchProfile()
})

// Função para enviar o perfil atualizado
const submitProfile = async () => {
  const token = Cookies.get('access_token')

  try {
    const response = await apiClient.put('auth/profile', profileForm, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      }
    })

    if (response.data.success) {
      // Atualiza o store com os dados do perfil atualizado
      mainStore.setUser(profileForm)
      alert('Perfil atualizado com sucesso!')
    }
  } catch (error) {
    console.error('Erro ao atualizar perfil:', error)
    alert('Erro ao atualizar perfil.')
  }
}

// Função para enviar a nova senha
const submitPass = async () => {
  const token = Cookies.get('access_token')

  try {
    const response = await apiClient.put('auth/change-password', passwordForm, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      }
    })

    if (response.data.success) {
      alert('Senha atualizada com sucesso!')
    }
  } catch (error) {
    console.error('Erro ao atualizar senha:', error)
    alert('Erro ao atualizar senha.')
  }
}
</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Formulário de atualização do perfil -->
        <CardBox is-form @submit.prevent="submitProfile">

          <FormField label="Name" help="Obrigatório. Seu nome">
            <FormControl
              v-model="profileForm.name"
              :icon="mdiAccount"
              name="username"
              required
              autocomplete="username"
            />
          </FormField>
          <FormField label="E-mail" help="Obrigatório. Seu email">
            <FormControl
              v-model="profileForm.email"
              :icon="mdiMail"
              type="email"
              name="email"
              required
              autocomplete="email"
            />
          </FormField>

          <template #footer>
            <BaseButtons>
              <BaseButton color="info" type="submit" label="Salvar" />
            </BaseButtons>
          </template>
        </CardBox>

        <!-- Formulário de alteração de senha -->
        <CardBox is-form @submit.prevent="submitPass">
          <FormField label="Senha atual" help="Obrigatório sua senha atual">
            <FormControl
              v-model="passwordForm.password_current"
              :icon="mdiAsterisk"
              name="password_current"
              type="password"
              required
              autocomplete="current-password"
            />
          </FormField>

          <BaseDivider />

          <FormField label="Nova senha" help="Obrigatório nova senha">
            <FormControl
              v-model="passwordForm.password"
              :icon="mdiFormTextboxPassword"
              name="password"
              type="password"
              required
              autocomplete="new-password"
            />
          </FormField>

          <FormField label="COnfirmar senha" help="Obrigatório. Nova senha mais uma vez">
            <FormControl
              v-model="passwordForm.password_confirmation"
              :icon="mdiFormTextboxPassword"
              name="password_confirmation"
              type="password"
              required
              autocomplete="new-password"
            />
          </FormField>

          <template #footer>
            <BaseButtons>
              <BaseButton type="submit" color="info" label="Salvar" />
            </BaseButtons>
          </template>
        </CardBox>
      </div>
    </SectionMain>
  </LayoutAuthenticated>
</template>
