import { createRouter, createWebHistory } from 'vue-router';

import HomeView from '../views/HomeView.vue';

const router = createRouter({
    history: createWebHistory(),
    scrollBehavior() {
        return { top: 0 };
    },
    routes: [
        {
            path: '/',
            name: 'home',
            component: HomeView,
        },
        {
            path: '/profil-desa',
            name: 'profile',
            component: () => import('../views/ProfileView.vue'),
        },
        {
            path: '/berita',
            name: 'news',
            component: () => import('../views/NewsListView.vue'),
        },
        {
            path: '/berita/:slug',
            name: 'news-detail',
            component: () => import('../views/NewsDetailView.vue'),
            props: true,
        },
        {
            path: '/galeri',
            name: 'gallery',
            component: () => import('../views/GalleryView.vue'),
        },
        {
            path: '/galeri/:slug',
            name: 'gallery-detail',
            component: () => import('../views/GalleryDetailView.vue'),
            props: true,
        },
        {
            path: '/admin',
            component: () => import('../views/admin/AdminLayout.vue'),
            children: [
                {
                    path: '',
                    name: 'admin.dashboard',
                    component: () => import('../views/admin/AdminDashboard.vue'),
                },
                {
                    path: 'berita',
                    name: 'admin.news',
                    component: () => import('../views/admin/AdminNewsView.vue'),
                },
                {
                    path: 'berita/tambah',
                    name: 'admin.news.create',
                    component: () => import('../views/admin/AdminNewsEditorView.vue'),
                },
                {
                    path: 'berita/:id/edit',
                    name: 'admin.news.edit',
                    component: () => import('../views/admin/AdminNewsEditorView.vue'),
                    props: true,
                },
                {
                    path: 'layanan',
                    name: 'admin.services',
                    component: () => import('../views/admin/AdminServiceView.vue'),
                },
                {
                    path: 'layanan/tambah',
                    name: 'admin.services.create',
                    component: () => import('../views/admin/AdminServiceEditorView.vue'),
                },
                {
                    path: 'layanan/:id/edit',
                    name: 'admin.services.edit',
                    component: () => import('../views/admin/AdminServiceEditorView.vue'),
                    props: true,
                },
                {
                    path: 'statistik',
                    name: 'admin.statistics',
                    component: () => import('../views/admin/AdminStatisticView.vue'),
                },
                {
                    path: 'galeri',
                    name: 'admin.gallery',
                    component: () => import('../views/admin/AdminGalleryView.vue'),
                },
                {
                    path: 'pejabat',
                    name: 'admin.officials',
                    component: () => import('../views/admin/AdminOfficialView.vue'),
                },
                {
                    path: 'pengaturan',
                    name: 'admin.settings',
                    component: () => import('../views/admin/AdminSettingView.vue'),
                },
            ],
        },
        {
            path: '/:pathMatch(.*)*',
            name: 'not-found',
            component: () => import('../views/NotFoundView.vue'),
        },
    ],
});

export default router;

