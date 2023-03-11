// import Vue from 'vue'
// import VueRouter from 'vue-router'
import { createWebHistory, createRouter } from "vue-router";

// Vue.use(VueRouter)

let routes = [{
        // will match everything
        path: '/:catchAll(.*)',
        component: () => import('../views/404.vue'),
    },
    {
        path: '/',
        name: 'Home',
        layout: "dashboard",

        redirect: '/dashboard',
        // component: () => import(/* webpackChunkName: "dashboard" */ '../views/Dashboard.vue'),

    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        layout: "dashboard",
        // route level code-splitting
        // this generates a separate chunk (about.[hash].js) for this route
        // which is lazy-loaded when the route is visited.
        component: () => import( /* webpackChunkName: "dashboard" */ '../views/Dashboard.vue'),
    },

    {
        path: '/academies',
        name: 'Academies-page',
        layout: "dashboard",
        component: () => import( /* webpackChunkName: "dashboard" */ '../views/Academies.vue'),
    },

    {
        path: '/academies/:academy_id',
        name: 'Faculties-page',
        layout: "dashboard",
        component: () => import( /* webpackChunkName: "dashboard" */ '../views/Faculties.vue'),
    },
    {
        path: '/courses/:course_id/lessons',
        name: 'Lessons-page',
        layout: "dashboard",
        component: () => import( /* webpackChunkName: "dashboard" */ '../views/Lessons.vue'),
    },
    {
        path: '/courses/:course_id/lessons/:lesson_id/tests',
        name: 'Tests-page',
        layout: "dashboard",
        component: () => import( /* webpackChunkName: "dashboard" */ '../views/Tests.vue'),
    },
    // {
    //     path: '/courses/:course_id/lessons/:lesson_id/tests/:test_id',
    //     name: 'Test-page',
    //     layout: "dashboard",
    //     component: () => import( /* webpackChunkName: "dashboard" */ '../views/Test.vue'),
    // },

    {
        path: '/users',
        name: 'Users-page',
        layout: "dashboard",
        component: () => import( /* webpackChunkName: "dashboard" */ '../views/Users.vue'),
    },

    {
        path: '/courses',
        name: 'Courses-page',
        layout: "dashboard",
        component: () => import( /* webpackChunkName: "dashboard" */ '../views/Courses.vue'),
    },

    // {
    //     path: '/courses/:course_id',
    //     name: 'Lessons-page',
    //     layout: "dashboard",
    //     component: () => import( /* webpackChunkName: "dashboard" */ '../views/Lessons.vue'),
    // },





    // {
    // 	path: '/layout',
    // 	name: 'Layout',
    // 	layout: "dashboard",
    // 	component: () => import('../views/Layout.vue'),
    // },
    // {
    // 	path: '/tables',
    // 	name: 'Tables',
    // 	layout: "dashboard",
    // 	component: () => import('../views/Tables.vue'),
    // },
    // {
    // 	path: '/billing',
    // 	name: 'Billing',
    // 	layout: "dashboard",
    // 	component: () => import('../views/Billing.vue'),
    // },
    // {
    // 	path: '/rtl',
    // 	name: 'RTL',
    // 	layout: "dashboard-rtl",
    // 	meta: {
    // 		layoutClass: 'dashboard-rtl',
    // 	},
    // 	component: () => import('../views/RTL.vue'),
    // },
    // {
    // 	path: '/Profile',
    // 	name: 'Profile',
    // 	layout: "dashboard",
    // 	meta: {
    // 		layoutClass: 'layout-profile',
    // 	},
    // 	component: () => import('../views/Profile.vue'),
    // },
    {
        path: '/sign-in',
        name: 'Sign-In',
        layout: 'empty',
        component: () => import('../views/Sign-In.vue'),
    },
    // {
    // 	path: '/sign-up',
    // 	name: 'Sign-Up',
    // 	meta: {
    // 		layoutClass: 'layout-sign-up',
    // 	},
    // 	component: () => import('../views/Sign-Up.vue'),
    // },
]

// Adding layout property from each route to the meta
// object so it can be accessed later.
function addLayoutToRoute(route, parentLayout = "default") {
    route.meta = route.meta || {};
    route.meta.layout = route.layout || parentLayout;

    if (route.children) {
        route.children = route.children.map((childRoute) => addLayoutToRoute(childRoute, route.meta.layout));
    }
    return route;
}

routes = routes.map((route) => addLayoutToRoute(route));


const router = createRouter({
    mode: 'hash',
    // base: process.env.BASE_URL,
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (to.hash) {
            return {
                selector: to.hash,
                behavior: 'smooth',
            }
        }
        return {
            x: 0,
            y: 0,
            behavior: 'smooth',
        }
    }
});

import AuthUtil from '@/libs/auth/auth';


router.beforeEach((to, from, next) => {
    const isAuthenticated = AuthUtil.checkAuth();
   
    if (!isAuthenticated && to.name !== 'Sign-In') {
        next({ name: 'Sign-In' });
    } else {
        if (isAuthenticated && to.name == 'Sign-In') {
            next({ name: 'Home' });
        } else {
            next();
        }
    }
});


// const router = new VueRouter({
// 	mode: 'hash',
// 	base: process.env.BASE_URL,
// 	routes,
// 	scrollBehavior (to, from, savedPosition) {
// 		if ( to.hash ) {
// 			return {
// 				selector: to.hash,
// 				behavior: 'smooth',
// 			}
// 		}
// 		return {
// 			x: 0,
// 			y: 0,
// 			behavior: 'smooth',
// 		}
// 	}
// })

export default router
