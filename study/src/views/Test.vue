<template>
  <div>
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
      </a-card>
      <a-card class="mb-20" v-if="test.questions.length>0">
        <div class="card" v-if="!finish">
          <a-typography-title :level="4" class="mb-20">{{question.question_text}}</a-typography-title>
          <a-checkbox-group class="mb-20" v-model:value="activeQuestionAnswers" :options="options" />
          <a-button type="default" :disabled="activeQuestionAnswers.length < 1" block @click="nextQuestion">
            Ответить
          </a-button>
        </div>
        <div class="card" v-else>
          Тест окончен
          Итоговый балл: {{ this.score }}
          Правильных ответов : {{ this.rightAnswers }}
        </div>
      </a-card>
      <div v-else>
        <a-typography-title :level="4" type="secondary" class="mb-20">К этому тесту нет вопросов</a-typography-title>
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
      finish:false,
      activeQuestion: 0,
      loading: true,
      meta: null,
      activeQuestionAnswers:[],
      score: 0,
      rightAnswers: 0,
      answers: [],
    }
  },
  // watch: {
  //   $route(to, from) {
  //     if(!to.params.test_id){
  //       return
  //     }
  //     if (to.params.test_id !== this.test.id) {
  //       this.loadData();
  //     }
  //   },
  // },

  computed:{
    question(){
      return this.test.questions[this.activeQuestion]
    },
    highScore(){

    },
    questionAnswers(){
      let answers = []
      this.question.options.forEach(o => {
        if(o.is_correct){
          answers.push(o.id)
        }
      })
      return answers
    },
    options(){
      return this.test.questions[this.activeQuestion].options.map(item => {
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
    nextQuestion(){
      if(_.isEqual(this.activeQuestionAnswers, this.questionAnswers)){
        this.score += this.question.points ? this.question.points : 1;
        this.rightAnswers++;
      }
      this.answers.push({
        question_id: this.question.id,
        answers: this.activeQuestionAnswers
      })
      if(this.activeQuestion >= this.test.questions.length - 1){
        this.finish = true
        let url = `/faculties/${this.$route.params.faculty_id}/courses/${this.$route.params.course_id}/lessons/${this.$route.params.lesson_id}/test/${this.$route.params.test_id}/answer`
        this.$axios.post(url, this.answers)
            .then(response => {
              // console.log(response)
              this.result = response.data.data;
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
            this.test.questions = this.test.questions.sort((a,b) => a.position - b.position)
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
