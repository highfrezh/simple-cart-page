import { createRouter, createWebHistory } from "vue-router";

import shopIndex from '../components/shop/index.vue'
import cart from '../components/shop/cart.vue'
import notFound from '../components/notFound.vue'

const routes = [
    {
        path:'/',
        component: shopIndex
    },
    {
        path:'/cart',
        component: cart
    },
    {
        path:'/:pathMatch(.*)*',
        component: notFound

    }
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router