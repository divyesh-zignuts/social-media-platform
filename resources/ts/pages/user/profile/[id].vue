<script lang="ts" setup>
import PersonalInformation from '@/components/user/PersonalInformation.vue'
import $http from '@/plugins/axios'
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'

// composable
const route = useRoute()

// Data
const userData = ref<any>()

// Method
const getUserDetails = async () => {
  try {
    await $http.get(`/user/profile/${route.params.id}`)
      .then(function (response) {
        userData.value = response.data.data.user
      })
      .catch(function (error) {
        console.log(error);
      })
  }
  catch (e) {
    console.log(e)
  }
}

onMounted(async () => {
  await getUserDetails()
})

</script>

<template>
  <section>
    <VRow>
      <VCol xl="6" lg="6" md="6" sm="6" cols="12">
        <PersonalInformation type="other_user_profile" :user-data="userData" />
      </VCol>
    </VRow>
  </section>


</template>

<style lang="scss"></style>
