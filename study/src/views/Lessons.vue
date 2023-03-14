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
            Уроки
        </a-typography-title>
        <a-skeleton active v-for="temp in [1,2,3]" :key="temp" v-if="loading" :loading="loading">
        </a-skeleton>
        <div v-for="lesson in lessons" class="mb-10">
            <router-link class="text-primary" :to="{ name: 'Lesson-page', params: {lesson_id : lesson.id}}">
                <a-card :class="{
                    'ant-card-minimal': (lesson.finished_tests_count == lesson.tests_count) && lesson.tests_count!=0
                }" :size="((lesson.finished_tests_count == lesson.tests_count) && lesson.tests_count!=0) ? 'small' : 'default'">
                    <template #title>
                        <a-avatar v-if="lesson.thumbnail == null" class="mr-10" shape="square" :size="((lesson.finished_tests_count == lesson.tests_count) && lesson.tests_count!=0) ? 30 : 64" :src="`https://doodleipsum.com/100x100/avatar?bg=lightgray&n=${lesson.id}`" />
                        <a-avatar v-else class="mr-10" shape="square" :size="((lesson.finished_tests_count == lesson.tests_count) && lesson.tests_count!=0) ? 30 : 64" :src="lesson.thumbnail.url" />
                        Урок {{lesson.title}}
                    </template>
                    <template #extra>
                        <a-typography-text v-if="lesson.tests_count!=0" :type="((lesson.finished_tests_count == lesson.tests_count) && lesson.tests_count!=0) ? 'success' : 'secondary'">{{lesson.finished_tests_count}} / {{lesson.tests_count}}</a-typography-text>
                    </template>
                    {{lesson.short_text}}
                </a-card>
            </router-link>
        </div>
    </div>
</template>
<script>
import { notification } from 'ant-design-vue';

export default ({
    components: {},
    data() {
        return {
            lessons: [],
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
            this.$axios.get(`/faculties/${this.$route.params.faculty_id}/courses/${this.$route.params.course_id}/lessons`)
                .then(response => {
                    this.lessons = response.data.data;
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


    }
})

</script>
<style lang="scss" scoped>
.ant-card-minimal {
    background: rgba(255, 255, 255, 0.66) !important;
    box-shadow: none;
}

</style>
