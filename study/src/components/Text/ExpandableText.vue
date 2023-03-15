<template>
    <div>
        <div ref="contentWrapper" class=" quill-content mb-10" v-html="content" :class="{'expandable-text' : isCollapsed}"></div>
        <a-button block @click="toggleCollapse">{{ isCollapsed ? 'Читать полностью' : 'Скрыть' }}</a-button>

        
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
            isCollapsed: false,
            showButton: false,
        };
    },
    mounted() {
        this.checkHeight();
    },

    methods: {

        checkHeight() {
            const contentWrapper = this.$refs.contentWrapper;
            console.log(contentWrapper.offsetHeight);
            // const content = this.$refs.content;
            if (contentWrapper.offsetHeight > 150) {
                this.isCollapsed = true;
            } else {
                this.isCollapsed = false;
            }
        },

        toggleCollapse() {
            this.isCollapsed = !this.isCollapsed;
        }
    }
};

</script>
<style scoped>
.expandable-text {
    height: 150px;
    overflow: hidden;
}

</style>
