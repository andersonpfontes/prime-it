import {
  mdiCalendarCheck,
  mdiAccountCircle,
  mdiMonitor,
  mdiStethoscope,
  mdiTable,
  mdiLogout,
} from '@mdi/js'
import Cookies from 'js-cookie'
import AuthService from '@/services/auth'

const role = Cookies.get('role')

const menu = [
  {
    to: '/dashboard',
    icon: mdiMonitor,
    label: 'Dashboard',
  },
  {
    to: '/profile',
    icon: mdiAccountCircle,
    label: 'Perfil',
  },
]

// Menu adicional apenas para clientes - "Agendar Consulta" e "Meus Agendamentos"
if (role === 'client') {
  menu.push(
    {
      to: '/appointments',
      icon: mdiCalendarCheck,
      label: 'Agendar Consulta',
    },
    {
      to: '/my-appointments',
      icon: mdiCalendarCheck,
      label: 'Meus Agendamentos',
    }
  )
}

// Menu adicional para recepcionistas e doutores - "Gestão de Marcações"
if (role === 'receptionist' || role === 'doctor') {
  menu.push(
    {
      to: '/manage-appointments',
      icon: mdiTable,
      label: 'Gestão de Marcações',
    }
  )
}

// Menu adicional apenas para médicos - "Marcações Atribuídas"
if (role === 'doctor') {
  menu.push(
    {
      to: '/doctor-view',
      icon: mdiStethoscope,
      label: 'Marcações Atribuídas',
    }
  )
}

// Menu de logout no final
menu.push(
  {
    label: 'Sair',
    icon: mdiLogout,
    action: () => AuthService.logout(),
  }
)

export default menu
