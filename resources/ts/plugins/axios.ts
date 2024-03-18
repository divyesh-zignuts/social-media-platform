import router from '@/router';
import axios from 'axios';
const axiosIns = axios.create({
  baseURL: import.meta.env.VITE_APP_API_URL,
  headers: {
    // Accept: "application/json",
  },
});

axiosIns.interceptors.request.use(config => {
  // Retrieve token from localStorage
  const token = localStorage.getItem('accessToken')

  // If token is found
  if (token) {

    config.headers = config.headers || {}

    config.headers.Authorization = token ? `Bearer ${localStorage.getItem('accessToken')}` : ''
  }

  return config
})

axiosIns.interceptors.response.use(response => {
  return response
}, error => {
  // Handle error
  if (error.response.status === 401) {
    // ℹ️ Logout user and redirect to login page
    // Remove "userData" from localStorage
    localStorage.removeItem('userData')

    // Remove "accessToken" from localStorage
    localStorage.removeItem('accessToken')

    // If 401 response returned from api
    router.push('/login')
  }
  else {
    return Promise.reject(error)
  }
})

export default axiosIns
