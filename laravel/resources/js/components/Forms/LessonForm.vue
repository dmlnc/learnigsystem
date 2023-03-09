<template>
  <a-drawer
      :title="'Урок ' + name"
      :width="992"
      :visible="visible"
      :body-style="{ paddingBottom: '80px' }"
      :footer-style="{ textAlign: 'right' }"
      @close="onClose"
  >
    <a-skeleton active :loading="loading" >
      <a-form
          layout="vertical"
          ref="formRef"
          :model="form"
          :rules="rules"
          @submit="handleSubmit"
          :hideRequiredMark="true"
      >
        <a-form-item class="mb-10" label="Изображение" name="image" :colon="false">
          <ImageUpload v-model="form.images" action='lessons/media' :images="form.media" :maxCount="1"></ImageUpload>
        </a-form-item>
        <a-form-item class="mb-10" label="Название" name="title" :colon="false">
          <a-input
              v-model:value="form.title"
          />
        </a-form-item>
        <a-form-item class="mb-10" label="Описание" name="description" :colon="false">
          <a-textarea v-model:value="form.short_text" />
        </a-form-item>

        <QuillEditor theme="snow"
                     v-model:content="form.long_text"
                     @ready="onEditorReady($event)"
                     contentType="html"
        />
        <a-form-item class="mb-10" label="Видео" name="video" :colon="false">
          <a-input v-model:value="form.video" />
        </a-form-item>
        <a-checkbox
            v-model:value="form.is_published"
            v-decorator="[
						'is_published',
						{
							valuePropName: 'checked',
							initialValue: false,
						},
						]"
        >
          Опубликовать
        </a-checkbox>
        <a-form-item>
          <a-button type="primary" block html-type="submit" >
            Сохранить
          </a-button>
        </a-form-item>
      </a-form>
    </a-skeleton>




  </a-drawer>
</template>

<script>


import ImageUpload from '@/components/Images/ImageUpload.vue'

import { notification } from 'ant-design-vue';
import AuthUtil from '@/libs/auth/auth';
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import ImageUploader from 'quill-image-uploader';
export default ({


  data() {
    return {
      // Binded model property for "Sign In Form" switch button for "Remember Me" .
      text: null,
      fileList: null,
      data: {},
      imageUrl: null,
      initialForm: {
        title: '',
        thumbnail:'',
        video:'',
        short_text:'',
        long_text:'',
        description:'',
        position:'',
        is_published:false,
        media:[],
        images:[],
      },
      initialId: null,
      loading: false,
      form: {},
      modules: {
        name: 'imageUploader',
        module: ImageUploader,
        options: {
          upload: file => {
            return new Promise((resolve, reject) => {
              const formData = new FormData();
              formData.append("file", file);
              axios.post('/lesson/media', formData)
                  .then(res => {
                    console.log(res)
                    resolve(res.data.url);
                  })
                  .catch(err => {
                    reject("Upload failed");
                    console.error("Error:", err)
                  })
            })
          }
        }
      },
      rules: {
        title: [
          { required: true, message: 'Введите название', trigger: 'blur' },
        ],
        short_text: [
          { required: true, message: 'Введите описание', trigger: 'blur' },
        ],
      }
    }
  },

  props: [
    'id',
    'visible'
  ],
  components:{
    QuillEditor,ImageUploader,
    ImageUpload,
  },
  computed:{
    getAuthToken(){
      return AuthUtil.getAuthToken()
    },
    name(){
      if(this.initialId != null){
        return this.form.title
      }

      return '';
    }
  },

  beforeCreate() {
    // Creates the form and adds to it component's "form" property.
    // this.form = this.$form.createForm(this, { name: 'normal_login' });
  },

  watch:{
    visible(){
      this.visibleForm = this.visible;
      this.resetForm()
    },
    id(){
      this.initialId = this.id;
      if(this.id!=null){
        this.loadData(this.id)
      }
      else{
        this.loading = false;
      }
    },
  },

  mounted(){
    this.resetForm();
  },

  methods: {
    onEditorReady (e) {
      console.log(e);
      e.container.querySelector('.ql-blank').innerHTML = this.form.long_text
    },
    resetForm(){
      this.form = {...this.initialForm, course_id: this.$route.params.course_id};
    },
    loadData(id){
      this.loading = true;
      this.$axios.get('/lessons/'+id)
          .then(response => {
            this.form = response.data.data;
            // router.push({ name: 'Academy', params: {academy_id: } })
          })
          .catch(error => {
            notification.error({
              message: 'Ошибка',
              description: error,
            });
          })
          .then(()=>{
            this.loading = false;
          });
    },

    onClose(){
      this.resetForm();
      // this.visibleForm = false;
      this.$emit('close');
    },

    handleSubmit(e) {
      e.preventDefault();
      this.$refs.formRef
          .validate()
          .then((e) => {})
          .catch((e) => {
            if(e.errorFields.length == 0){
              this.submitForm();
            }
          })
    },

    submitForm(){
      let data = {...this.form};
      let url = "lessons";
      if(this.initialId!=null){
        data._method = "put";
        url = url + '/' + this.initialId;
      }

      let formData = data

      this.$axios.post(url, formData)
          .then(response => {
            // console.log(response)
            this.initialId = response.data.data.id;
            this.loadData(this.initialId);
            this.$emit('save');
            this.$emit('close');
            notification.success({
              message: 'Успешно',
            });

            // const token = response.data.token;
            // setAuthToken(token);
            // router.push({ name: 'Home' })
          })
          .catch(error => {
            notification.error({
              message: 'Ошибка',
              description: error,
            });
          });

    },
  }
})

</script>
