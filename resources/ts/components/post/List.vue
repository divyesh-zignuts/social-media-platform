<script lang="ts" setup>
import { Ref } from 'vue'; // Import Ref from 'vue'
import { post } from '../../composables/post';
import CommentDialogBox from '../CommentDialogBox.vue';
import ReportDialog from '../ReportDialog.vue';

const { getPostList, test, like, postList: Post, getComments } = post

const postList: Ref<Post[]> = post.postList;
const comments: Ref<Comment[]> = post.comments;
const isDialogVisible = ref<boolean>(false)
const isReportDialogVisible = ref<boolean>(false)

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

const moreList = [
  { title: 'Edit', id: 1, value: 'edit', action: 'edit' },
  { title: 'Delete', id: 2, value: 'delete', action: 'delete' },
  { title: 'Report', id: 3, value: 'report', action: 'report' },
]

const dialogAction = (item: string) => {
  if (item === 'close') {
    console.log('item: ', item);
    isDialogVisible.value = false
    getPostList()
  }
}

const commentBtnClick = (id: string) => {
  post.getComments(id)
  isDialogVisible.value = true
}

const handleMoreAction = (i: any) => {
  console.log('i: ', i);
}

onMounted(async () => {
  await getPostList()
})
</script>

<template>
  <div>
    <VRow class="d-flex align-content-center flex-column">
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
                <IconBtn>
                  <VIcon icon="mdi-dots-vertical" />
                  <VMenu activator="parent" open-on-click>
                    <VCard>
                      <VList v-for="i in moreList" :key="i?.id" class="pa-1">
                        <VListItem @click="handleMoreAction(i)">
                          <template #title>
                            {{ i?.title }}
                          </template>
                        </VListItem>
                      </VList>
                    </VCard>
                  </VMenu>
                </IconBtn>
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
              <IconBtn icon="tabler-share" class="me-1" />
            </div>
          </div>
        </VCard>
      </VCol>
    </VRow>
    <CommentDialogBox v-if="isDialogVisible" :isDialogVisible="isDialogVisible" :comments="comments"
      @action="dialogAction" />
    <ReportDialog v-if="isReportDialogVisible" :isReportDialogVisible="isReportDialogVisible" @action="dialogAction" />
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
