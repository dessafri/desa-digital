<?php

namespace Database\Seeders;

use App\Models\GalleryAlbum;
use App\Models\GalleryItem;
use App\Models\NewsCategory;
use App\Models\NewsPost;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Statistic;
use App\Models\VillageOfficial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InitialContentSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $settings = [
            ['key' => 'site.name', 'value' => 'Desa Digital Sugihwaras', 'type' => 'text'],
            ['key' => 'site.tagline', 'value' => 'Mewujudkan layanan desa yang cepat, transparan, dan partisipatif.', 'type' => 'text'],
            ['key' => 'site.primary_color', 'value' => '#0EA5E9', 'type' => 'text'],
            ['key' => 'site.secondary_color', 'value' => '#10B981', 'type' => 'text'],
            ['key' => 'hero.title', 'value' => 'Desa Sugihwaras Digital', 'type' => 'text'],
            ['key' => 'hero.subtitle', 'value' => 'Portal resmi desa yang menghadirkan kemudahan akses informasi dan pelayanan publik.', 'type' => 'text'],
            ['key' => 'hero.cta_text', 'value' => 'Pelajari Profil Desa', 'type' => 'text'],
            ['key' => 'hero.cta_link', 'value' => '/profil-desa', 'type' => 'text'],
            ['key' => 'welcome.title', 'value' => 'Sambutan Kepala Desa', 'type' => 'text'],
            ['key' => 'welcome.subtitle', 'value' => 'Selamat datang di portal resmi Desa Sugihwaras.', 'type' => 'text'],
            ['key' => 'welcome.message', 'value' => 'Kami berkomitmen menghadirkan layanan publik yang responsif dan informasi terbaru bagi masyarakat. Semoga portal ini menjadi jembatan komunikasi yang efektif antara pemerintah desa dan warga.', 'type' => 'text'],
            ['key' => 'welcome.author', 'value' => 'H. Budi Santoso, S.E. - Kepala Desa Sugihwaras', 'type' => 'text'],
            ['key' => 'vision.title', 'value' => 'Visi', 'type' => 'text'],
            ['key' => 'vision.body', 'value' => 'Terwujudnya Desa Sugihwaras yang maju, mandiri, dan berdaya saing melalui transformasi digital.', 'type' => 'text'],
            ['key' => 'mission.title', 'value' => 'Misi', 'type' => 'text'],
            ['key' => 'mission.items', 'value' => json_encode([
                'Meningkatkan kualitas pelayanan publik berbasis teknologi informasi.',
                'Memperkuat partisipasi masyarakat dalam pembangunan desa.',
                'Mengembangkan potensi ekonomi desa melalui inovasi digital.',
                'Mendorong transparansi dan akuntabilitas pemerintah desa.',
            ]), 'type' => 'json'],
            ['key' => 'profile.about', 'value' => 'Desa Sugihwaras terletak di kecamatan fiktif dengan potensi pertanian dan UMKM yang terus berkembang. Melalui program desa digital, berbagai inovasi layanan dihadirkan untuk masyarakat.', 'type' => 'text'],
            ['key' => 'profile.history', 'value' => 'Sejak didirikan pada tahun 1975, Desa Sugihwaras terus bertransformasi mengikuti perkembangan zaman. Program digitalisasi desa dimulai pada tahun 2020 sebagai bagian dari inisiatif Smart Village.', 'type' => 'text'],
            ['key' => 'profile.geography', 'value' => 'Luas wilayah desa mencapai 450 Ha yang terdiri dari area persawahan, perkebunan, dan permukiman yang tersebar di 5 dusun.', 'type' => 'text'],
            ['key' => 'profile.map_embed', 'value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d...', 'type' => 'text'],
            ['key' => 'contact.address', 'value' => 'Jl. Merdeka No. 45, Sugihwaras, Jawa Timur', 'type' => 'text'],
            ['key' => 'contact.email', 'value' => 'admin@sugihwaras.desa.id', 'type' => 'text'],
            ['key' => 'contact.phone', 'value' => '+62 812 3456 7890', 'type' => 'text'],
            ['key' => 'social.facebook', 'value' => 'https://facebook.com/desasugihwaras', 'type' => 'text'],
            ['key' => 'social.instagram', 'value' => 'https://instagram.com/desasugihwaras', 'type' => 'text'],
        ];

        foreach ($settings as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                ['value' => $item['type'] === 'json' ? $item['value'] : $item['value'], 'type' => $item['type']]
            );
        }

        $statistics = [
            ['label' => 'Penduduk', 'value' => 3200, 'icon' => 'users', 'order' => 1],
            ['label' => 'Luas Wilayah (Ha)', 'value' => 450, 'icon' => 'map', 'order' => 2],
            ['label' => 'Dusun', 'value' => 5, 'icon' => 'home', 'order' => 3],
            ['label' => 'UMKM Aktif', 'value' => 48, 'icon' => 'store', 'order' => 4],
        ];

        Statistic::truncate();
        Statistic::insert(array_map(function ($stat) use ($now) {
            return array_merge($stat, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $statistics));

        $services = [
            ['title' => 'Layanan Administrasi', 'subtitle' => 'Pengajuan surat menyurat', 'description' => 'Permohonan surat pengantar, domisili, dan layanan administrasi lainnya secara daring.', 'icon' => 'DocumentTextIcon', 'link' => '/layanan/administrasi', 'order' => 1],
            ['title' => 'Layanan Kependudukan', 'subtitle' => 'Data penduduk terpadu', 'description' => 'Pantau dan update data kependudukan secara real-time melalui dashboard kependudukan.', 'icon' => 'UserGroupIcon', 'link' => '/layanan/kependudukan', 'order' => 2],
            ['title' => 'Informasi UMKM', 'subtitle' => 'Direktori pelaku usaha', 'description' => 'Jelajahi katalog UMKM desa lengkap dengan informasi produk dan kontak.', 'icon' => 'BuildingStorefrontIcon', 'link' => '/layanan/umkm', 'order' => 3],
            ['title' => 'Layanan Pengaduan', 'subtitle' => 'Sampaikan aspirasi', 'description' => 'Gunakan kanal pengaduan untuk menyampaikan masukan, kritik, dan saran demi kemajuan desa.', 'icon' => 'ChatBubbleOvalLeftEllipsisIcon', 'link' => '/layanan/pengaduan', 'order' => 4],
        ];

        Service::truncate();
        Service::insert(array_map(function ($service) use ($now) {
            return array_merge($service, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $services));

        $pelayanan = NewsCategory::firstOrCreate(
            ['slug' => 'pelayanan-desa'],
            ['name' => 'Pelayanan Desa', 'description' => 'Informasi layanan publik desa']
        );

        $program = NewsCategory::firstOrCreate(
            ['slug' => 'program-unggulan'],
            ['name' => 'Program Unggulan', 'description' => 'Program kerja prioritas Desa Sugihwaras']
        );

        NewsPost::truncate();

        $posts = [
            [
                'news_category_id' => $pelayanan->id,
                'title' => 'Integrasi Layanan Administrasi Desa Secara Daring',
                'slug' => Str::slug('Integrasi Layanan Administrasi Desa Secara Daring'),
                'excerpt' => ' Pemerintah Desa Sugihwaras meluncurkan layanan administrasi berbasis digital untuk mempermudah warga mengurus dokumen. ',
                'body' => '<p>Pemerintah Desa Sugihwaras resmi mengintegrasikan layanan administrasi desa melalui portal digital. Warga kini dapat mengajukan surat keterangan, pengantar, hingga permohonan layanan kependudukan secara daring.</p><p>Melalui sistem ini proses verifikasi menjadi lebih cepat dan transparan. Setiap pengajuan dapat dipantau statusnya secara real-time oleh pemohon.</p>',
                'cover_image' => 'https://source.unsplash.com/960x640/?village,community',
                'status' => 'published',
                'published_at' => $now->copy()->subDays(5),
                'meta' => ['author' => 'Admin Desa'],
            ],
            [
                'news_category_id' => $program->id,
                'title' => 'Program UMKM Go Digital Resmi Diluncurkan',
                'slug' => Str::slug('Program UMKM Go Digital Resmi Diluncurkan'),
                'excerpt' => 'Dungkap potensi UMKM desa melalui platform digital Desa Sugihwaras.',
                'body' => '<p>Program UMKM Go Digital memberikan pelatihan dan pendampingan kepada pelaku usaha desa untuk memanfaatkan platform digital.</p><p>Melalui program ini, pelaku UMKM mendapatkan akses ke katalog online, pemasaran digital, dan fasilitasi pembayaran nontunai.</p>',
                'cover_image' => 'https://source.unsplash.com/960x640/?small-business',
                'status' => 'published',
                'published_at' => $now->copy()->subDays(10),
                'meta' => ['author' => 'Admin Desa'],
            ],
        ];

        foreach ($posts as $post) {
            NewsPost::create($post);
        }

        $album = GalleryAlbum::firstOrCreate(
            ['slug' => 'kegiatan-desa'],
            ['title' => 'Kegiatan Desa', 'description' => 'Dokumentasi kegiatan Desa Sugihwaras', 'cover_image' => 'https://source.unsplash.com/960x640/?community,event']
        );

        GalleryItem::where('gallery_album_id', $album->id)->delete();

        $galleryItems = [
            ['gallery_album_id' => $album->id, 'title' => 'Pelatihan UMKM', 'caption' => 'Pelatihan digital marketing untuk pelaku UMKM desa.', 'image_url' => 'https://source.unsplash.com/600x400/?training', 'order' => 1],
            ['gallery_album_id' => $album->id, 'title' => 'Kerja Bakti', 'caption' => 'Warga bergotong royong membersihkan lingkungan.', 'image_url' => 'https://source.unsplash.com/600x400/?gotong-royong', 'order' => 2],
            ['gallery_album_id' => $album->id, 'title' => 'Panen Raya', 'caption' => 'Panen bersama kelompok tani Desa Sugihwaras.', 'image_url' => 'https://source.unsplash.com/600x400/?harvest', 'order' => 3],
            ['gallery_album_id' => $album->id, 'title' => 'Pelayanan Publik', 'caption' => 'Layanan administrasi terpadu di balai desa.', 'image_url' => 'https://source.unsplash.com/600x400/?public-service', 'order' => 4],
        ];

        GalleryItem::insert(array_map(function ($item) use ($now) {
            return array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $galleryItems));

        VillageOfficial::truncate();

        $officials = [
            ['name' => 'H. Budi Santoso, S.E.', 'position' => 'Kepala Desa', 'photo' => 'https://source.unsplash.com/320x320/?man,leader', 'phone' => '+62 812 3456 7890', 'email' => 'kepala@sugihwaras.desa.id', 'order' => 1],
            ['name' => 'Siti Rahmawati, S.Sos.', 'position' => 'Sekretaris Desa', 'photo' => 'https://source.unsplash.com/320x320/?woman,office', 'phone' => '+62 812 3456 7891', 'email' => 'sekdes@sugihwaras.desa.id', 'order' => 2],
            ['name' => 'Adi Pratama', 'position' => 'Kaur Pemerintahan', 'photo' => 'https://source.unsplash.com/320x320/?man,office', 'phone' => '+62 813 5555 6789', 'email' => 'pemerintahan@sugihwaras.desa.id', 'order' => 3],
        ];

        VillageOfficial::insert(array_map(function ($official) use ($now) {
            return array_merge($official, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $officials));
    }
}

