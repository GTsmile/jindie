import Vue from 'vue'
import VueRouter from 'vue-router'
import AdminBase from './components/Admin/Base.vue'

Vue.use(VueRouter)

export default new VueRouter({
    saveScrollPosition: true,
    routes: [
        {
            name: "index",
            path: '/login',
            component: resolve =>void(require(['./components/Login.vue'], resolve)),
            hidden: true
        },
        {
            path: '/',
            component: AdminBase,
            name: '人员管理',
            iconCls: 'el-icon-message',//图标样式class
            leaf: true,//只有一个节点
            children: [
                { path: '/user', component: resolve =>void(require(['./components/Admin/User.vue'], resolve)), name: '用户管理'},
            ]
        },
        {
            path: '/',
            component: AdminBase,
            name: '人员管理',
            iconCls: 'el-icon-message',//图标样式class
            leaf: true,//只有一个节点
            children: [
                { path: '/auth', component: resolve =>void(require(['./components/Admin/Auth.vue'], resolve)), name: '权限管理'},
            ]
        },
    ]
})
