import axios from '@axios';
import { ref } from 'vue';


export const post = {
  postList: ref({}),
  comments: ref({}),
  getPostList: async () => {
    try {
    await axios.get('/post/list')
      .then(function (response) {
        post.postList.value = response.data.data.posts
        return post.postList.value;
      })
      .catch(function (error) {
        console.log(error);
      })
    } catch (error) {
      console.log(error);
    }
  },
  test: () => {
    console.log('fsf');
  },
  like: async (id:string) => {
    try {
      await axios.get(`/post/likeUnlike/${id}`)
        .then(function (response) {
          if(response.data.data.post){
            post.getPostList()
          }
        })
        .catch(function (error) {
          console.log(error);
        })
      } catch (error) {
        console.log(error);
      }
  },
  getComments: async (id:string) => {
    try {
      await axios.get(`/post/commentList/${id}`)
        .then(function (response) {
          post.comments.value = response.data.data.postComments
          return post.comments.value
        })
        .catch(function (error) {
          console.log(error);
        })
      } catch (error) {
        console.log(error);
      }
  }
};
