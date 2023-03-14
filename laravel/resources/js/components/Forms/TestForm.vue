<template>
    <a-drawer :title="'Тест ' + name" :width="992" :visible="visible" :body-style="{ paddingBottom: '80px' }" :footer-style="{ textAlign: 'right' }" @close="onClose">
        <a-skeleton active :loading="loading">
            <a-form layout="vertical" ref="formRef" :model="form" :rules="rules" @submit="handleSubmit" :hideRequiredMark="true">
                <a-tabs default-active-key="1" @change="callback">
                    <a-tab-pane key="1" tab="Основные">
                        <a-form-item class="mb-10" label="Название" name="title" :colon="false">
                            <a-input v-model:value="form.title" />
                        </a-form-item>
                        <a-form-item class="mb-10" label="Описание" name="description" :colon="false">
                            <a-textarea v-model:value="form.description" />
                        </a-form-item>
                        <a-form-item class="mb-10" label="Изображение" name="image" :colon="false">
                            <ImageUpload v-model="form.images" action='tests/media' :images="form.media" :maxCount="1"></ImageUpload>
                        </a-form-item>
                        <a-checkbox v-model:checked="form.is_published">
                            Опубликовать
                        </a-checkbox>
                    </a-tab-pane>
                    <a-tab-pane key="2" tab="Вопросы" force-render>
                        <draggable item-key="randomString" :list="form.questions" class="list-group" handle=".handle" @change="updatePositions">
                            <template #item="{element,index}">
                                <a-collapse v-model:activeKey="activeKey">
                                    <a-collapse-panel :key="element.id" class="mb-10">
                                        <template #extra>
                                            <a-popconfirm @click.stop :title="'Уверены, что хотите удалить вопрос №'+(index+1)+ '?'" ok-text="Да" cancel-text="Нет" @confirm="removeQuestion(index)">
                                                <div class="text-danger">
                                                    <DeleteOutlined />
                                                </div>
                                            </a-popconfirm>
                                        </template>
                                        <template #header>
                                            <div class="handle mr-10">
                                                <holder-outlined />
                                            </div>
                                            <a-typography-paragraph
                                            class="mb-0 pr-20"

                                            :ellipsis="{ rows: 1, expandable: false, }"
                                            >
                                            {{ element.question_text ? 'Вопрос №' + (index + 1) +': ' + element.question_text : 'Вопрос №' + (index + 1) }}
                                        </a-typography-paragraph>
                                        </template>
                                        <a-form-item class="mb-15" label="Вопрос" name="question" :colon="false">
                                            <a-textarea v-model:value="element.question_text" />
                                        </a-form-item>
                                        <a-form-item class="mb-15" label="Изображение" name="q_image" :colon="false">
                                            <ImageUpload v-model="element.images" action='questions/media' :images="element.media" :maxCount="1"></ImageUpload>
                                        </a-form-item>
                                        <div class="ant-col ant-form-item-label"><label for="form_item_options" class="ant-form-item-no-colon" title="Варианты ответов">Варианты ответов</label></div>
                                        <!-- <hr> -->
                                        <!-- <a-form-item class="mb-10" label="Варианты ответов" name="options" :colon="false"> -->
                                        <a-row :gutter="16" class="mb-15">
                                            <a-col class="gutter-row" :span="6" v-for="(option,index) in element.options">
                                                <!-- <a-form-item class="mb-10" label="Вопрос" name="question" :colon="false"> -->
                                                <a-input class="mb-10" v-model:value="option.option_text" />
                                                <!-- </a-form-item> -->
                                                <div style="display: flex; justify-content: space-between;">
                                                    <a-checkbox v-model:checked="option.is_correct" >
                                                        Верный ответ
                                                    </a-checkbox>
                                                    <a-button size="small" type="link" danger @click="removeOption(element,index)" class="mb-10">
                                                        <DeleteOutlined />
                                                    </a-button>
                                                </div>
                                            </a-col>
                                        </a-row>
                                        <!-- </a-form-item> -->
                                        <a-button type="default" block @click="addOption(element)">
                                            Добавить ответ
                                        </a-button>
                                    </a-collapse-panel>
                                </a-collapse>
                            </template>
                        </draggable>
                        <a-button type="primary" block ghost class="mt-20 mb-10" @click="addQuestion">
                            Добавить вопрос
                        </a-button>
                    </a-tab-pane>
                </a-tabs>
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
import AuthUtil from '@/libs/auth/auth';
import ImageUpload from '@/components/Images/ImageUpload.vue';
import { DeleteOutlined, HolderOutlined } from '@ant-design/icons-vue';
import draggable from 'vuedraggable';

export default ({


    data() {
        return {
            text: null,
            fileList: null,
            data: {},
            activeKey: [],
            imageUrl: null,
            initialForm: {
                title: '',
                description: '',
                is_published: false,
                questions: [],
                images: [],
                media: []
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
        ImageUpload,
        DeleteOutlined,
        draggable,
        HolderOutlined,
    },
    computed: {
        getAuthToken() {
            return AuthUtil.getAuthToken()
        },
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
        },
        id() {
            this.initialId = this.id;
            this.resetForm();
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
        updatePositions() {
            console.log('change')
            // Loop through the questions and update their position based on their index in the array
            this.form.questions.forEach((question, index) => {
                question.position = index + 1
            })
        },
        getComponentData() {
            return {
                onChange: this.handleChange,
                onInput: this.inputChanged,
                wrap: true,
                value: this.activeNames
            };
        },
        addOption(question) {
            question.options.push({
                option_text: '',
                is_correct: false
            })
        },
        removeOption(question, optionIndex) {
            question = question.options.splice(optionIndex, 1)
        },
        addQuestion() {
            let question = {
                id: 'new_' + Math.random().toString(36).substr(2, 9),
                position: this.form.questions.length + 1,
                points: 1,
                question_text: '',
                images: [],
                media: [],
                options: [],
            };
            this.form.questions.push(question);
            this.activeKey = [...this.activeKey, question.id]
        },
        removeQuestion(index) {
            console.log(index)
            this.form.questions.splice(index, 1)
        },

        resetForm() {
            this.form = { ...this.initialForm, lesson_id: this.$route.params.lesson_id, course_id: this.$route.params.course_id };
        },
        loadData(id) {
            this.loading = true;
            this.$axios.get('/lessons/' + this.$route.params.lesson_id + '/tests/' + id)
                .then(response => {
                    this.form = response.data.data;
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
            let url = '/lessons/' + this.$route.params.lesson_id + "/tests";
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
                        // //description: error,
                    });
                });

        },
    }
})

</script>
