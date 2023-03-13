<template>
    <div>
        <a-typography-title :level="5" class="mb-40">Обучение</a-typography-title>
        <a-skeleton active v-for="temp in [1,2,3]" :key="temp" v-if="loading" :loading="loading">
        </a-skeleton>
        <a-card v-for="academy in academies" class="mb-10">
            <template #title>
                <a-avatar class="mr-10" :src="`https://doodleipsum.com/100x100/avatar?bg=lightgray&n=${academy.id}`" />
                Академия {{academy.name}}
            </template>
            <a-card v-for="faculty in academy.faculties">
                <template #title>
                    <a-avatar class="mr-10" :src="`https://doodleipsum.com/100x100/avatar?bg=lightgray&n=${academy.id}-${faculty.id}`" />
                    Факультет {{faculty.name}}
                </template>
                <template #extra>
                    <router-link class="text-primary"  :to="{ name: 'Courses-page', params: {faculty_id : faculty.id}}"> Перейти </router-link>
                </template>
            </a-card>
        </a-card>
        <!-- / Counter Widgets -->
        <!-- Cards -->
        <a-row :gutter="24" type="flex" align="stretch">
        </a-row>
        <!-- / Cards -->
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
            academies: [],
            loading: true,

        }
    },

    mounted() {
        this.loadData();
    },

    methods: {

        loadData() {
            this.loading = true;
            this.$axios.get('/faculties')
                .then(response => {
                    console.log(response)
                    this.academies = response.data.data;
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
м