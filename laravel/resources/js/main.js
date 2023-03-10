/*
=========================================================
Muse - Vue Ant Design Dashboard - v1.0.0
=========================================================

Product Page: https://www.creative-tim.com/product/vue-ant-design-dashboard
Copyright 2021 Creative Tim (https://www.creative-tim.com)
Coded by Creative Tim

=========================================================
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software. 
*/

import Antd from 'ant-design-vue';

import './scss/app.scss';
import 'ant-design-vue/dist/antd.css';
// import 'ant-design-vue/dist/antd.dark.css';


import { createApp, h } from 'vue';
import App from './App.vue'

import DefaultLayout from './layouts/Default.vue'
import DashboardLayout from './layouts/Dashboard.vue'
import EmptyLayout from './layouts/Empty.vue'

// import DashboardRTLLayout from './layouts/DashboardRTL.vue'
import router from './router'
// import './plugins/click-away'
import axios from './libs/auth/axios';

import store from './store'



const app  = createApp({
    render: ()=>h(App)
});

app.config.globalProperties.$axios = axios;
app.use(router);
app.use(Antd);
app.use(store)



// Vue.config.productionTip = false

// Adding template layouts to the vue components.
app.component('layout-default', DefaultLayout);
app.component('layout-dashboard', DashboardLayout);
app.component('layout-empty', EmptyLayout);




app.mount('#app');

