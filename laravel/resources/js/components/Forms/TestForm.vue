<template>
  <a-drawer
      :title="'Тест ' + name"
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
        <a-tabs default-active-key="1" @change="callback">
          <a-tab-pane key="1" tab="Основные">
            <a-form-item class="mb-10" label="Изображение" name="image" :colon="false">
              <ImageUpload v-model="form.images" action='lessons/media' :images="form.media" :maxCount="1"></ImageUpload>
            </a-form-item>
            <a-form-item class="mb-10" label="Описание" name="description" :colon="false">
              <a-textarea v-model:value="form.description" />
            </a-form-item>
            <a-form-item class="mb-10" label="Название" name="title" :colon="false">
              <a-input
                  v-model:value="form.title"
              />
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
          </a-tab-pane>
          <a-tab-pane key="2" tab="Вопросы" force-render>
            <a-button type="primary" class="mb-10" @click="addQuestion">
              Добавить вопрос
            </a-button>

            <a-collapse v-model="activeKey" v-if="form.questions.length">
              <a-collapse-panel :key="question.id" :header="'Вопрос ' + (index + 1)" v-for="(question,index) in form.questions">
                <a-form-item class="mb-10" label="Вопрос" name="question" :colon="false">
                  <a-textarea
                      v-model:value="question.question_text"
                  />
                  <a-button ghost type="primary" class="mb-10" @click="addOption(question)">
                    Добавить ответ
                  </a-button>
                  <a-row :gutter="16">
                    <a-col class="gutter-row" :span="6" v-for="(option,index) in question.options">
                      <a-input
                          v-model:value="option.option_text"
                      />
                      <a-checkbox
                          v-model:value="option.is_correct"
                          v-decorator="[
                            'is_correct',
                            {
                              valuePropName: 'is_correct',
                              initialValue: false,
                            },
                            ]"
                      >
                        Верный ответ
                      </a-checkbox>
                      <a-button danger @click="removeOption(question,index)" class="mb-10" shape="circle">
                        <template  #icon><DeleteOutlined /></template>
                      </a-button>
                    </a-col>
                  </a-row>
                </a-form-item>
                <a-button danger class="mb-10" @click="removeQuestion(question)">
                  Удалить вопрос
                </a-button>
              </a-collapse-panel>
            </a-collapse>
          </a-tab-pane>
        </a-tabs>
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



import { notification } from 'ant-design-vue';
import AuthUtil from '@/libs/auth/auth';
import ImageUpload from '@/components/Images/ImageUpload.vue';
import { DeleteOutlined } from '@ant-design/icons-vue';

export default ({


  data() {
    return {
      // Binded model property for "Sign In Form" switch button for "Remember Me" .
      text: null,
      fileList: null,
      data: {},
      activeKey: ['1'],
      imageUrl: null,
      initialForm: {
        title: '',
        description:'',
        is_published: false,
        questions: []
      },
      initialId: null,
      loading: false,
      form: {},
      rules: {
        title: [
          { required: true, message: 'Введите название', trigger: 'blur' },
        ],
        description: [
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
    ImageUpload,
    DeleteOutlined
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
    },
    activeKey(key) {
      console.log(key);
    },
    id(){
      this.initialId = this.id;
      this.resetForm();
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
    addOption(question){
      question.options.push({
        option_text: '',
        is_correct: false
      })
    },
    removeOption(question,optionIndex){
      question = question.options.splice(optionIndex, 1)
    },
    addQuestion(){
      this.form.questions.push({
        id: this.form.questions.length+1,
        question_text: '',
        options: [

        ],
      })
      // this.activeKey = [this.form.questions.length + 1]
    },
    removeQuestion(id){
      this.form.questions = this.form.questions.filter(item => item.id != id)
    },

    resetForm(){
      this.form = this.initialForm;
    },
    loadData(id){
      this.loading = true;
      this.$axios.get('/tests/'+id)
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
      let url = "tests";
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
              description: error,
            });
          });

    },
  }
})

</script>
