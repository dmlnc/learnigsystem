<template>
    <a-drawer :title="'Курс ' + name" :width="320" :visible="visible" :body-style="{ paddingBottom: '80px' }" :footer-style="{ textAlign: 'right' }" @close="onClose">
        <a-skeleton active :loading="loading">
            <a-form layout="vertical" ref="formRef" :model="form" :rules="rules" @submit="handleSubmit" :hideRequiredMark="true">
                <a-form-item class="mb-10" label="Название" name="title" :colon="false">
                    <a-input v-model:value="form.title" />
                </a-form-item>
                <a-form-item  class="mb-10" label="Изображение" name="image" :colon="false" style="display: block">
                    <ImageUpload v-model="form.images" action='courses/media' :images="form.media" :maxCount="1"></ImageUpload>
                </a-form-item>
                <!-- api/v1/courses/media -->
                <!--  <a-upload v-model:file-list="fileList" name="file" :multiple="false" list-type="picture-card" class="avatar-uploader" :show-upload-list="true" :headers="{'Authorization': `Bearer ${getAuthToken}`}" action="api/v1/courses/media">
                        <img v-if="imageUrl" :src="imageUrl" alt="avatar" />
                        <div v-else>
                            <div class="ant-upload-text">Upload</div>
                        </div>
                    </a-upload> -->
                <a-form-item class="mb-10" label="Факультет" name="faculties" :colon="false">
                    <a-select :labelInValue="false" v-model:value="form.faculties" mode="multiple" :options="data.academies" :field-names="{ label: 'label', value: 'value', options: 'children' }"></a-select>
                </a-form-item>
                <a-form-item class="mb-10" label="Описание" name="description" :colon="false">
                    <a-textarea v-model:value="form.description" />
                </a-form-item>
                <a-form-item>
                    <a-button type="primary" block html-type="submit">
                        Сохранить
                    </a-button>
                </a-form-item>
            </a-form>
        </a-skeleton>
    </a-drawer>
</template>
<script>
import { notification } from 'ant-design-vue';

import ImageUpload from '@/components/Images/ImageUpload.vue'

export default ({


    data() {
        return {
            text: null,
            data: {},
            imageUrl: null,
            initialForm: {
                title: '',
                description: '',
                faculties: [],
                media: [],
                images: [],
            },
            initialId: null,
            loading: false,
            form: {},
            rules: {
                title: [
                    { required: true, message: 'Введите название', trigger: 'blur' },
                ],
                description: [
                    { required: true, message: 'Введите описание', trigger: 'blur' },
                ],
            }
        }
    },

    props: [
        'id',
        'visible'
    ],
    components: {
        ImageUpload
    },
    computed: {
        name() {
            if (this.initialId != null) {
                return this.form.title
            }

            return '';
        }
    },

    beforeCreate() {
        // Creates the form and adds to it component's "form" property.
        // this.form = this.$form.createForm(this, { name: 'normal_login' });
    },

    watch: {
        visible() {
            this.visibleForm = this.visible;
            this.resetForm();
        },
        id() {
            this.initialId = this.id;
            if (this.id != null) {
                this.loadData(this.id)
            } else {
                this.loading = false;
            }

        },
    },

    mounted() {

        this.fetchCreate();
        this.resetForm();
    },

    methods: {

        resetForm() {
            this.form = {...this.initialForm};
        },

        fetchCreate() {
            this.$axios.get('/courses/create')
                .then(response => {
                    let academies = response.data.meta.academies
                    let transformedAcademies = academies.map(academy => ({
                        label: `Академия ${academy.name}`,
                        value: academy.id.toString(),
                        children: academy.faculties.map(faculty => ({
                            label: `Факультет ${faculty.name}`,
                            value: faculty.id,
                        }))
                    }));

                    this.data = {
                        academies: transformedAcademies
                    }
                })
        },


        loadData(id) {
            this.loading = true;
            this.$axios.get('/courses/' + id)
                .then(response => {
                    let transformedAcademies = response.data.data.faculties.map(faculty => (faculty.id));
                    this.form = response.data.data
                    this.form.faculties = transformedAcademies;

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

        onClose() {
            this.resetForm();
            // this.visibleForm = false;
            this.$emit('close');
        },

        handleSubmit(e) {
            e.preventDefault();
            // console.log(this.$refs.formRef.validate())
            // // this.$refs.formRef.target.validate();
            // console.log(this.form
            this.$refs.formRef
                .validate()
                .then((e) => {})
                .catch((e) => {
                    if (e.errorFields.length == 0) {
                        this.submitForm();
                    }
                })
        },

        submitForm() {
            let data = { ...this.form };
            let url = "courses";
            if (this.initialId != null) {
                data._method = "put";
                url = url + '/' + this.initialId;
            }

            let formData = data

            this.$axios.post(url, formData)
                .then(response => {
                    // console.log(response)
                    this.initialId = response.data.data.id;
                    this.loadData(this.initialId);
                    this.$emit('save');
                    notification.success({
                        message: 'Успешно',
                    });

                    // const token = response.data.token;
                    // setAuthToken(token);
                    // router.push({ name: 'Home' })
                })
                .catch(error => {
                    notification.error({
                        message: 'Ошибка',
                        //description: error,
                    });
                });

        },
    }
})

</script>
