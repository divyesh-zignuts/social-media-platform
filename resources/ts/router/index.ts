import { setupLayouts } from 'virtual:generated-layouts'
import { createRouter, createWebHistory } from 'vue-router'
import routes from '~pages'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    ...setupLayouts(routes),
  ],
})

// checks auth token
const checkToken = () => {
  return !!localStorage.getItem('accessToken')
}

router.beforeEach((to, from, next) => {
  if (!checkToken() && (to.name !== 'login' && to.name !== 'registration' && to.name !== 'trending')) {
    next({ name: 'trending' })
  }
  else if(checkToken()){
    if (to.name === 'login' || to.name === 'registration') {
      next({ name: 'index' })
    } else {
      next()
    }
  }
  else
    next()
})

export default router
