<template>
    <div>
        <div class="quill-content" v-html="content"></div>
       <!--  <div v-if="isCollapsed" v-html="collapsedText"></div>
        <div v-else v-html="content"></div>
        <button @click="toggleCollapse">{{ isCollapsed ? 'Read more' : 'Read less' }}</button> -->
    </div>
</template>
<script>
export default {
    props: {
        content: {
            type: String,
            required: true
        },
        rows: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            isCollapsed: true,
        };
    },
    mounted() {},

    methods: {
        toggleCollapse() {
            this.isCollapsed = !this.isCollapsed;
        }
    }
};

</script>

<style scoped>
  .expandable-text p:last-child{
      margin-bottom: 0!important;
  }
</style>
