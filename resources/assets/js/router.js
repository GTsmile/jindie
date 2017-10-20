import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

export default new VueRouter({
    saveScrollPosition: true,
    routes: [
    	{
            name: "root",
            path: '/admin',
            component: resolve =>void(require(['./components/Index.vue'], resolve))
        },
        {
            name: "index",
            path: '/login',
            component: resolve =>void(require(['./components/Login.vue'], resolve))
        },
    ]
})
