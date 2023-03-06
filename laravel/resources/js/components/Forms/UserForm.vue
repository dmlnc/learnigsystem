<template>
    <a-drawer :title="'Пользователь ' + name" :width="320" :visible="visible" :body-style="{ paddingBottom: '80px' }" :footer-style="{ textAlign: 'right' }" @close="onClose">
        <a-skeleton active :loading="loading">
            <a-form layout="vertical" ref="formRef" :model="form" :rules="rules" @submit="handleSubmit" :hideRequiredMark="true">
                <a-form-item class="mb-10" label="Факультет" name="faculties" :colon="false">
                    <vSelect :options="data.academies" :reduce="val=>val.id" :multiple="true" v-model="form.faculties" :clearable="true" :closeOnSelect="false" label="name" :selectable="car => !car.disabled">
                    </vSelect>
                </a-form-item>
                <a-form-item class="mb-10" label="Роль" name="faculties" :colon="false">
                    <vSelect label="title" :options="data.roles" :reduce="val=>val.id" :multiple="true" v-model:value="form.roles" :clearable="false" />
                    <!-- <a-select v-model:value="form.roles" mode="multiple" :options="data.roles"></a-select> -->
                </a-form-item>
                <a-form-item class="mb-10" label="Имя" name="name" :colon="false">
                    <a-input v-model:value="form.name" />
                </a-form-item>
                <a-form-item class="mb-10" label="Почта" name="email" :colon="false">
                    <a-input v-model:value="form.email" />
                </a-form-item>
                <a-form-item v-if="initialId == null" class="mb-10" label="Пароль" name="password" :colon="false">
                    <a-input type="password" v-model:value="form.password" />
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

import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

export default ({

    components: {
        vSelect,
    },

    data() {
        return {
            // Binded model property for "Sign In Form" switch button for "Remember Me" .
            text: null,
            initialForm: {
                name: '',
                faculties: [],
                email: '',
                password: '',
                roles: [],
            },
            data: {},
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

    computed: {
        name() {
            if (this.initialId != null) {
                return this.form.name
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
            this.form = this.initialForm;
        },

        fetchCreate() {
            this.$axios.get('/users/create')
                .then(response => {
                    let academies = response.data.meta.academies

                    let transformedAcademies = academies.flatMap(academy => [
                        { name: `Академия ${academy.name}`, disabled: true },
                        ...academy.faculties.map(faculty => ({
                            name: `Факультет ${faculty.name}`,
                            id: faculty.id,
                            disabled: false,
                        }))
                    ]);

                    // let transformedAcademies = academies.map(academy => ({
                    //     label: `Академия ${academy.name}`,
                    //     options: academy.faculties.map(faculty => ({
                    //         label: `Факультет ${faculty.name}`,
                    //         value: faculty.id.toString(),
                    //     }))
                    // }));
                    // let transformedRoles = response.data.meta.roles.map(role => ({
                    //     label: role.title,
                    //     id: role.id.toString(),
                    // }));

                    // console.log(transformedAcademies)

                    this.data = {
                        roles: response.data.meta.roles,
                        academies: transformedAcademies
                    }
                    // this.form = response.data.data;
                    // router.push({ name: 'Academy', params: {academy_id: } })
                })
        },

        loadData(id) {
            this.loading = true;
            this.$axios.get('/users/' + id)
                .then(response => {
                    // let transformedRoles = response.data.data.roles.map(role => ({
                    //   label: role.title,
                    //   value: role.id.toString(),
                    // }));




                    this.form = response.data.data
                    // this.form.roles = transformedRoles;
                    // this.form.faculties = transformedAcademies;

                    // router.push({ name: 'Academy', params: {academy_id: } })
                })
                .catch(error => {
                    notification.error({
                        message: 'Ошибка loadData',
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
            let url = "users";
            if (this.initialId != null) {
                data._method = "put";
                url = url + '/' + this.initialId;
            }

            console.log(data);

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
                        message: 'Ошибка submitForm',
                        description: error,
                    });
                });

        },
    }
})

</script>
