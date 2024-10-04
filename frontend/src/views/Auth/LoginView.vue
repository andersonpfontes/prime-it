<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { mdiAccount, mdiAsterisk } from '@mdi/js'
import SectionFullScreen from '@/components/SectionFullScreen.vue'
import CardBox from '@/components/CardBox.vue'
import FormCheckRadio from '@/components/FormCheckRadio.vue'
import FormField from '@/components/FormField.vue'
import FormControl from '@/components/FormControl.vue'
import BaseButton from '@/components/BaseButton.vue'
import BaseButtons from '@/components/BaseButtons.vue'
import LayoutGuest from '@/layouts/LayoutGuest.vue'
import AuthService from '@/services/auth' // Importa o serviço de autenticação

const form = reactive({
  login: '',
  pass: '',
  remember: true,
})

const error = ref(null) // Para armazenar mensagens de erro
const loading = ref(false) // Para gerenciar o estado de carregamento
const router = useRouter()

const submit = async () => {
  try {
    loading.value = true
    error.value = null

    // Chama o serviço de autenticação
    const response = await AuthService.login({
      email: form.login,
      password: form.pass,
    })

    if (response.data.success) {
      // Armazena o token no localStorage
      localStorage.setItem('access_token', response.data.data.access_token)
      // Redireciona para o dashboard
      router.push('/dashboard')
    } else {
      // Se a API retornar sucesso: false
      error.value = response.data.message || 'Login failed. Please try again.'
    }
  } catch (err) {
    // Captura erros de requisição
    error.value = err.response?.data?.message || 'An error occurred. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <LayoutGuest>
    <SectionFullScreen v-slot="{ cardClass }" bg="purplePink">
      <CardBox :class="cardClass" is-form @submit.prevent="submit">
        <!-- Mensagem de erro -->
        <v-alert v-if="error" type="error" dismissible>
          {{ error }}
        </v-alert>

        <FormField label="Login" help="Please enter your login">
          <FormControl
            v-model="form.login"
            :icon="mdiAccount"
            name="login"
            autocomplete="username"
          />
        </FormField>

        <FormField label="Password" help="Please enter your password">
          <FormControl
            v-model="form.pass"
            :icon="mdiAsterisk"
            type="password"
            name="password"
            autocomplete="current-password"
          />
        </FormField>

        <FormCheckRadio
          v-model="form.remember"
          name="remember"
          label="Remember"
          :input-value="true"
        />

        <template #footer>
          <BaseButtons>
            <BaseButton
              type="submit"
              color="info"
              :label="loading ? 'Logging in...' : 'Login'"
              :disabled="loading"
            />
            <BaseButton to="/dashboard" color="info" outline label="Voltar" />
          </BaseButtons>
        </template>
      </CardBox>
    </SectionFullScreen>
  </LayoutGuest>
</template>
