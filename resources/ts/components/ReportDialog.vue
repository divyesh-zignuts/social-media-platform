<script lang="ts" setup>
import { defineEmits } from 'vue'

const firstName = ref('')
const middleName = ref('')
const lastName = ref('')
const email = ref('')
const password = ref('')
const age = ref()
const interest = ref<string[]>([])

// Props
const props = defineProps<Props>()
const emit = defineEmits<Emit>()

interface Emit {
  (e: 'action', value: string): void
}

interface Props {
  isReportDialogVisible: boolean
}
</script>

<template>
  <div>
    <VDialog :model-value="props.isReportDialogVisible" max-width="600">
      <!-- Dialog Activator -->
      <template #activator="{ props }">
        <VBtn v-bind="props">
          Open Dialog
        </VBtn>
      </template>

      <!-- Dialog close btn -->
      <DialogCloseBtn @click="emit('action', 'close')" />

      <!-- Dialog Content -->
      <VCard title="User Profile">
        <VCardText>
          <VRow>
            <VCol cols="12" sm="6" md="4">
              <AppTextField v-model="firstName" label="First Name" />
            </VCol>
            <VCol cols="12" sm="6" md="4">
              <AppTextField v-model="middleName" label="Middle Name" />
            </VCol>
            <VCol cols="12" sm="6" md="4">
              <AppTextField v-model="lastName" label="Last Name" persistent-hint />
            </VCol>
            <VCol cols="12">
              <AppTextField v-model="email" label="Email" />
            </VCol>
            <VCol cols="12">
              <AppTextField v-model="password" label="Password" type="password" />
            </VCol>
            <VCol cols="12" sm="6">
              <AppTextField v-model="age" label="Age" type="number" />
            </VCol>
            <VCol cols="12" sm="6">
              <AppTextField v-model="interest" label="Interests" />
            </VCol>
          </VRow>
        </VCardText>

        <VCardText class="d-flex justify-end flex-wrap gap-3">
          <VBtn variant="tonal" color="secondary" @click="emit('action', 'close')">
            Close
          </VBtn>
          <VBtn @click="emit('action', 'close')">
            Save
          </VBtn>
        </VCardText>
      </VCard>
    </VDialog>
  </div>
</template>
