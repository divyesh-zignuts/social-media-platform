<script lang="ts" setup>
const props = defineProps<Props>()

interface Props {
  postList: object
}

const actionsItems = [
  {
    title: 'Edit',
    value: 'edit',
    id: 1,
    action: 'edit',
  },
  {
    title: 'Delete',
    value: 'delete',
    id: 2,
    action: 'delete',
  }
]


const test = () => {
  console.log('fsaf');

}
</script>

<template>
  <VRow>
    <!-- 👉 Influencing The Influencer -->
    <VCol cols="12" sm="4" md="3" v-for="(post, index) in props?.postList" :key="index">
      <VCard height="470">
        <div class="vCardHeader">
          <div class="postInfo">
            <div class="userInfo">
              <VAvatar color="primary">
                A
              </VAvatar>
              <span class="ms-2"> {{ post?.user?.first_name }}</span>
            </div>

            <div class="postAction">
              <VMenu>
                <template #activator="{ props }">
                  <VBtn color="secondary" variant="plain" rounded="pill" v-bind="props">
                    <VIcon icon="tabler-dots-vertical" size="22" />
                  </VBtn>
                </template>

                <VCard>
                  <VList v-for="i in actionsItems" :key="i?.id">
                    <VListItem @click="test()">
                      <template #title>
                        {{ i?.title }}
                      </template>
                    </VListItem>
                  </VList>
                </VCard>
              </VMenu>
            </div>
          </div>
        </div>
        <div v-for="(asset, assIndex) in post?.assets" :key="assIndex">
          <VImg :src="asset?.asset_url" cover height="300" />
          <div class="py-3 px-4" v-html="post?.bio">
          </div>
        </div>
        <div class="d-flex justify-lg-space-evenly">
          <div>
            <IconBtn icon="tabler-heart" class="me-1" />
            <span class="text-subtitle-2 me-4">11</span>
          </div>
          <div>
            <IconBtn icon="tabler-message" class="me-1" />
            <span class="text-subtitle-2 mt-1">12</span>
          </div>
          <div>
            <IconBtn icon="tabler-octagon-off" class="me-1" />
          </div>
        </div>
      </VCard>
    </VCol>
  </VRow>
</template>

<style lang="scss">
.vCardHeader {
  display: flex;
  align-items: center;
}

.postInfo {
  display: flex;
  flex: 1 1 auto;
  align-items: center;
  justify-content: space-between;
  padding: 10px;
  font-size: 0.9375rem;
  font-weight: 400;
  letter-spacing: normal;
  text-transform: none;
}
</style>
