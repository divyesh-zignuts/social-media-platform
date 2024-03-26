<script lang="ts" setup>
import CommentDialogBox from '@/components/CommentDialogBox.vue';
import ReportDialog from '@/components/ReportDialog.vue';
import axios from '@axios';
import { useHead } from '@unhead/vue';
import { useRouter } from 'vue-router';
import { post } from '../composables/post.ts';

// change page title using this useHead
useHead({
  title: 'SMP | Trending Post',
})

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

const { getTrendingPostList, postList: Post, getComments: Comment } = post

const postList = post.postList;
const comments = post.comments;
const reportPostId = ref<string>()
const deletePostId = ref<string>()
const isDialogVisible = ref<boolean>(false)
const isCommentDialogVisible = ref<boolean>(false)
const isReportDialogVisible = ref<boolean>(false)
const router = useRouter()

const moreList = [
  { title: 'Report', id: 3, value: 'report', action: 'report' },
]

const dialogAction = (item: string) => {
  if (item === 'commentDialogClose') {
    isCommentDialogVisible.value = false
    getTrendingPostList()
  } else if (item === 'reportDialogClose') {
    isReportDialogVisible.value = false
  } else if (item === 'reportSuccessful') {
    isReportDialogVisible.value = false
    getTrendingPostList()
  } else if (item === 'dialogClose') {
    isDialogVisible.value = false
  } else if (item === 'deletePost') {
    try {
      axios.post(`/post/destroy/${deletePostId.value}`)
        .then(function (response) {
          if (response) {
            isDialogVisible.value = false
            getTrendingPostList()
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    } catch (error) {
      console.error(error);
    }
  }
}

const commentBtnClick = (id: string) => {
  post.getComments(id)
  isCommentDialogVisible.value = true
}

const likeBtnClick = (id: string) => {
  post.like(id)
    .then(function (response: any) {
      getTrendingPostList()
    })
    .catch(function (error: any) {
      console.error('Error while liking:', error);
    });
}

const handleMoreAction = (value: string, id: string) => {
  if (value === 'report') {
    reportPostId.value = id
    isReportDialogVisible.value = true
  }
}

const userInfo = (id: string) => {
  router.push({
    path: `/user/profile/${id}`,
  })
}

onMounted(async () => {
  await getTrendingPostList()
})
</script>

<template>
  <div>
    <VRow class="d-flex align-content-center flex-column">
      <VCol cols="12" sm="4" md="3" v-for="(post, index) in postList" :key="index">
        <VCard>
          <div class="vCardHeader">
            <div class="postInfo">
              <div class="userInfo" @click="userInfo(post?.user?.id)">
                <VAvatar color="primary">
                  {{ post?.user?.first_name.charAt(0) }}{{ post?.user?.last_name.charAt(0) }}
                </VAvatar>
                <span class="ms-2"> {{ post?.user?.first_name }}</span>
              </div>

              <div class="postAction">
                <IconBtn>
                  <VIcon icon="mdi-dots-vertical" />
                  <VMenu activator="parent" open-on-click>
                    <VCard>
                      <VList v-for="i in moreList" :key="i?.id" class="pa-1">
                        <VListItem @click="handleMoreAction(i.value, post.id)">
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
            <div @click="likeBtnClick(post.id)">
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
    <CommentDialogBox v-if="isCommentDialogVisible" :isCommentDialogVisible="isCommentDialogVisible"
      :comments="comments" @action="dialogAction" />
    <ReportDialog v-if="isReportDialogVisible" :isReportDialogVisible="isReportDialogVisible"
      :reportPostId="reportPostId" @action="dialogAction" />
    <DialogBox v-if="isDialogVisible" :isDialogVisible="isDialogVisible" :dialogTitle="dialogTitle"
      :dialogDescription="dialogDescription" :dialogFirstButton="dialogFirstButton"
      :dialogSecondButton="dialogSecondButton" @action="dialogAction" />
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

.postInfo .userInfo {
  cursor: pointer;
}
</style>
