<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GalleryAlbum;
use App\Models\NewsPost;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Statistic;
use App\Models\VillageOfficial;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class SiteContentController extends Controller
{
    public function home(): JsonResponse
    {
        $settings = $this->settings([
            'site.name',
            'hero.title',
            'hero.subtitle',
            'hero.cta_text',
            'hero.cta_link',
            'welcome.title',
            'welcome.subtitle',
            'welcome.message',
            'welcome.author',
            'vision.title',
            'vision.body',
            'mission.title',
            'mission.items',
        ]);

        $statistics = Statistic::orderBy('order')->get();
        $services = Service::orderBy('order')->get();

        $news = NewsPost::published()
            ->latest('published_at')
            ->take(3)
            ->get(['id', 'title', 'slug', 'excerpt', 'cover_image', 'published_at']);

        $album = GalleryAlbum::with(['items' => function ($query) {
            $query->orderBy('order')->limit(6);
        }])->latest('updated_at')->first();

        return response()->json([
            'siteName' => $settings->get('site.name'),
            'hero' => [
                'title' => $settings->get('hero.title'),
                'subtitle' => $settings->get('hero.subtitle'),
                'ctaText' => $settings->get('hero.cta_text'),
                'ctaLink' => $settings->get('hero.cta_link'),
            ],
            'welcome' => [
                'title' => $settings->get('welcome.title'),
                'subtitle' => $settings->get('welcome.subtitle'),
                'message' => $settings->get('welcome.message'),
                'author' => $settings->get('welcome.author'),
            ],
            'vision' => [
                'title' => $settings->get('vision.title'),
                'body' => $settings->get('vision.body'),
            ],
            'mission' => [
                'title' => $settings->get('mission.title'),
                'items' => $this->decodeJson($settings->get('mission.items')),
            ],
            'statistics' => $statistics,
            'services' => $services,
            'news' => $news,
            'featuredAlbum' => $album,
        ]);
    }

    public function profile(): JsonResponse
    {
        $settings = $this->settings([
            'profile.about',
            'profile.history',
            'profile.geography',
            'profile.map_embed',
            'vision.body',
            'mission.items',
        ]);

        $officials = VillageOfficial::orderBy('order')->get();

        return response()->json([
            'about' => $settings->get('profile.about'),
            'history' => $settings->get('profile.history'),
            'geography' => $settings->get('profile.geography'),
            'mapEmbed' => $settings->get('profile.map_embed'),
            'vision' => $settings->get('vision.body'),
            'mission' => $this->decodeJson($settings->get('mission.items')),
            'officials' => $officials,
        ]);
    }

    public function contact(): JsonResponse
    {
        $settings = $this->settings([
            'contact.address',
            'contact.email',
            'contact.phone',
            'social.facebook',
            'social.instagram',
        ]);

        return response()->json([
            'address' => $settings->get('contact.address'),
            'email' => $settings->get('contact.email'),
            'phone' => $settings->get('contact.phone'),
            'social' => [
                'facebook' => $settings->get('social.facebook'),
                'instagram' => $settings->get('social.instagram'),
            ],
        ]);
    }

    public function theme(): JsonResponse
    {
        $settings = $this->settings([
            'site.primary_color',
            'site.secondary_color',
        ]);

        return response()->json([
            'primary' => $settings->get('site.primary_color') ?? '#0EA5E9',
            'secondary' => $settings->get('site.secondary_color') ?? '#10B981',
        ]);
    }

    protected function settings(array $keys): Collection
    {
        return Setting::whereIn('key', $keys)
            ->get()
            ->mapWithKeys(function (Setting $setting) {
                return [$setting->key => $setting->value];
            });
    }

    protected function decodeJson($value)
    {
        if (empty($value)) {
            return [];
        }

        $decoded = json_decode($value, true);

        return $decoded ?? $value;
    }
}
