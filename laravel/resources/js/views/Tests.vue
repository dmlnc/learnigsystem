<!--
  This is the dashboard page, it uses the dashboard layout in:
  "./layouts/Dashboard.vue" .
 -->
<template>
    <div>
        <TestForm :visible="formVisible" :id="formId" @close="hideForm" @save="loadData" />
        <a-typography-title :level="5" class="mb-40">
            <router-link  v-if="meta?.course.id" :to="{'name': 'Lessons-page', params: {'course_id': meta?.course.id}}"><a-typography-text type="secondary">Курс {{meta?.course.title}}</a-typography-text></router-link>
            <a-divider type="vertical" />
            <a-typography-text type="secondary">Урок {{meta?.lesson.title}}</a-typography-text>
            <a-divider type="vertical" />
            Тесты
        </a-typography-title>
        <a-row :gutter="24">
            <a-col :span="24" :lg="12" :xl="8" class="mb-24" v-for="temp in [1,2,3]" :key="temp" v-if="loading">
                <a-skeleton active :loading="loading">
                </a-skeleton>
            </a-col>
            <a-col :span="24" :lg="12" :xl="8" class="mb-24" v-for="test in tests" :key="test.id">
                <a-skeleton active :loading="loading">
                    <a-card>
                        <template #cover>
                            <img v-if="test.thumbnail == null" :src="'https://doodleipsum.com/900x525/flat?n='+test.id" />
                            <img v-else :src="test.thumbnail.url" />
                        </template>
                        <template class="ant-card-actions" #actions>
                            <div @click="showForm(test.id)"><svg viewBox="64 64 896 896" data-icon="edit" width="1em" height="1em" fill="currentColor" aria-hidden="true" focusable="false" class="">
                                    <path d="M257.7 752c2 0 4-.2 6-.5L431.9 722c2-.4 3.9-1.3 5.3-2.8l423.9-423.9a9.96 9.96 0 0 0 0-14.1L694.9 114.9c-1.9-1.9-4.4-2.9-7.1-2.9s-5.2 1-7.1 2.9L256.8 538.8c-1.5 1.5-2.4 3.3-2.8 5.3l-29.5 168.2a33.5 33.5 0 0 0 9.4 29.8c6.6 6.4 14.9 9.9 23.8 9.9zm67.4-174.4L687.8 215l73.3 73.3-362.7 362.6-88.9 15.7 15.6-89zM880 836H144c-17.7 0-32 14.3-32 32v36c0 4.4 3.6 8 8 8h784c4.4 0 8-3.6 8-8v-36c0-17.7-14.3-32-32-32z"></path>
                                </svg></div>
                            <!--              <router-link class="text-primary"  :to="{ name: 'Test-page', params: {test_id : test.id}}"> Перейти</router-link>-->
                            <a-popconfirm :title="'Уверены, что хотите удалить '+test.title+ '?'" ok-text="Да" cancel-text="Нет" @confirm="deleteInstance(test.id)">
                                <div class="text-danger">
                                    <svg viewBox="64 64 896 896" data-icon="delete" width="1em" height="1em" fill="currentColor" aria-hidden="true" focusable="false" class="">
                                        <path d="M360 184h-8c4.4 0 8-3.6 8-8v8h304v-8c0 4.4 3.6 8 8 8h-8v72h72v-80c0-35.3-28.7-64-64-64H352c-35.3 0-64 28.7-64 64v80h72v-72zm504 72H160c-17.7 0-32 14.3-32 32v32c0 4.4 3.6 8 8 8h60.4l24.7 523c1.6 34.1 29.8 61 63.9 61h454c34.2 0 62.3-26.8 63.9-61l24.7-523H888c4.4 0 8-3.6 8-8v-32c0-17.7-14.3-32-32-32zM731.3 840H292.7l-24.2-512h487l-24.2 512z"></path>
                                    </svg>
                                </div>
                            </a-popconfirm>
                        </template>
                        <a-card-meta :title="'Тест '+ test.title" :description="test.description">
                        </a-card-meta>
                    </a-card>
                </a-skeleton>
            </a-col>
            <a-col :span="24" :lg="12" :xl="8" class="mb-24">
                <a-skeleton active :loading="loading">
                    <a-card>
                        <template #cover>
                            <img alt="example" src="https://doodleipsum.com/900x525/outline?i=f9b600d9d2fd687422cee8090c384c28" />
                        </template>
                        <template class="ant-card-actions" #actions>
                            <div @click="showForm(null)"> Создать</div>
                        </template>
                        <a-card-meta title="Создать тест" description="">
                        </a-card-meta>
                    </a-card>
                </a-skeleton>
            </a-col>
        </a-row>
        <!-- / Counter Widgets -->
        <!-- Cards -->
        <a-row :gutter="24" type="flex" align="stretch">
        </a-row>
        <!-- / Cards -->
    </div>
</template>
<script>
import TestForm from '@/components/Forms/TestForm.vue'
// Bar chart for "Active Users" card.
// import CardBarChart from '../components/Cards/CardBarChart.vue' ;

// Line chart for "Sales Overview" card.
// import CardLineChart from '../components/Cards/CardLineChart.vue' ;



import { notification } from 'ant-design-vue';

export default ({
    components: {
        TestForm,
    },
    data() {
        return {
            tests: [],
            formVisible: false,
            formId: null,
            loading: true,
            meta: null,

        }
    },

    mounted() {
        this.loadData();
    },

    methods: {

        loadData() {
            this.loading = true;
            this.$axios.get('/lessons/' + this.$route.params.lesson_id + '/tests')
                .then(response => {
                    this.tests = response.data.data;
                    this.meta = response.data.meta;
                    // router.push({ name: 'Academy', params: {academy_id: } })
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

        deleteInstance(id) {
            this.loading = true;

            this.$axios.delete('/lessons/' + this.$route.params.lesson_id + '/tests/' + id)
                .then(response => {
                    notification.success({
                        message: 'Успешно',
                    });
                })
                .catch(error => {
                    notification.error({
                        message: 'Ошибка',
                        // //description: error,
                    });
                })
                .then(() => {

                    this.loadData();
                    // this.loading = false;
                });
        },

        showForm(id) {
            this.formId = id
            this.formVisible = true
        },
        hideForm() {
            this.formVisible = false;
            this.formId = null
        }
    }
})

</script>
<style lang="scss">
</style>
