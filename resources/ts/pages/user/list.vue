<script lang="ts" setup>
import DialogBox from "@/components/DialogBox.vue";
import axios from '@axios';
import { useHead } from '@unhead/vue';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { VDataTable } from 'vuetify/labs/VDataTable';

// change page title using this useHead
useHead({
  title: 'SMP | User List',
})

const router = useRouter()

// Data
const users = ref([])
const isDialogVisible = ref(false)
const actionId = ref<string>()
const dialogTitle = ref<string>()
const dialogDescription = ref<string>()
const dialogFirstButton = ref({
  firstButtonTitle: '',
  firstButtonColor: '',
  firstButtonAction: '',
})
const dialogSecondButton = ref({
  secondButtonTitle: '',
  secondButtonColor: '',
  secondButtonAction: '',
})

const UserList = async () => {
  axios.post('user/list')
    .then(function (response) {
      users.value = response.data.data.users
    })
    .catch(function (error) {
      // handle error
      console.log(error);
    })
}

const headers = [
  { title: 'NAME', key: 'first_name' },
  { title: 'EMAIL', key: 'email' },
  { title: 'ROLE', key: 'role' },
  { title: 'STATUS', key: 'status' },
  { title: 'ACTION', key: 'actions' },
]

const resolveStatusVariant = (status: string) => {
  if (status === 'P')
    return { color: 'warning', text: 'Pending' }
  else if (status === 'A')
    return { color: 'success', text: 'Approve' }
  else if (status === 'R')
    return { color: 'error', text: 'Rejected' }
}

const actionsItems = [
  {
    title: 'View',
    value: 'view',
    id: 1,
    action: 'viewDetails',
  },
  {
    title: 'Delete',
    value: 'delete',
    id: 2,
    action: 'delete',
  },
  {
    title: 'Approve/Reject',
    value: 'approveReject',
    id: 3,
    action: 'approveReject',
  }
]

const handleAction = (val: object, id: string) => {
  if (val.value === 'view') {
    router.push({
      path: `/user/profile/${id}`,
    })
  }
  else if (val.value === 'delete') {
    actionId.value = id
    dialogTitle.value = 'Delete'
    dialogDescription.value = 'Are you sure!! You want to delete this ??'
    dialogFirstButton.value.firstButtonTitle = 'Delete'
    dialogFirstButton.value.firstButtonColor = 'error'
    dialogFirstButton.value.firstButtonAction = 'delete'
    dialogSecondButton.value.secondButtonTitle = 'Close'
    dialogSecondButton.value.secondButtonColor = 'secondary'
    dialogSecondButton.value.secondButtonAction = 'close'
    isDialogVisible.value = true
  }
  else if (val.value === 'approveReject') {
    dialogTitle.value = 'User Request!'
    dialogDescription.value = 'Review user profile and take necessary action on it.'
    isDialogVisible.value = true
    actionId.value = id
    dialogFirstButton.value.firstButtonTitle = 'Approve'
    dialogFirstButton.value.firstButtonColor = 'success'
    dialogFirstButton.value.firstButtonAction = 'approve'
    dialogSecondButton.value.secondButtonTitle = 'Reject'
    dialogSecondButton.value.secondButtonColor = 'error'
    dialogSecondButton.value.secondButtonAction = 'reject'
  }
}

const dialogAction = (item: string) => {
  if (item === 'close') {
    isDialogVisible.value = false
  }
  else if (item === 'delete') {
    axios.get(`/user/delete/${actionId.value}`)
      .then(function (response) {
        if (response) {
          actionId.value = ''
          isDialogVisible.value = false
          dialogTitle.value = ''
          dialogDescription.value = ''
          dialogFirstButton.value.firstButtonTitle = ''
          dialogFirstButton.value.firstButtonColor = ''
          dialogFirstButton.value.firstButtonAction = ''
          dialogSecondButton.value.secondButtonTitle = ''
          dialogSecondButton.value.secondButtonColor = ''
          dialogSecondButton.value.secondButtonAction = ''
          UserList()
        }
      })
      .catch(function (error) {
        // handle error
        console.log(error)
      })
  }
  else if (item === 'approve' || item === 'reject') {
    axios.post(`/user/statusChange`, {
      id: actionId.value,
      status: item === 'approve' ? 'A' : 'R'
    })
      .then(function (response) {
        if (response) {
          actionId.value = ''
          isDialogVisible.value = false
          dialogTitle.value = ''
          dialogDescription.value = ''
          dialogFirstButton.value.firstButtonTitle = ''
          dialogFirstButton.value.firstButtonColor = ''
          dialogFirstButton.value.firstButtonAction = ''
          dialogSecondButton.value.secondButtonTitle = ''
          dialogSecondButton.value.secondButtonColor = ''
          dialogSecondButton.value.secondButtonAction = ''
          UserList()
        }
      })
      .catch(function (error) {
        // handle error
        console.log(error)
      })
  }

}

// Hooks
onMounted(async () => {
  await UserList()
})
</script>

<template>
  <div>
    <VRow>
      <VCol cols="12">
        <VCard title="User list">
          <VCardText>
            <VDataTable :headers="headers" :items="users" :items-per-page="10">
              <template #item.role="{ item }">
                <div class="d-flex gap-1">
                  {{ item.props.title.role === "A" ? 'Admin' : 'User' }}
                </div>
              </template>

              <template #item.status="{ item }">
                <VChip size="default" :color="resolveStatusVariant(item.props.title.status).color" density="comfortable"
                  class="font-weight-medium">
                  {{ resolveStatusVariant(item.props.title.status).text }}
                </VChip>
              </template>

              <template #item.actions="{ item }">
                <VMenu>
                  <template #activator="{ props }">
                    <VBtn color="secondary" variant="plain" rounded="pill" v-bind="props">
                      <VIcon icon="tabler-dots-vertical" size="22" />
                    </VBtn>
                  </template>

                  <VCard>
                    <VList v-for="i in actionsItems" :key="i?.id">
                      <VListItem @click="handleAction(i, item.props.title.id)">
                        <template #title>
                          {{ i?.title }}
                        </template>
                      </VListItem>
                    </VList>
                  </VCard>
                </VMenu>
              </template>
            </VDataTable>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
    <DialogBox v-if="isDialogVisible" :isDialogVisible="isDialogVisible" :dialogTitle="dialogTitle"
      :dialogDescription="dialogDescription" :dialogFirstButton="dialogFirstButton"
      :dialogSecondButton="dialogSecondButton" @action="dialogAction" />
  </div>
</template>

<style lang="scss"></style>
