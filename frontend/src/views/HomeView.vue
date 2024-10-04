<script setup>
import { computed, ref, onMounted } from 'vue'
import { useMainStore } from '@/stores/main'
import {
  mdiDogService,
  mdiAccountMultiple,
  mdiCalendarCheck,
  mdiChartTimelineVariant,
  mdiMonitorCellphone,
  mdiReload,
  mdiGithub,
  mdiChartPie
} from '@mdi/js'
import * as chartConfig from '@/components/Charts/chart.config.js'
import LineChart from '@/components/Charts/LineChart.vue'
import SectionMain from '@/components/SectionMain.vue'
import CardBoxWidget from '@/components/CardBoxWidget.vue'
import CardBox from '@/components/CardBox.vue'
import TableSampleClients from '@/components/TableSampleClients.vue'
import NotificationBar from '@/components/NotificationBar.vue'
import BaseButton from '@/components/BaseButton.vue'
import CardBoxTransaction from '@/components/CardBoxTransaction.vue'
import CardBoxClient from '@/components/CardBoxClient.vue'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'
import SectionTitleLineWithButton from '@/components/SectionTitleLineWithButton.vue'
import SectionBannerStarOnGitHub from '@/components/SectionBannerStarOnGitHub.vue'

const chartData = ref(null)

// Dados simulados
const fakeClients = ref([
  { id: 1, name: 'John Doe', login: 'johndoe', created: '2024-10-01', progress: 75 },
  { id: 2, name: 'Jane Smith', login: 'janesmith', created: '2024-10-02', progress: 50 },
])

const fakeTransactions = ref([
  { amount: '$250', date: '2024-10-01', business: 'Consulta', type: 'Cão', name: 'Toby', account: 'Veterinário' },
  { amount: '$350', date: '2024-10-02', business: 'Consulta', type: 'Gato', name: 'Whiskers', account: 'Veterinário' },
])

const fillChartData = () => {
  chartData.value = chartConfig.sampleChartData()
}

onMounted(() => {
  fillChartData()
})

const mainStore = useMainStore()

// Filtro de clientes e transações simuladas
const clientBarItems = computed(() => fakeClients.value.slice(0, 4))
const transactionBarItems = computed(() => fakeTransactions.value)
</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
      <!-- Título principal do Dashboard -->
      <SectionTitleLineWithButton :icon="mdiChartTimelineVariant" title="Visão Geral" main>
        <BaseButton
          href="https://github.com/andersonpfontes/prime-it"
          target="_blank"
          :icon="mdiGithub"
          label="Clone Projeto Github"
          color="contrast"
          rounded-full
          small
        />
      </SectionTitleLineWithButton>

      <!-- Widgets de informações principais -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-6">
        <CardBoxWidget
          trend="15%"
          trend-type="up"
          color="text-emerald-500"
          :icon="mdiAccountMultiple"
          :number="200"
          label="Clientes Ativos"
        />
        <CardBoxWidget
          trend="10%"
          trend-type="up"
          color="text-blue-500"
          :icon="mdiCalendarCheck"
          :number="50"
          label="Marcações Hoje"
        />
        <CardBoxWidget
          trend="80%"
          trend-type="alert"
          color="text-red-500"
          :icon="mdiDogService"
          :number="256"
          suffix="%"
          label="Capacidade de Atendimento"
        />
      </div>

      <!-- Tabela de transações (marcações recentes) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="flex flex-col justify-between">
          <CardBoxTransaction
            v-for="(transaction, index) in transactionBarItems"
            :key="index"
            :amount="transaction.amount"
            :date="transaction.date"
            :business="transaction.business"
            :type="transaction.type"
            :name="transaction.name"
            :account="transaction.account"
          />
        </div>
        <div class="flex flex-col justify-between">
          <CardBoxClient
            v-for="client in clientBarItems"
            :key="client.id"
            :name="client.name"
            :login="client.login"
            :date="client.created"
            :progress="client.progress"
          />
        </div>
      </div>

      <!-- Gráfico de tendências -->
      <SectionTitleLineWithButton :icon="mdiChartPie" title="Visão de Tendências">
        <BaseButton :icon="mdiReload" color="whiteDark" @click="fillChartData" />
      </SectionTitleLineWithButton>

      <CardBox class="mb-6">
        <div v-if="chartData">
          <line-chart :data="chartData" class="h-96" />
        </div>
      </CardBox>

      <!-- Tabela de clientes recentes -->
      <SectionTitleLineWithButton :icon="mdiAccountMultiple" title="Clientes Recentes" />

      <CardBox has-table>
        <TableSampleClients />
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>
