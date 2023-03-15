<!-- 
    This is the dashboard page, it uses the dashboard layout in: 
    "./layouts/Dashboard.vue" .
 -->
<template>
    <div>
        <a-typography-title :level="5" class="mb-40">Главная</a-typography-title>
        <a-row :gutter="24">
            <a-col :span="24" :lg="12" :xl="10" class="mb-24" v-if="posts.length>0">
                <a-card v-for="post in posts" class="mb-20">
                    <template #cover>
                    </template>
                    <a-card-meta>
                        <template #title>
                            <a-image v-if="post.media != null" :src="post.media.url" class="mr-10" :width="64"></a-image>
                            {{post.title}}
                        </template>
                        <template #description>
                            <expandable-text :content="post.text" :rows="3"></expandable-text>
                            <!--   <a-typography-paragraph :ellipsis="{ rows: 1, expandable: true, symbol: 'more' }"  :content="">
                                
                            </a-typography-paragraph> -->
                        </template>
                    </a-card-meta>
                </a-card>
                <!-- <a-button v-if="posts.length >= perPage" @click="loadMore">Load more</a-button> -->
            </a-col>
            <a-col :span="24" :lg="12" :xl="14" class="mb-24">
                <a-row :gutter="24">
                    <a-col :span="24" :lg="12" :xl="12" class="mb-24">
                        <a-card>
                            <template #cover>
                                <img src="https://doodleipsum.com/900x525/flat?i=5087a915dddcb6e95b5262e070332347" />
                            </template>
                            <template class="ant-card-actions" #actions>
                                <router-link :to="{ name: 'Academies-page'}"> Перейти</router-link>
                            </template>
                            <a-card-meta title="Обучение" description="Страница просмотра всех курсов">
                            </a-card-meta>
                        </a-card>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </div>
</template>
<script>
import ExpandableText from '@/components/Text/ExpandableText.vue';

export default ({
    data() {
        return {
            posts: [],
            page: 1
        }
    },
    components: {
        ExpandableText,
    },

    mounted() {
        this.getPosts();
    },

    methods: {

        getPosts() {
            this.$axios.get(`/posts`, {
                    params: {
                        page: this.page
                    }
                })
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
