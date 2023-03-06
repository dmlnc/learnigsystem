<!-- 
    This is the dashboard page, it uses the dashboard layout in: 
    "./layouts/Dashboard.vue" .
 -->
<template>
    <div>
        <UserForm :visible="formVisible" :id="formId" @close="hideForm" @save="loadData"/>
        <a-typography-title :level="5" class="mb-40">Пользователи</a-typography-title>

        <div class="mb-40">
             <a-button type="primary" @click="showForm(null)">Создать</a-button>
           
        </div>
        <a-table :dataSource="users" :columns="columns" :row-key="record => record.id">
            <template #customFilterDropdown="{ setSelectedKeys, selectedKeys, confirm, clearFilters, column }">
                <div>
                    <div style="padding: 8px">
                        <a-input ref="searchInput" placeholder="Поиск" :value="selectedKeys[0]" style="width: 188px; display: block" @change="e => setSelectedKeys(e.target.value ? [e.target.value] : [])" @pressEnter="handleSearch(selectedKeys, confirm, column.dataIndex)" />
                    </div>
                    <div class="ant-table-filter-dropdown-btns">
                        <!-- <a-space> -->
                        <a-button type="link" size="small" @click="handleReset(clearFilters)">
                            Reset
                        </a-button>
                        <a-button type="primary" size="small" @click="handleSearch(selectedKeys, confirm, column.dataIndex)">
                            OK
                        </a-button>
                        <!-- </a-space> -->
                    </div>
                </div>
            </template>
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'name'">
                    <a>
                        {{ record.name }}
                    </a>
                </template>
                <template v-else-if="column.key === 'roles'">
                    <span>
                        <a-tag v-for="role in record.roles" :key="role.id">
                            {{ role.title }}
                        </a-tag>
                    </span>
                </template>
                <template v-else-if="column.key === 'faculties'">
                    <span>
                        <a-tag v-for="faculty in record.faculties" :key="faculty.id">
                           Факультет {{ faculty.name }}
                        </a-tag>
                    </span>
                </template>
                <template v-else-if="column.key === 'actions'">
                    <a-space>
                        <a-button size="small" type="link" @click="showForm(record.id)"><svg viewBox="64 64 896 896" data-icon="edit" width="1em" height="1em" fill="currentColor" aria-hidden="true" focusable="false" class="">
                                <path d="M257.7 752c2 0 4-.2 6-.5L431.9 722c2-.4 3.9-1.3 5.3-2.8l423.9-423.9a9.96 9.96 0 0 0 0-14.1L694.9 114.9c-1.9-1.9-4.4-2.9-7.1-2.9s-5.2 1-7.1 2.9L256.8 538.8c-1.5 1.5-2.4 3.3-2.8 5.3l-29.5 168.2a33.5 33.5 0 0 0 9.4 29.8c6.6 6.4 14.9 9.9 23.8 9.9zm67.4-174.4L687.8 215l73.3 73.3-362.7 362.6-88.9 15.7 15.6-89zM880 836H144c-17.7 0-32 14.3-32 32v36c0 4.4 3.6 8 8 8h784c4.4 0 8-3.6 8-8v-36c0-17.7-14.3-32-32-32z"></path>
                            </svg></a-button>
                        <a-popconfirm :title="'Уверены, что хотите удалить '+record.name+ '?'" ok-text="Да" cancel-text="Нет" @confirm="deleteInstance(record.id)">
                            <a-button type="link" danger size="small">
                                <svg viewBox="64 64 896 896" data-icon="delete" width="1em" height="1em" fill="currentColor" aria-hidden="true" focusable="false" class="">
                                    <path d="M360 184h-8c4.4 0 8-3.6 8-8v8h304v-8c0 4.4 3.6 8 8 8h-8v72h72v-80c0-35.3-28.7-64-64-64H352c-35.3 0-64 28.7-64 64v80h72v-72zm504 72H160c-17.7 0-32 14.3-32 32v32c0 4.4 3.6 8 8 8h60.4l24.7 523c1.6 34.1 29.8 61 63.9 61h454c34.2 0 62.3-26.8 63.9-61l24.7-523H888c4.4 0 8-3.6 8-8v-32c0-17.7-14.3-32-32-32zM731.3 840H292.7l-24.2-512h487l-24.2 512z"></path>
                                </svg>
                            </a-button>
                        </a-popconfirm>
                    </a-space>
                </template>
            </template>
        </a-table>
    </div>
</template>
<script>
import UserForm from '@/components/Forms/UserForm.vue'

// Bar chart for "Active Users" card.
// import CardBarChart from '../components/Cards/CardBarChart.vue' ;

// Line chart for "Sales Overview" card.
// import CardLineChart from '../components/Cards/CardLineChart.vue' ;



import { notification } from 'ant-design-vue';

export default ({
    components: {
        UserForm
    },
    data() {
        return {
            searchText: null,
            users: [],
            formVisible: false,
            formId: null,
            loading: true,
            columns: [{
                    title: 'Имя',
                    dataIndex: 'name',
                    key: 'name',
                    sorter: (a, b) => a.name.length - b.name.length,
                    customFilterDropdown: true,
                    onFilter: (value, record) => record.name.toString().toLowerCase().includes(value.toLowerCase()),
                },
                {
                    title: 'Почта',
                    dataIndex: 'email',
                    key: 'email',
                    sorter: (a, b) => a.name.length - b.name.length,

                },
                {
                    title: 'Роль',
                    dataIndex: 'roles',
                    key: 'roles',
                    filters: [
                        { text: 'Админ', value: 'Admin' },
                        { text: 'Студент', value: 'Student' },
                    ],
                    filterMultiple: false,
                    onFilter: (value, record) => (record.roles.filter((role) => { return role.title == value })).length > 0
                },
                {
                    title: 'Факультеты',
                    dataIndex: 'faculties',
                    key: 'faculties',

                },
                {
                    title: 'Управление',
                    key: 'actions',
                },
            ],

        }
    },

    mounted() {
        this.loadData();
    },

    methods: {

        loadData() {
            this.loading = true;
            this.$axios.get('/users')
                .then(response => {
                    console.log(response)
                    this.users = response.data.data;
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

        deleteInstance(id) {
            this.loading = true;

            this.$axios.delete('/users/' + id)
                .then(response => {
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
                .then(() => {

                    this.loadData();
                    // this.loading = false;
                });
        },

        showForm(id) {
            this.formId = id
            this.formVisible = true
        },
        hideForm() {
            this.formVisible = false;
            this.formId = null
        },

        handleSearch(selectedKeys, confirm, dataIndex) {
            confirm();
            // this.searchText = selectedKeys[0];
        },
        handleReset(clearFilters) {

            clearFilters({
                confirm: true,
            });

            this.searchText = '';
        },


    }
})

</script>
<style lang="scss">
</style>
