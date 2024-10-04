import apiClient from './http'
import Cookies from 'js-cookie'

const login = (credentials) => {
  return apiClient.post('auth/login', credentials).then(response => {
    if (response.data.success) {
      Cookies.set('access_token', response.data.data.access_token, { expires: 1 })

      return apiClient.post('auth/profile', {}, {
        headers: {
          Authorization: `Bearer ${response.data.data.access_token}`,
          Accept: 'application/json',
        }
      }).then(profileResponse => {
        if (profileResponse.data.success) {
          const role = profileResponse.data.data.role
          // Armazena o role nos cookies
          Cookies.set('role', role, { expires: 1 })
        }
        return profileResponse
      })
    }
    return response
  })
}

const logout = () => {
  const token = Cookies.get('access_token')

  if (!token) {
    window.location.href = '/login'
    return
  }

  return apiClient.post('auth/logout', {}, {
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: 'application/json',
    }
  }).then(() => {
    Cookies.remove('access_token')
    Cookies.remove('role')

    window.location.href = '/login'
  }).catch((error) => {
    console.error('Erro ao fazer logout:', error)
    window.location.href = '/login'
  })
}

const AuthService = {
  login,
  logout,
}

export default AuthService
