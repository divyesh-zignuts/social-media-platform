<script setup lang="ts">
import axios from '@/plugins/axios';
import avatar1 from '@images/avatars/avatar-1.png';
import CryptoJs from 'crypto-js';
import { useRouter } from 'vue-router';

const router = useRouter()
const first_name = ref('')
const role = ref('')
const ProfileImage = ref('')
const id = ref('')

// logout
const logout = () => {
  // const token = localStorage.getItem('auth-token')
  axios.post('logout').then(() => {
    // removes auth-token
    localStorage.removeItem('accessToken')

    // removes userData
    localStorage.removeItem('userData')

    // //removes auth-data for remember me functionality
    // localStorage.removeItem('auth-data')
    router.push({ name: 'login' })
  }).catch(e => {
    console.log('error', e)
  })
}

// get userdata from local
onMounted(() => {
  const encryptedData = localStorage.getItem('userData')

  const userData = encryptedData ? JSON.parse(CryptoJs.AES.decrypt(encryptedData || '', import.meta.env.VITE_CRYPTO_SECURE_KEY).toString(CryptoJs.enc.Utf8)) : null

  if (userData) {
    first_name.value = userData.first_name
    role.value = userData.role === 'A' ? 'Admin' : 'User'
    ProfileImage.value = userData.profile_image_url
    id.value = userData.id
  }
})
</script>

<template>
  <VBadge dot location="bottom right" offset-x="3" offset-y="3" bordered color="success">
    <VAvatar class="cursor-pointer" color="primary" variant="tonal">
      <VImg :src="avatar1" />

      <!-- SECTION Menu -->
      <VMenu activator="parent" width="230" location="bottom end" offset="14px">
        <VList>
          <!-- 👉 User Avatar & Name -->
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VBadge dot location="bottom right" offset-x="3" offset-y="3" color="success">
                  <VAvatar color="primary" variant="tonal">
                    <VImg :src="avatar1" />
                  </VAvatar>
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{ first_name }}
            </VListItemTitle>
            <VListItemSubtitle>{{ role }}</VListItemSubtitle>
          </VListItem>

          <VDivider class="my-2" />

          <!-- 👉 Profile -->
          <VListItem link>
            <template #prepend>
              <VIcon class="me-2" icon="tabler-user" size="22" />
            </template>

            <VListItemTitle @click="router.push(`/user/profile/${id}`)">Profile</VListItemTitle>
          </VListItem>

          <!-- Divider -->
          <VDivider class="my-2" />

          <!-- 👉 Logout -->
          <VListItem @click="logout">
            <template #prepend>
              <VIcon class="me-2" icon="tabler-logout" size="22" />
            </template>

            <VListItemTitle>Logout</VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>
