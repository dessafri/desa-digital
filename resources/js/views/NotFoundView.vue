<template>
    <div class="min-h-screen bg-slate-50 flex items-center justify-center px-4">
        <div class="max-w-md text-center space-y-6">
            <span class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-primary text-2xl font-bold">404</span>
            <div class="space-y-3">
                <h1 class="text-3xl font-bold text-slate-900">Halaman Tidak Ditemukan</h1>
                <p class="text-sm text-slate-600">Maaf, kami tidak menemukan halaman yang Anda tuju. Silakan kembali ke beranda atau hubungi administrator desa.</p>
            </div>
            <div class="flex flex-col sm:flex-row sm:justify-center gap-3 text-sm font-semibold">
                <RouterLink to="/" class="inline-flex items-center justify-center rounded-full bg-primary px-6 py-3 text-white">
                    Kembali ke Beranda
                </RouterLink>
                <RouterLink to="/admin" class="inline-flex items-center justify-center rounded-full border border-primary px-6 py-3 text-primary">
                    Buka Panel Admin
                </RouterLink>
            </div>
        </div>
    </div>
</template>
