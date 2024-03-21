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
  dialogTitle: string
  dialogDescription: string
  dialogFirstButton: any
  dialogSecondButton: any
}

</script>

<template>
  <VDialog persistent max-width="500" :model-value="props.isDialogVisible">

    <!-- Dialog close btn -->
    <DialogCloseBtn @click="emit('action', 'dialogClose')" />

    <!-- Dialog Content -->
    <VCard :title="props.dialogTitle">
      <VForm @submit.prevent="">

        <VCardText>
          {{ props.dialogDescription }}
        </VCardText>

        <VCardText class="d-flex justify-end flex-wrap gap-3">
          <VBtn v-if="props.dialogFirstButton" :color="props.dialogFirstButton.firstButtonColor"
            @click="emit('action', props.dialogFirstButton.firstButtonAction)">
            {{ props.dialogFirstButton.firstButtonTitle }}
          </VBtn>

          <VBtn v-if="props.dialogSecondButton" @click="emit('action', props.dialogSecondButton.secondButtonAction)"
            :color="props.dialogFirstButton.secondButtonColor">
            {{ props.dialogSecondButton.secondButtonTitle }}
          </VBtn>
        </VCardText>

      </VForm>
    </VCard>
  </VDialog>
</template>
