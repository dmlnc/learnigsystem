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
            Урок {{lesson.title}}
        </a-typography-title>
        <a-skeleton active v-for="temp in [1,2,3]" :key="temp" v-if="loading" :loading="loading">
        </a-skeleton>
        <div v-if="!loading">
            <a-card class="mb-20">
                <template #title>
                    <a-row :gutter="24" type="flex" align="middle">
                        <a-col :span="24" :lg="8" :xl="8" v-if="lesson.media.length>0">
                            <a-image v-if="lesson.media.length>0" :src="lesson.media[0].url" alt="" />
                        </a-col>
                        <a-col :span="24" :lg="16" :xl="16">
                            <a-typography-title :level="5">Урок {{lesson.title}}</a-typography-title>
                            <p class="mb-0 font-weight-normal">{{lesson.short_text}}</p>
                        </a-col>
                    </a-row>
                </template>
                <a-row :gutter="24" type="flex" justify="center">
                    <a-col :span="24" :lg="12" :xl="12" class="lesson-content" v-html="lesson.long_text">
                    </a-col>
                </a-row>
            </a-card>
            <div v-if="lesson.tests.length>0">
                <a-typography-title :level="4" class="mb-20">Тесты</a-typography-title>
                <a-row :gutter="24">
                    <a-col :span="24" :lg="12" :xl="8" class="mb-24 h-full" v-for="test in lesson.tests" :key="test.id">
                        <!-- TODO: Установить пройденные тесты, роут на тест -->
                        <router-link class="h-full" :to="{ name: 'Lessons-page', params: {course_id : test.id}}">
                            <a-card class="h-full">
                                <!-- <template #cover>
                                    <img v-if="test.thumbnail == null" :src="'https://doodleipsum.com/900x525/flat?n='+test.id" />
                                    <img v-else :src="test.thumbnail.url" />
                                 
                                </template> -->
                                <template class="ant-card-actions" #actions>
                                    <span v-if="test.test_results.length > 0">Тест пройден</span>
                                    <span v-else>К тесту</span>
                                </template>
                                <a-card-meta :description="test.description">
                                    <template #title>
                                        <a-avatar v-if="test.thumbnail == null" class="mr-10" :src="`https://doodleipsum.com/100x100/avatar?bg=lightgray&n=${test.id}`" />
                                        <a-avatar v-else class="mr-10" :src="test.thumbnail.url" />
                                        Тест {{test.title}}
                                        <a-typography-text  v-if="test.test_results.length > 0" type="success">
                                            <check-circle-outlined />
                                        </a-typography-text>

                                        
                                    </template>
                                </a-card-meta>
                            </a-card>
                        </router-link>
                    </a-col>
                </a-row>
            </div>
            <div v-else>
                <a-typography-title :level="4" type="secondary" class="mb-20">К этому уроку нет тестов</a-typography-title>
            </div>
            <a-row v-if="meta!=null" :gutter="24">
                <a-col :span="24" :lg="12" :xl="12" v-if="meta.lessons.previousLesson!=null">
                    <router-link class="text-primary" :to="{ name: 'Lesson-page', params: {lesson_id : meta.lessons.previousLesson.id}}">
                        <a-card>
                            <a-card-meta :description="'Урок '+meta.lessons.previousLesson.title">
                                <template #title>
                                    <arrow-left-outlined /> Предыдущий урок 
                                </template>
                            </a-card-meta>
                        </a-card>
                    </router-link>
                </a-col>
                <a-col :span="24" :lg="12" :xl="12" v-else>
                </a-col>
                <a-col :span="24" :lg="12" :xl="12" v-if="meta.lessons.nextLesson!=null">
                    <router-link class="text-primary" :to="{ name: 'Lesson-page', params: {lesson_id : meta.lessons.nextLesson.id}}">
                        <a-card>
                            <a-card-meta  :description="'Урок '+meta.lessons.nextLesson.title">
                                <template #title>
                                    Следующий урок <arrow-right-outlined />
                                </template>
                            </a-card-meta>
                        </a-card>
                    </router-link>
                </a-col>
            </a-row>
        </div>
        
    </div>
</template>
<script>
import { notification } from 'ant-design-vue';
import { ArrowRightOutlined, ArrowLeftOutlined, CheckCircleOutlined   } from '@ant-design/icons-vue';


export default ({
    components: {
        ArrowRightOutlined, ArrowLeftOutlined, CheckCircleOutlined
    },
    data() {
        return {
            lesson: {
                id: null
            },
            loading: true,
            meta: null,

        }
    },
    watch: {
        $route(to, from) {
            if(!to.params.lesson_id){
                return
            }
            if (to.params.lesson_id !== this.lesson.id) {
               this.loadData();
            }
        },

    },


    mounted() {
        this.loadData();
    },

    methods: {

        loadData() {
            this.loading = true;
            this.$axios.get(`/faculties/${this.$route.params.faculty_id}/courses/${this.$route.params.course_id}/lessons/${this.$route.params.lesson_id}`)
                .then(response => {
                    this.lesson = response.data.data;
                    this.meta = response.data.meta;
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
