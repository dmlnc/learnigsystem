<template>
    <div>
        <a-typography-title :level="5" class="mb-40">
            <a-typography-text type="secondary">Факультет {{parentName}}</a-typography-text>
            <a-divider type="vertical" />
            Курсы
        </a-typography-title>
        <a-row :gutter="24">
            <a-col :span="24" :lg="12" :xl="8" class="mb-24" v-for="temp in [1,2,3]" :key="temp" v-if="loading">
                <a-skeleton active :loading="loading">
                </a-skeleton>
            </a-col>
            <a-col :span="24" :lg="12" :xl="8" class="mb-24" v-for="course in courses" :key="course.id">
                <a-skeleton active :loading="loading">
                    <router-link class="text-primary" :to="{ name: 'Lessons-page', params: {course_id : course.id}}">
                        <a-card>
                            <template #cover>
                                <img v-if="course.thumbnail == null" :src="'https://doodleipsum.com/900x525/flat?n='+course.id" />
                                <img v-else :src="course.thumbnail.url" />
                                <!-- <img alt="example" :src="'https://doodleipsum.com/900x525/flat?n='+course.id" /> -->
                            </template>
                            <template class="ant-card-actions" #actions>
                                Уроки
                            </template>
                            <a-card-meta :title="'Курс '+ course.title" :description="course.description">
                            </a-card-meta>
                        </a-card>
                    </router-link>
                </a-skeleton>
            </a-col>
        </a-row>
    </div>
</template>
<script>
// Bar chart for "Active Users" card.
// import CardBarChart from '../components/Cards/CardBarChart.vue' ;

// Line chart for "Sales Overview" card.
// import CardLineChart from '../components/Cards/CardLineChart.vue' ;



import { notification } from 'ant-design-vue';

export default ({
    components: {},
    data() {
        return {
            courses: [],
            loading: true,
            parentName: '',

        }
    },

    mounted() {
        this.loadData();
    },

    methods: {

        loadData() {
            this.loading = true;
            this.$axios.get(`/faculties/${this.$route.params.faculty_id}/courses`)
                .then(response => {
                    this.courses = response.data.data;
                    this.parentName = response.data.meta.name;
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


    }
})

</script>
<style lang="scss">
</style>
