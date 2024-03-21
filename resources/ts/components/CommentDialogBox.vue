<script lang="ts" setup>
import axios from '@axios';
import { defineEmits } from 'vue';
import { post } from '../composables/post.ts';

const { getComments } = post

// Props
const props = defineProps<Props>()
const emit = defineEmits<Emit>()


interface Emit {
  (e: 'action', value: string): void
}

interface Props {
  isCommentDialogVisible: boolean
  comments: object
}

const commentMsg = ref('')

const comment = async (id: string) => {
  try {
    const response = await axios.post('/post/commentAdd', {
      post_id: id,
      comment_id: null,
      comment: commentMsg.value,
    })
      .then(function (response) {
        if (response.data.data.comment) {
          props.comments = getComments(id);
          commentMsg.value = ''
        }
      })
      .catch(function (error) {
        console.log(error);
      })

  } catch (error) {
    console.error(error)
  }
}
</script>

<template>
  <div>
    <VDialog :model-value="props.isCommentDialogVisible" persistent max-width="600">
      <!-- Dialog close btn -->
      <DialogCloseBtn @click="emit('action', 'commentDialogClose')" />

      <!-- Dialog Content -->
      <VCard>
        <VCardItem class="pb-3">
          <VCardTitle>Comments</VCardTitle>
        </VCardItem>
        <VDivider />
        <VCardText>
          <div v-for="(post, postIndex) in props.comments" :key="postIndex">
            <div v-for="(asset, assIndex) in post?.assets" :key="assIndex">
              <VImg :src="asset?.asset_url" height="300" />
            </div>
            <div class="pt-4" v-html="post?.bio">
            </div>
            <VDivider />
            <VMain class="chat-content-container">
              <div class="chat-log py-5">
                <div class="comment-container">
                  <div class="chat-group d-flex align-start" v-for="(comments, commIndex) in post?.comments"
                    :key="commIndex">
                    <div class="chat-avatar me-4">
                      <VTooltip activator="parent" location="top">
                        {{ post?.user?.first_name }} {{ post?.user?.last_name }}
                      </VTooltip>
                      <VAvatar size="32" color="primary">
                        {{ post?.user?.first_name.charAt(0) }}{{ post?.user?.last_name.charAt(0) }}
                      </VAvatar>
                    </div>
                    <div class="chat-body d-inline-flex flex-column align-start">
                      <p class="chat-content py-2 px-4 elevation-1 chat-left">
                        {{ comments.comment }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <VForm class="chat-log-message-form">
                <VTextField v-model="commentMsg" variant="solo" class="chat-message-input"
                  placeholder="Type your message..." density="default" autofocus>
                  <template #append-inner>
                    <VBtn @click.prevent="comment(post.id)">
                      Send
                    </VBtn>
                  </template>
                </VTextField>
              </VForm>
            </VMain>
          </div>
        </VCardText>
      </VCard>
    </VDialog>
  </div>
</template>

<style lang="scss">
.chat-content-container {
  .chat-message-input {
    .v-field__append-inner {
      align-items: center;
      padding-block-start: 0;
    }

    .v-field--appended {
      padding-inline-end: 9px;
    }
  }
}

.chat-log {
  .chat-content {
    border-end-end-radius: 6px;
    border-end-start-radius: 6px;

    &.chat-left {
      border-start-end-radius: 6px;
    }

    &.chat-right {
      border-start-start-radius: 6px;
    }
  }
}

.comment-container {
  max-block-size: 200px;
  overflow-y: auto;
}
</style>
