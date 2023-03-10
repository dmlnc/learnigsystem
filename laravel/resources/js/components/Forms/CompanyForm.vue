<template>
    <a-drawer :title="'Компания ' + name" :width="320" :visible="visible" :body-style="{ paddingBottom: '80px' }" :footer-style="{ textAlign: 'right' }" @close="onClose">
        <a-skeleton active :loading="loading">
            <a-form layout="vertical" ref="formRef" :model="form" :rules="rules" @submit="handleSubmit" :hideRequiredMark="true">
                <a-form-item class="mb-10" label="Название" name="name" :colon="false">
                    <a-input v-model:value="form.name" />
                </a-form-item>
                <a-form-item class="mb-10" label="Изображение" name="image" :colon="false" style="display: block">
                    <ImageUpload v-model="form.images" action='companies/media' :images="form.logo" :maxCount="1"></ImageUpload>
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

import AuthUtil from '@/libs/auth/auth';


export default ({


    data() {
        return {
            text: null,
            data: null,
            initialForm: {
                name: '',
                logo: [],
                images: [],
            },
            initialId: null,
            loading: false,
            form: {},
            rules: {
                name: [
                    { required: true, message: 'Введите название', trigger: 'blur' },
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
                return this.form.name
            }
            return '';
        }
    },

    watch: {
        visible() {
            this.visibleForm = this.visible;
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
        this.resetForm();
    },

    methods: {

        resetForm() {
            this.form = { ...this.initialForm };
        },

        loadData(id) {
            this.loading = true;
            this.$axios.get('/companies/' + id)
                .then(response => {
                    let data = response.data.data;

                    if(data.logo != null){
                      data.logo = [data.logo];
                    }
                    else{
                      data.logo = [];
                    }
                    this.form = data;
                    // router.push({ name: 'Academy', params: {academy_id: } })
                })
                .catch(error => {
                    notification.error({
                        message: 'Ошибка',
                        description: error,
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
            let url = "companies";
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
                    if(AuthUtil.getCompany().id == this.initialId){
                      AuthUtil.setCompany(response.data.data);
                    }

                    // const token = response.data.token;
                    // setAuthToken(token);
                    // router.push({ name: 'Home' })
                })
                .catch(error => {
                    notification.error({
                        message: 'Ошибка',
                        description: error,
                    });
                });

        },
    }
})

</script>
