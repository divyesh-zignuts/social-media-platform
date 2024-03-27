<script lang="ts" setup>
import navItems from '@/navigation/vertical'
import { useThemeConfig } from '@core/composable/useThemeConfig'
import CryptoJs from 'crypto-js'

// Components
import Footer from '@/layouts/components/Footer.vue'
import NavbarThemeSwitcher from '@/layouts/components/NavbarThemeSwitcher.vue'
import UserProfile from '@/layouts/components/UserProfile.vue'

// @layouts plugin
import { VerticalNavLayout } from '@layouts'

const { appRouteTransition, isLessThanOverlayNavBreakpoint } = useThemeConfig()
const { width: windowWidth } = useWindowSize()

// Computed
const filterAccessedList = computed(() => {
  let role = ''

  const encryptedData = localStorage.getItem('userData')
  const userData = encryptedData ? JSON.parse(CryptoJs.AES.decrypt(encryptedData || '', import.meta.env.VITE_CRYPTO_SECURE_KEY).toString(CryptoJs.enc.Utf8)) : null

  if (userData)
    console.log('userData: ', userData);
  role = userData.role === 'A' ? 'admin' : 'user'

  // check and return only list which are accessBy role-wise
  return navItems.filter((item: any) => {
    return item.accessBy.includes(role)
  })
})
</script>

<template>
  <VerticalNavLayout :nav-items="filterAccessedList">
    <!-- 👉 navbar -->
    <template #navbar="{ toggleVerticalOverlayNavActive }">
      <div class="d-flex h-100 align-center">
        <IconBtn v-if="isLessThanOverlayNavBreakpoint(windowWidth)" id="vertical-nav-toggle-btn" class="ms-n3"
          @click="toggleVerticalOverlayNavActive(true)">
          <VIcon size="26" icon="tabler-menu-2" />
        </IconBtn>

        <NavbarThemeSwitcher />

        <VSpacer />

        <UserProfile />
      </div>
    </template>

    <!-- 👉 Pages -->
    <RouterView v-slot="{ Component }">
      <Transition :name="appRouteTransition" mode="out-in">
        <Component :is="Component" />
      </Transition>
    </RouterView>

    <!-- 👉 Footer -->
    <template #footer>
      <Footer />
    </template>

    <!-- 👉 Customizer -->
    <!-- <TheCustomizer /> -->
  </VerticalNavLayout>
</template>
