<template>
    <div>
        <div>
            <a-upload-dragger @change="handleFileStatus" :disabled="maxCount <= allImages.length" :headers="getHeaders" :show-upload-list="false" name="file" :maxCount="maxCount - allImages.length" :multiple="maxCount - allImages.length > 1" :action="`api/v1/${action}`">
                <p class="ant-upload-drag-icon">
                    <inbox-outlined></inbox-outlined>
                </p>
                <p class="ant-upload-text">Загрузить изображение({{allImages.length}}/{{maxCount}})</p>
            </a-upload-dragger>
        </div>
        <a-list v-if="allImages.length" item-layout="horizontal" :data-source="allImages">
            <template #renderItem="{ item }">
                <a-list-item>
                    <template #actions v-if="!item.loading">
                        <a-button @click.prevent="deleteImage(item.id)" type="link" danger key="list-delete">
                            <delete-outlined />
                        </a-button>
                    </template>
                    <a-list-item-meta description="">
                        <template #title>
                            {{ item.name }}
                        </template>
                        <template #avatar>
                            <!-- <a-image  :src="item.url" alt=""/> -->
                            <a-avatar v-if="!item.loading" shape="square" :src="item.url" />
                            <div v-else>
                                loading
                            </div>
                        </template>
                    </a-list-item-meta>
                </a-list-item>
            </template>
        </a-list>
    </div>
</template>
<script>
import { InboxOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import { notification } from 'ant-design-vue';

import AuthUtil from '@/libs/auth/auth';

export default ({
    props: [
        'action',
        'id',
        'images',
        'maxCount',

    ],
    components: {
        InboxOutlined,
        DeleteOutlined
    },

    watch: {
        async images() {
            if (Array.isArray(this.images)) {
                this.allImages = [...this.images];
            } else {
                this.allImages = [];
            }

            await this.getImages();
            this.emitSelectedIds();
        }
    },

    async mounted() {
        this.allImages = [...this.images];
        await this.getImages();
        this.emitSelectedIds();
    },

    data() {
        return {
            allImages: [],
        }
    },

    computed: {
        getHeaders() {
            return { 'Authorization': `Bearer ${AuthUtil.getAuthToken()}` }
        },

    },

    methods: {
        deleteImage(id) {
            this.loading = true;
            // let url = this.action;
            this.$axios.delete('media/' + id)
                .then(response => {
                    this.allImages = this.allImages.filter(item => item.id != id);
                    notification.success({
                        message: 'Успешно',
                    });
                })
                .catch(error => {
                    notification.error({
                        message: 'Ошибка',
                        description: error,
                    });
                })
                .then(() => {});
        },

        getImages() {
            this.allImages.forEach(obj => {
                obj.loading = false;
            });

        },

        emitSelectedIds() {
            const selectedIds = this.allImages
                .filter(obj => !obj.loading) // filter objects where loading is false
                .map(obj => obj.id); // extract the IDs of the remaining objects
            this.$emit('update:modelValue', selectedIds); // emit the selected IDs to parent component's v-model
        },

        handleFileStatus(info) {

            let file = info.file;
            let status = file.status;
            console.log(file)
            if (status === 'uploading') {
                // Check if file with the same UID already exists in allImages array
                const existingFile = this.allImages.find(obj => obj.id === file.uid);
                if (!existingFile) {
                    // Add new object to allImages array with loading = true and name = file.name, id = file.uid
                    this.allImages.push({ loading: true, name: file.name, id: file.uid });
                }
            } else if (status === 'error') {
                // Remove object from allImages array with id = file.uid
                this.allImages = this.allImages.filter(obj => obj.id !== file.uid);
            } else if (status === 'done') {
                // Find object in allImages array with id = file.uid
                const imageIndex = this.allImages.findIndex(obj => obj.id === file.uid);
                if (imageIndex >= 0) {
                    // Update object with id = file.uid to use response ID, name, and URL and set loading to false
                    let response = JSON.parse(file.xhr.response);
                    response = response.data;
                    const updatedImage = {
                        loading: false,
                        name: response.name,
                        id: response.id,
                        url: response.url,
                    };
                    this.allImages.splice(imageIndex, 1, updatedImage);
                    // Call emitSelectedIds method to update parent component's v-model
                    this.emitSelectedIds();
                }
            }

            console.log(this.allImages)

        }


    }
})

</script>
