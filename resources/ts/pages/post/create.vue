<script lang="ts" setup>
import axios from '@axios';
import { useHead } from '@unhead/vue';
import vueDropzone from 'dropzone-vue3';

// change page title using this useHead
useHead({
  title: 'SMP | Post Create',
})

const inlineRadio = ref('I')
const editor = ref()
const myVueDropzone = ref()
const postFile = ref([])
const removeFileVal = ref([])

const dropzoneOptions = {
  url: 'https://httpbin.org/post',
  maxFiles: 25,
  uploadMultiple: false,
  parallelUploads: 100,
  thumbnailWidth: 150,
  maxFilesize: 10,
  addRemoveLinks: true,
  headers: { "My-Awesome-Header": "header value" }
}
const handleFileAdding = (file: any) => {
  if (file.status == 'success') {
    postFile.value.push(file)

  }
}

const removeFile = (file: any) => {
  removeFileVal.value.push(file)
}

const handleSubmit = async () => {
  try {

    // axios.defaults.headers.post['Content-Type'] = 'multipart/form-data'
    // const payload = {
    //   post_type: inlineRadio.value,
    //   bio: editor.value,
    //   post_file: postFile.value
    // }
    // const response = await axios.post('/post/create', payload)

    let formData = new FormData();
    formData.append('post_type', inlineRadio.value);
    formData.append('bio', editor.value);
    postFile.value.forEach((file) => {
      formData.append('post_files[]', file);
    });

    const response = await axios.post('/post/create', formData)
    if (response) {
      console.log(response)
    }

  } catch (error) {
    console.error(error);
  }
}

</script>

<template>
  <div>
    <VCard title="Post Create">
      <VCardText>
        <VRow>
          <VCol cols="12">
            <VRow no-gutters>
              <VCol cols="12" class="">
                <label class="v-label text-body-2 text-high-emphasis">Upload file</label>
                <vue-dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions"
                  @vdropzone-success="handleFileAdding" @duplicateCheck="true" @vdropzone-removed-file="removeFile" />
              </VCol>
            </VRow>
          </VCol>
          <VCol cols="12">
            <VRow no-gutters>
              <VCol cols="12" class="">
                <label class="v-label text-body-2 text-high-emphasis">Bio</label>
                <QuillEditor v-model:content="editor" contentType="html" theme="snow" toolbar="full" />
              </VCol>
            </VRow>
          </VCol>
          <VCol cols="12">
            <label class="v-label text-body-2 text-high-emphasis">Post Type</label>
            <VRadioGroup v-model="inlineRadio" inline>
              <VRadio label="Image" value="I" />
              <VRadio label="Video" value="V" />
              <VRadio label="Gif" value="G" />
            </VRadioGroup>
          </VCol>
          <VCol cols="12" class="d-flex gap-4">
            <VBtn type="button" @click.prevent="handleSubmit()">
              Submit
            </VBtn>
            <VBtn color="secondary" variant="tonal">
              Cancel
            </VBtn>
          </VCol>
        </VRow>

      </VCardText>
    </VCard>
  </div>
</template>


<style lang="scss">
.ql-container {
  block-size: auto !important;
}

.vue-dropzone .dz-preview .dz-remove {
  position: absolute;
  z-index: 30;
  padding: 8px;
  border: 1px white solid;
  color: white;
  font-size: 10px;
  font-weight: 500;
  inset-block: inherit 1px;
  letter-spacing: 0.5px;
  margin-inline-start: 15px;
  opacity: 0;
  text-decoration: none;
  text-transform: uppercase;
}
</style>
