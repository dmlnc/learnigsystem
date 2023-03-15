<template>
  <div>
    <a-typography-title :level="5" class="mb-40">
      <router-link v-if="meta!=null" :to="{ name: 'Courses-page', params: {faculty_id : meta.faculty.id}}">
        <a-typography-text type="secondary">Факультет {{meta.faculty.name}}</a-typography-text>
      </router-link>
      <a-divider type="vertical" />
      <router-link v-if="meta!=null" :to="{ name: 'Lessons-page', params: {course_id : meta.course.id}}">
        <a-typography-text type="secondary">Курс {{meta.course.name}}</a-typography-text>
      </router-link>
      <a-divider type="vertical" />
      <router-link v-if="meta!=null" :to="{ name: 'Lesson-page', params: {lesson_id : meta.lesson.id}}">
        <a-typography-text type="secondary">Урок {{meta.lesson.name}}</a-typography-text>
      </router-link>
      <a-divider type="vertical" />
      Тест {{test.title}}
    </a-typography-title>
    <a-skeleton active v-for="temp in [1,2,3]" :key="temp" v-if="loading" :loading="loading">
    </a-skeleton>
    <div v-if="!loading">
      <a-card class="mb-20">
        <template #title>
          <a-row :gutter="24" type="flex" align="middle">
            <a-col :span="24" :lg="8" :xl="8" v-if="test.thumbnail">
              <a-image :src="test.thumbnail.url" alt="" />
            </a-col>
            <a-col :span="24" :lg="16" :xl="16">
              <a-typography-title :level="5">Тест {{test.title}}</a-typography-title>
              <p class="mb-0 font-weight-normal">{{test.description}}</p>
            </a-col>
          </a-row>
        </template>
        <a-progress v-if="test.questions" :percent="Math.floor(activeQuestion/test.questions.length * 100)" />
      </a-card>
      <a-card class="mb-20">
        <div class="card" v-if="test.questions != null && test?.questions.length > 0 && !meta.testFinished">
          <a-typography-title :level="4" class="mb-20">{{question.question_text}}</a-typography-title>
          <div class="mb-20">
            <a-image v-if="question.thumbnail" :src="question.thumbnail.url" alt="" />
          </div>
          <a-checkbox-group class="mb-20" v-model:value="activeQuestionAnswers" :options="options" />
          <a-button type="default" :disabled="activeQuestionAnswers.length < 1" block @click="nextQuestion">
            Ответить
          </a-button>
        </div>
        <div class="card" v-if="testResult?.score >= 0 && meta.testFinished">
          <a-typography-title :level="4" class="mb-20">Тест окончен</a-typography-title>
          <div class="d-flex">
            <a-progress
                type="circle"
                :stroke-color="{
                '0%': '#108ee9',
                '100%': '#87d068',
              }"
                :percent="Math.round(testResult.score/testResult.maxScore  * 100)"
            />
            <div class="ml-20 d-flex" style="flex-direction: column;justify-content: center;">
              <p>  Итоговый балл: {{ testResult.score }}</p>
              <p>  Максимальный балл: {{ testResult.maxScore }}</p>
              <p>  Правильных ответов: {{ testResult.userCorrectAnswers }}</p>
            </div>
          </div>
        </div>
      </a-card>
      <div class="card" v-if="meta!=null && meta.testFinished">
        <a-row  class="mb-20" :gutter="24">
          <a-col :span="24" :lg="12" :xl="12" v-if="meta.tests.previousTest !=null">
            <router-link class="text-primary" :to="{ name: 'Test-page', params: {test_id : meta.tests.previousTest.id}}">
              <a-card>
                <a-card-meta :description="'Тест '+meta.tests.previousTest.title">
                  <template #title>
                    <arrow-left-outlined /> Назад
                  </template>
                </a-card-meta>
              </a-card>
            </router-link>
          </a-col>
          <a-col :span="24" :lg="12" :xl="12" v-else>
          </a-col>
          <a-col :span="24" :lg="12" :xl="12" v-if="meta.tests.nextTest!=null">
            <router-link class="text-primary" :to="{ name: 'Test-page', params: {test_id : meta.tests.nextTest.id}}">
              <a-card>
                <a-card-meta  :description="'Тест '+meta.tests.nextTest.title">
                  <template #title>
                    Далее <arrow-right-outlined />
                  </template>
                </a-card-meta>
              </a-card>
            </router-link>
          </a-col>
        </a-row>
      </div>
    </div>
  </div>
</template>
<script>
import { notification } from 'ant-design-vue';
import { ArrowRightOutlined, ArrowLeftOutlined, CheckCircleOutlined   } from '@ant-design/icons-vue';
import _ from 'lodash';

export default ({
  components: {
    ArrowRightOutlined, ArrowLeftOutlined, CheckCircleOutlined
  },
  data() {
    return {
      test: {
        id: null
      },
      activeQuestion: 0,
      loading: true,
      activeQuestionAnswers:[],
      answers: [],
      testResult: null,
      meta: null
    }
  },
  watch: {
    $route(to, from) {
      if(!to.params.test_id){
        return
      }
      if (to.params.test_id !== this.test.id) {
        this.resetTest();

      }
    },
  },

  computed:{
    question(){
      return this.test?.questions[this.activeQuestion]
    },
    options(){
      return this.test?.questions[this.activeQuestion]?.options.map(item => {
        return {
          label: item.option_text,
          value: item.id
        }
      })
    }
  },
  mounted() {
    this.loadData();
  },

  methods: {
    resetTest(){
      this.test = {
        id: null
      }
      this.answers = [];
      this.activeQuestion = 0
      this.loading = true,
      this.meta = null;
      this.testResult = null;
      this.loadData();
    },
    nextQuestion(){
      this.answers.push({
        id: this.question.id,
        answers: this.activeQuestionAnswers
      })
      if(this.activeQuestion >= this.test?.questions.length - 1){
        this.activeQuestion++;
        this.meta.testFinished = true
        let url = `/faculties/${this.$route.params.faculty_id}/courses/${this.$route.params.course_id}/lessons/${this.$route.params.lesson_id}/test/${this.$route.params.test_id}/answer`
        this.$axios.post(url, {answers: this.answers})
            .then(response => {
              this.testResult = response.data.data;
            })
            .catch(error => {
              notification.error({
                message: 'Ошибка',
                //description: error,
              });
            });
      } else {
        this.activeQuestion++;
      }

    },
    onCheckAllChange(id){
      if(this.activeQuestionAnswers.find(i => i == id)){
        this.activeQuestionAnswers.remove(id)
      } else {
        this.activeQuestionAnswers.push(id)
      }
    },
    loadData() {
      this.loading = true;
      this.$axios.get(`/faculties/${this.$route.params.faculty_id}/courses/${this.$route.params.course_id}/lessons/${this.$route.params.lesson_id}/test/`+ this.$route.params.test_id)
          .then(response => {
            this.test = response.data.data;
            this.meta = response.data.meta
            if(response.data.meta.testResult){
              this.testResult = response.data.meta.testResult
            }
            if(this.test.questions){
              this.test.questions = this.test.questions.sort((a,b) => a.position - b.position)
            }

            // this.meta = response.data.meta;
          })
          .catch(error => {
            notification.error({
              message: 'Ошибка',
              //description: error,
            });
          })
          .then(() => {
            this.loading = false;
          });
    },


  }
})

</script>
<style lang="scss" scoped>
.font-weight-normal {
  font-weight: 400;
}

</style>
