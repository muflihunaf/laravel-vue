import { useAuthStore } from '../store/auth';
import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';
import AuthorDetail from '../pages/AuthorDetail.vue';
import CategoryDetail from '../pages/CategoryDetail.vue';

const routes = [
    {
        path: '/',
        name: 'landing',
        component: () => import('../pages/Landing.vue'),
        meta: { guest: true, title: 'Landing Page' },
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../pages/Login.vue'),
        meta: { guest: true, title: 'Login' },
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('../pages/Dashboard.vue'),
        meta: { requiresAuth: true, title: 'Dashboard' },
    },
    {
        path: '/users',
        name: 'users',
        component: () => import('../pages/Users.vue'),
        meta: { requiresAuth: true, role: 'Administrator', title: 'Users' },
    },
    {
        path: '/roles',
        name: 'roles',
        component: () => import('../pages/Roles.vue'),
        meta: { requiresAuth: true, title: 'Roles' },
    },
    {
        path: '/books',
        name: 'books',
        component: () => import('../pages/Books.vue'),
        meta: { requiresAuth: true, title: 'Books' },
    },
    {
        path: '/books/:uuid',
        name: 'book-detail',
        component: () => import('../pages/BookDetail.vue'),
        meta: { requiresAuth: true, title: 'Book Details' },
    },
    {
        path: '/authors',
        name: 'authors',
        component: () => import('../pages/Authors.vue'),
        meta: { requiresAuth: true, title: 'Authors' },
    },
    {
        path: '/categories',
        name: 'categories',
        component: () => import('../pages/Categories.vue'),
        meta: { requiresAuth: true, title: 'Categories' },
    },
    {
        path: '/authors/:uuid',
        name: 'author-detail',
        component: AuthorDetail,
        meta: { requiresAuth: true }
    },
    {
        path: '/categories/:uuid',
        name: 'category-detail',
        component: CategoryDetail,
        meta: { requiresAuth: true }
    },
    {
        path: '/history',
        name: 'export-import-history',
        component: () => import('../pages/ExportImportHistory.vue'),
        meta: { requiresAuth: true, title: 'Export/Import History' },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const auth = useAuthStore();
    try {
        const response = await axios.get('/api/check');
        auth.user = response.data;
        if (to.meta.guest) {
            next({ name: 'dashboard' });
        } else {
            next();
        }
    } catch (error) {
        if (to.meta.requiresAuth) {
            next({ name: 'login' });
        } else {
            next();
        }
    }
});

router.afterEach((to) => {
    document.title = to.meta.title || 'My App';
});

export default router; 