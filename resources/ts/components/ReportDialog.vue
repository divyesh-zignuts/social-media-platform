<script lang="ts" setup>
import axios from '@axios';
import { requiredValidator } from '@validators';
import { defineEmits } from 'vue';
import type { VForm } from 'vuetify/components/VForm';

const reportForm = ref<VForm>()
const reasonRadio = ref()
const otherReason = ref<string>('')

// Props
const props = defineProps<Props>()
const emit = defineEmits<Emit>()

interface Emit {
  (e: 'action', value: string): void
}

interface Props {
  isReportDialogVisible: boolean,
  reportPostId: string
}
const submitReport = () => {
  reportForm.value.validate().then((valid) => {
    if (valid) {
      console.log('reasonRadio', reasonRadio.value);
      try {

        let formData = new FormData();
        formData.append('post_id', props.reportPostId);
        formData.append('reason', reasonRadio.value);
        formData.append('other_reason', otherReason.value);

        axios.post('/post/report', formData)
          .then(function (response) {
            if (response) {
              emit('action', 'reportSuccessful')
            }
          })
          .catch(function (error) {
            console.log(error);
          });

      } catch (error) {
        console.error(error);
      }
    } else {
      console.log('Form validation failed. Please correct errors.');
    }
  });
}

const otherReasonRules = computed(() => {
  if (reasonRadio.value === 'Oth') {
    return [requiredValidator]
  } else {
    return []
  }
})
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
      <DialogCloseBtn @click="emit('action', 'reportDialogClose')" />

      <!-- Dialog Content -->
      <VCard title="Report Post">
        <VForm ref="reportForm" @submit.prevent="submitReport()">
          <VCardText>
            <VRow>
              <VCol cols="12">
                <label class="v-label text-body-2 text-high-emphasis">Select Reason</label>
                <VRadioGroup v-model="reasonRadio" :rules="[requiredValidator]" inline>
                  <VRadio label="Spam" value="S" />
                  <VRadio label="Inappropriate" value="I" />
                  <VRadio label="Offensive" value="O" />
                  <VRadio label="Other" value="Oth" />
                </VRadioGroup>
              </VCol>
              <VCol cols="12">
                <AppTextarea v-model="otherReason" label="Other Reason" :rules="otherReasonRules" />
              </VCol>
            </VRow>
          </VCardText>

          <VCardText class="d-flex justify-end flex-wrap gap-3">
            <VBtn variant="tonal" color="secondary" @click="emit('action', 'reportDialogClose')">
              Close
            </VBtn>
            <VBtn type="submit">
              Save
            </VBtn>
          </VCardText>
        </VForm>
      </VCard>
    </VDialog>
  </div>
</template>
