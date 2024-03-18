<script lang="ts" setup>
import { defineEmits } from 'vue';

// Props
const props = defineProps<Props>()
const emit = defineEmits<Emit>()

interface Emit {
  (e: 'action', value: string): void
}

interface Props {
  isDialogVisible: boolean
  comments: object
}

const countryList = [
  {
    label: 'Bahamas, The',
    value: 'bahamas',
  },
  {
    label: 'Bahrain',
    value: 'bahrain',
  },
  {
    label: 'Bangladesh',
    value: 'bangladesh',
  },
  {
    label: 'Barbados',
    value: 'barbados',
  },
  {
    label: 'Belarus',
    value: 'belarus',
  },
  {
    label: 'Belgium',
    value: 'belgium',
  },
  {
    label: 'Belize',
    value: 'belize',
  },
  {
    label: 'Benin',
    value: 'benin',
  },
  {
    label: 'Bhutan',
    value: 'bhutan',
  },
  {
    label: 'Bolivia',
    value: 'bolivia',
  },
  {
    label: 'Bosnia and Herzegovina',
    value: 'bosnia',
  },
  {
    label: 'Botswana',
    value: 'botswana',
  },
  {
    label: 'Brazil',
    value: 'brazil',
  },
  {
    label: 'Brunei',
    value: 'brunei',
  },
  {
    label: 'Bulgaria',
    value: 'bulgaria',
  },
  {
    label: 'Burkina Faso',
    value: 'burkina',
  },
]

const selectedCountry = ref('')
const isDialogVisible = ref(false)
</script>

<template>
  <div>
    <VDialog :model-value="props.isDialogVisible" persistent scrollable max-width="600">

      <!-- Dialog close btn -->
      <DialogCloseBtn @click="emit('action', 'close')" />

      <!-- Dialog Content -->
      <VCard>
        <VCardItem class="pb-5">
          <VCardTitle>Select Country</VCardTitle>
        </VCardItem>

        <VDivider />
        <VCardText style="block-size: 300px;">
          {{ props.comments }}
          <VRadioGroup v-model="selectedCountry" :inline="false">
            <VRadio v-for="country in countryList" :key="country.label" :label="country.label" :value="country.value"
              color="primary" />
          </VRadioGroup>
        </VCardText>

        <VDivider />

        <VCardText class="d-flex justify-end flex-wrap gap-3 pt-5">
          <VBtn color="secondary" variant="tonal" @click="emit('action', 'close')">
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
