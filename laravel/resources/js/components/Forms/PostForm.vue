<template>
  <a-drawer
      :title="'Статья ' + form.title"
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
        <a-form-item class="mb-10" label="Тип" name="faculties" :colon="false">
          <a-select
              v-model:value="form.postable_type"
              :options="postable_types"
              :field-names="{ label: 'name', value: 'model'}"
          ></a-select>
        </a-form-item>
        <a-form-item class="mb-10" label="" v-if="form.postable_type == 'App\\Models\\Academy'" name="academy"  :colon="false">
          <a-select
              v-model:value="form.postable_id"
              :options="meta?.academies"
              :field-names="{ label: 'name', value: 'id'}"
          ></a-select>
        </a-form-item>
        <a-form-item class="mb-10" label="" v-if="form.postable_type == 'App\\Models\\Faculty'" name="faculty"  :colon="false">
          <a-select
              v-model:value="form.postable_id"
              :options="meta?.faculties"
              :field-names="{ label: 'name', value: 'id'}"
          ></a-select>
        </a-form-item>
        <a-form-item class="mb-10" label="Изображение" name="image" :colon="false">
          <ImageUpload v-model="form.images" action='posts/media' :images="form.media" :maxCount="1"></ImageUpload>
        </a-form-item>
        <a-form-item class="mb-10" label="Название" name="title" :colon="false">
          <a-input
              v-model:value="form.title"
          />
        </a-form-item>
        <a-form-item class="mb-20" label="Текст статьи" name="text" :colon="false">
          <QuillEditor v-if="visible" theme="snow"
                       v-model:content="form.text"
                       :options="editorOption"
                       contentType="html"
          />
        </a-form-item>
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
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import ImageUploader from 'quill-image-uploader';
import { QuillEditor, Quill } from '@vueup/vue-quill'
import { notification } from 'ant-design-vue';


export default ({


  data() {
    return {
      text: null,
      postable_types:[
        {
          name: "Компания",
          model: "App\\Models\\Company"
        },
        {
          name: "Академия",
          model: "App\\Models\\Academy"
        },
        {
          name: "Факультет",
          model: 'App\\Models\\Faculty'
        },
      ],
      initialForm: {
        title: '',
        thumbnail:'',
        postable_type:"App\\Models\\Company",
        postable_id:'',
        text: '',
        media:[],
        images:[],
      },
      initialId: null,
      loading: false,
      editorOption: {
        modules: {
          toolbar: {
            container: [
              ['bold', 'italic', 'underline', 'strike'],
              ['blockquote'],

              [{'header': 1}, {'header': 2}],
              [{'list': 'ordered'}, {'list': 'bullet'}],
              [{'color': []}, {'background': []}],
              [{'align': []}],
              ['image', 'video'],
              ['clean'],
            ],
          },
          history: {
            delay: 1000,
            maxStack: 50,
            userOnly: false
          },
        }
      },
      meta:[],
      form: {},
      rules: {
        title: [
          { required: true, message: 'Введите название', trigger: 'blur' },
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
    name(){
      if(this.initialId != null){
        return this.form.name
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
    this.fetchCreate()
    this.resetForm();
  },

  methods: {

    resetForm(){
      this.form = {...this.initialForm};
    },
    fetchCreate(){
      this.loading = true;
      this.$axios.get('/posts/create')
          .then(response => {
            this.meta = response.data.meta;
            console.log(this.meta)
            // router.push({ name: 'Academy', params: {academy_id: } })
          })
          .catch(error => {
            notification.error({
              message: 'Ошибка',
              //description: error,
            });
          })
          .then(()=>{
            this.loading = false;
          });
    },
    loadData(id){
      this.loading = true;
      this.$axios.get('/posts/'+id)
          .then(response => {
            this.form = response.data.data;
            // router.push({ name: 'Academy', params: {academy_id: } })
          })
          .catch(error => {
            notification.error({
              message: 'Ошибка',
              //description: error,
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
      // console.log(this.$refs.formRef.validate())
      // // this.$refs.formRef.target.validate();
      // console.log(this.form
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
      let url = "posts";
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
              //description: error,
            });
          });

    },
  }
})

</script>
