<script lang="ts" setup>
import List from '@/components/post/List.vue';
import axios from '@axios';
import { useHead } from '@unhead/vue';
import { onMounted, ref } from 'vue';

// change page title using this useHead
useHead({
  title: 'SMP | Post List',
})

const postList = ref()

const getPostList = async () => {

  await axios.get('/post/list')
    .then(function (response) {
      postList.value = response.data.data.post
    })
    .catch(function (error) {
      // handle error
      console.log(error);
    })
    .finally(function () {
      // always executed
    });
}

onMounted(async () => {
  await getPostList()
})

</script>

<template>
  <div>
    <List :postList="postList" />
  </div>
</template>

<style lang="scss"></style>
