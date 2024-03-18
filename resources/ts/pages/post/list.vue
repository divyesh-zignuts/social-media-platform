<script lang="ts" setup>
import List from '@/components/post/List.vue';
import { useHead } from '@unhead/vue';

// change page title using this useHead
useHead({
  title: 'SMP | Post List',
})

</script>

<template>
  <div>
    <List />
  </div>
</template>

<style lang="scss"></style>
