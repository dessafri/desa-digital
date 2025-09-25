import { defineStore } from 'pinia';
import axios from 'axios';

export const useSiteStore = defineStore('site', {
    state: () => ({
        siteName: 'Desa Digital',
        theme: {
            primary: '#0EA5E9',
            secondary: '#10B981',
        },
        home: null,
        profile: null,
        contact: null,
        isLoading: false,
        menu: [
            { name: 'Beranda', to: '/' },
            { name: 'Profil Desa', to: '/profil-desa' },
            { name: 'Berita Desa', to: '/berita' },
            { name: 'Galeri Desa', to: '/galeri' },
        ],
    }),

    getters: {
        missionList(state) {
            return state.home?.mission?.items ?? [];
        },
        displayedServices(state) {
            return state.home?.services ?? [];
        },
        displayedStatistics(state) {
            return state.home?.statistics ?? [];
        },
        featuredAlbum(state) {
            return state.home?.featuredAlbum ?? null;
        },
    },

    actions: {
        async bootstrap() {
            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                await Promise.all([
                    this.fetchTheme(),
                    this.fetchHome(),
                    this.fetchContact(),
                ]);
            } finally {
                this.isLoading = false;
            }
        },

        async fetchTheme() {
            const { data } = await axios.get('/site/theme');
            this.theme = data;
            this.applyTheme();
        },

        async fetchHome() {
            const { data } = await axios.get('/site/home');
            this.home = data;
            if (data?.siteName) {
                this.siteName = data.siteName;
                document.title = `${data.siteName} | Desa Digital`; 
            }
        },

        async fetchProfile() {
            if (this.profile) {
                return;
            }

            const { data } = await axios.get('/site/profile');
            this.profile = data;
        },

        async fetchContact() {
            const { data } = await axios.get('/site/contact');
            this.contact = data;
        },

        applyTheme() {
            const root = document.documentElement;
            root.style.setProperty('--color-primary', this.theme.primary ?? '#0EA5E9');
            root.style.setProperty('--color-secondary', this.theme.secondary ?? '#10B981');
        },
    },
});
