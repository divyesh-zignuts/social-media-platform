<script setup lang="ts">
import { isEmpty } from 'lodash'
import { defineProps } from 'vue'

const props = defineProps({
  userData: Object
})

// computed
const getUserLabel = computed(() => {
  if (props?.userData?.role === 'U')
    return 'User'

  else if (props?.userData?.role === 'A')
    return 'Admin'
})
</script>

<template>
  <section>
    <VCard class="personal-details__card">
      <VCardTitle>
        <div class="d-flex justify-lg-space-between pa-8">
          <div>
            <h3>Personal information</h3>
          </div>
        </div>
      </VCardTitle>
      <VCardText>
        <VRow>
          <VCol cols="4">
            <div class="d-flex align-center justify-center">
              <VAvatar color="primary" size="120">
                <VImg v-if="!isEmpty(props?.userData?.profile_image_url)" :src="props?.userData?.profile_image_url" />
                <span v-else class="profile-initial">{{ props?.userData?.first_name?.charAt(0) }}
                </span>
              </VAvatar>
            </div>
            <div class="d-flex align-center justify-center mt-5">
              {{ getUserLabel }}
            </div>
          </VCol>
          <VCol cols="8">
            <div class="d-flex">
              <div class="personal-details__keys">
                Name of Respondent:
              </div>
              <div class="personal-details__values">
                {{ props?.userData?.first_name }}
                {{ props?.userData?.last_name }}
              </div>
            </div>
            <div class="d-flex">
              <div class="personal-details__keys">
                Email Address:
              </div>
              <div class="personal-details__values">
                {{ props?.userData?.email }}
              </div>
            </div>
            <div class="d-flex">
              <div class="personal-details__keys">
                Contact Number:
              </div>
              <div class="personal-details__values">
                {{ props?.userData?.phone }}
              </div>
            </div>
          </VCol>
        </VRow>
      </VCardText>
    </VCard>
  </section>
</template>

<style scoped lang="scss">
.profile-initial {
  font-size: 100px;
  text-transform: capitalize;
}
</style>
