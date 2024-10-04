import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Auth/LoginView.vue'
import Dashboard from '../views/HomeView.vue'
import Cookies from 'js-cookie'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },

  // Rota do Dashboard acessível a todos os usuários logados
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true },
  },

  // Rota para agendamento de marcações (apenas recepcionista e médico)
  {
    path: '/appointments',
    name: 'Appointments',
    component: () => import('../views/Appointment/AppointmentsView.vue'),
    meta: { requiresAuth: true, role: ['receptionist', 'doctor', 'client'] },
  },

  // Rota para Meus Agendamentos
  {
    path: '/my-appointments',
    name: 'MyAppointments',
    component: () => import('../views/Appointment/MyAppointmentsView.vue'),
    meta: { requiresAuth: true, role: ['client'] }, // Apenas clientes podem acessar
  },

  // Rota para ver e gerenciar marcações (apenas recepcionista)
  {
    path: '/manage-appointments',
    name: 'ManageAppointments',
    component: () => import('../views/Appointment/ManageAppointmentsView.vue'),
    meta: { requiresAuth: true, role: ['receptionist'] },
  },

  // Rota para atribuir médicos às marcações (apenas recepcionista)
  {
    path: '/assign-doctors',
    name: 'AssignDoctors',
    component: () => import('../views/Doctor/AssignDoctorsView.vue'),
    meta: { requiresAuth: true, role: ['receptionist'] },
  },

  // Rota para o médico ver as marcações atribuídas a ele
  {
    path: '/doctor-view',
    name: 'DoctorView',
    component: () => import('../views/Doctor/DoctorView.vue'),
    meta: { requiresAuth: true, role: ['doctor'] },
  },

  // Rota do cliente para ver suas próprias marcações
  {
    path: '/client-dashboard',
    name: 'ClientDashboard',
    component: () => import('../views/ClientDashboard.vue'),
    meta: { requiresAuth: true, role: ['client'] },
  },

  // Rota de perfil acessível a todos os usuários logados
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('../views/Profile/ProfileView.vue'),
    meta: { requiresAuth: true },
  },

  // Rota de edição de perfil acessível a todos os usuários logados
  {
    path: '/edit-profile',
    name: 'EditProfile',
    component: () => import('../views/Profile/EditProfileView.vue'),
    meta: { requiresAuth: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Middleware de autenticação
router.beforeEach((to, from, next) => {
  const token = Cookies.get('access_token')
  const role = Cookies.get('role')

  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!token) {
      next({ name: 'Login' })
    } else if (to.meta.role && !to.meta.role.includes(role)) {
      next({ name: 'Dashboard' })
    } else {
      next()
    }
  } else {
    next()
  }
})

export default router
