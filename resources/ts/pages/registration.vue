<script setup>
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'

import authV2RegisterIllustrationBorderedDark from '@images/pages/auth-v2-register-illustration-bordered-dark.png'
import authV2RegisterIllustrationBorderedLight from '@images/pages/auth-v2-register-illustration-bordered-light.png'
import authV2RegisterIllustrationDark from '@images/pages/auth-v2-register-illustration-dark.png'
import authV2RegisterIllustrationLight from '@images/pages/auth-v2-register-illustration-light.png'
import authV2MaskDark from '@images/pages/misc-mask-dark.png'
import authV2MaskLight from '@images/pages/misc-mask-light.png'

import { themeConfig } from '@themeConfig'
import { alphaDashValidator, emailValidator, requiredValidator } from '@validators'

import axios from '@axios'
import { useHead } from '@unhead/vue'
import { useRouter } from 'vue-router'

// change page title using this useHead
useHead({
  title: 'SMP | Registration',
})

const register = ref({
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  phone: '',
  profile_image_url: '',
})


const router = useRouter()

const imageVariant = useGenerateImageVariant(
  authV2RegisterIllustrationLight,
  authV2RegisterIllustrationDark, authV2RegisterIllustrationBorderedLight,
  authV2RegisterIllustrationBorderedDark,
  true,
)

const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)

const isPasswordVisible = ref(false)


const registration = async () => {
  try {
    await axios.post('register', {
      first_name: register.value.first_name,
      last_name: register.value.last_name,
      email: register.value.email,
      password: register.value.password,
      phone: register.value.phone,
      profile_image_url: register.value.profile_image_url,
    }).then(function (response) {
      if (response) {
        router.push('/login')
      }
    })
      .catch(function (error) {
        // handle error
        console.log(error);
      })

  } catch (error) {
    console.error(error)
  }
}


</script>

<template>
  <VRow no-gutters class="auth-wrapper bg-surface">
    <VCol lg="8" class="d-none d-lg-flex">
      <div class="position-relative bg-background rounded-lg w-100 ma-8 me-0">
        <div class="d-flex align-center justify-center w-100 h-100">
          <VImg max-width="441" :src="imageVariant" class="auth-illustration mt-16 mb-2" />
        </div>

        <VImg class="auth-footer-mask" :src="authThemeMask" />
      </div>
    </VCol>

    <VCol cols="12" lg="4" class="d-flex align-center justify-center">
      <VCard flat :max-width="500" class="mt-12 mt-sm-0 pa-4">
        <VCardText>
          <VNodeRenderer :nodes="themeConfig.app.logo" class="mb-6" />
          <h5 class="text-h5 mb-1">
            Register yourself here 🚀
          </h5>
        </VCardText>

        <VCardText>
          <VForm ref="refVForm" @submit.prevent="registration">
            <VRow>
              <!-- First Name -->
              <VCol cols="12">
                <AppTextField v-model="register.first_name" autofocus :rules="[requiredValidator, alphaDashValidator]"
                  label="First Name*" />
              </VCol>

              <!-- Last Name -->
              <VCol cols="12">
                <AppTextField v-model="register.last_name" autofocus :rules="[requiredValidator, alphaDashValidator]"
                  label="Last Name*" />
              </VCol>

              <!-- Phone -->
              <VCol cols="12">
                <AppTextField v-model="register.phone" label="Contact Number"
                  :rules="[requiredValidator(register.phone, 'Contact number')]" />
              </VCol>

              <!-- email -->
              <VCol cols="12">
                <AppTextField v-model="register.email" :rules="[requiredValidator, emailValidator]" label="Email*"
                  type="email" />
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <AppTextField v-model="register.password" :rules="[requiredValidator]" label="Password*"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible" />

                <VBtn block type="submit" class="mt-4">
                  Sign up
                </VBtn>
              </VCol>

              <!-- create account -->
              <VCol cols="12" class="text-center text-base">
                <span>Already have an account?</span>
                <RouterLink class="text-primary ms-2" :to="{ name: 'login' }">
                  Login in
                </RouterLink>
              </VCol>

            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>

<route lang="yaml">
meta:
  layout: blank
</route>
