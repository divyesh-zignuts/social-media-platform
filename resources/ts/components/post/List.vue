<script lang="ts" setup>
import { Ref } from 'vue'; // Import Ref from 'vue'
import { post } from '../../composables/post';
import CommentDialogBox from '../CommentDialogBox.vue';

const { getPostList, test, like, postList: Post, comment } = post

const postList: Ref<Post[]> = post.postList;
const comments: Ref<Comment[]> = post.comments;
const isDialogVisible = ref<boolean>(false)

interface Asset {
  id: string;
  post_id: string;
  type: string;
  asset_url: string;
}

interface User {
  id: string;
  first_name: string;
  last_name: string;
  profile_image_url: string | null;
}

interface Comment {
  id: string;
  post_id: string;
  user_id: string;
  comment_id: string | null;
  comment: string;
  user: User;
  replies: Comment[];
}

interface Like {
  id: string;
  first_name: string;
  last_name: string;
  email: string;
  email_verified_at: string | null;
  role: string;
  phone: string | null;
  status: string;
  token: string | null;
  profile_image_url: string | null;
  created_by: string | null;
  updated_by: string | null;
  deleted_by: string | null;
  created_at: string;
  updated_at: string;
  deleted_at: string | null;
  pivot: {
    post_id: string;
    user_id: string;
  };
}

interface Post {
  id: string;
  user_id: string;
  bio: string;
  likes_count: number;
  comments_count: number;
  liked_by_user: boolean;
  assets: Asset[];
  user: User;
  comments: Comment[];
  likes: Like[];
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

const dialogAction = (item: string) => {
  if (item === 'close') {
    console.log('item: ', item);

    isDialogVisible.value = false
  }
}

const commentBtnClick = (id: string) => {
  post.comment(id)

  isDialogVisible.value = true
}

onMounted(async () => {
  await getPostList()
})
</script>

<template>
  <div>
    <VRow>
      <VCol cols="12" sm="4" md="3" v-for="(post, index) in postList" :key="index">
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
            <div @click="like(post.id)">
              <IconBtn :icon="post?.liked_by_user ? 'tabler-heart-filled' : 'tabler-heart'"
                :color="post?.liked_by_user ? 'error' : ''" class="me-1" />
              <span class="text-subtitle-2 me-4">{{ post?.likes_count }}</span>
            </div>
            <div @click="commentBtnClick(post.id)">
              <IconBtn icon="tabler-message" class="me-1" />
              <span class="text-subtitle-2 mt-1">{{ post?.comments_count }}</span>
            </div>
            <div>
              <IconBtn icon="tabler-octagon-off" class="me-1" />
            </div>
          </div>
        </VCard>
      </VCol>
    </VRow>
    <CommentDialogBox v-if="isDialogVisible" :isDialogVisible="isDialogVisible" :comments="comments"
      @action="dialogAction" />
  </div>
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
