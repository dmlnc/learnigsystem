<!-- 
	This is the dashboard page, it uses the dashboard layout in: 
	"./layouts/Dashboard.vue" .
 -->
<template>
    <div>

        <a-typography-title :level="5" class="mb-40">Главная</a-typography-title>


        <a-row :gutter="24">
            <!-- <a-col :span="24" :lg="12" :xl="8" class="mb-24">
                <a-card>
                    <template #cover>
                        <img alt="example" src="https://doodleipsum.com/900x525/flat?i=7e83ba77e602c180346437548c5df08e" />
                    </template>
                    <template class="ant-card-actions" #actions>
                        <router-link :to="{ name: 'Academies-page'}"> Перейти</router-link>
                    </template>
                    <a-card-meta title="Академии" description="Страница просмотра всех академий">
                    </a-card-meta>
                </a-card>
            </a-col> -->
            <a-col :span="24" :lg="12" :xl="8" class="mb-24">
                <a-card>
                	<template #cover>
                        <img alt="example" src="https://doodleipsum.com/900x525/flat?i=5087a915dddcb6e95b5262e070332347" />
                    </template>
                    <template class="ant-card-actions" #actions>
                        <router-link :to="{ name: 'Academies-page'}"> Перейти</router-link>
                    </template>
                    <a-card-meta title="Обучение" description="Страница просмотра всех курсов">
                    </a-card-meta>
                </a-card>
            </a-col>
            <!-- <a-col :span="24" :lg="12" :xl="8" class="mb-24">
                <a-card>
                	<template #cover>
                        <img alt="example" src="https://doodleipsum.com/900x525/flat?i=330e732c9b45f0dbb3a6728d57e02977" />
                    </template>
                    <template class="ant-card-actions" #actions>
                        <router-link :to="{ name: 'Users-page'}"> Перейти</router-link>
                    </template>
                    <a-card-meta title="Пользователи" description="Страница просмотра всех пользователей">
                    </a-card-meta>
                </a-card>
            </a-col> -->
        </a-row>
    </div>
</template>
<script>
// Bar chart for "Active Users" card.
// import CardBarChart from '../components/Cards/CardBarChart.vue' ;

// Line chart for "Sales Overview" card.
// import CardLineChart from '../components/Cards/CardLineChart.vue' ;



export default ({
    data() {
        return {
            posts: [],

        }
    },

    mounted() {
        this.getPosts();
    },

    methods: {

        getPosts(){
            this.$axios.get(`/posts`)
                .then(response => {
                    this.posts = response.data.data;
                    // this.parentName = response.data.meta.name;
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
        }
    }

})

</script>
<style lang="scss">
</style>
