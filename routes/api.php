<?php

use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\GalleryItemController;
use App\Http\Controllers\Api\NewsCategoryController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\SiteContentController;
use App\Http\Controllers\Api\StatisticController;
use App\Http\Controllers\Api\VillageOfficialController;
use Illuminate\Support\Facades\Route;

Route::get('/site/home', [SiteContentController::class, 'home']);
Route::get('/site/profile', [SiteContentController::class, 'profile']);
Route::get('/site/contact', [SiteContentController::class, 'contact']);
Route::get('/site/theme', [SiteContentController::class, 'theme']);

Route::get('/news/slug/{slug}', [NewsController::class, 'showBySlug']);
Route::apiResource('news', NewsController::class);
Route::apiResource('news-categories', NewsCategoryController::class);

Route::apiResource('services', ServiceController::class);
Route::apiResource('statistics', StatisticController::class);
Route::apiResource('village-officials', VillageOfficialController::class);
Route::post('/village-officials/upload-photo', [VillageOfficialController::class, 'uploadPhoto']);

Route::get('/gallery-albums/slug/{slug}', [GalleryController::class, 'showBySlug']);

Route::apiResource('gallery-albums', GalleryController::class)->parameters([
    'gallery-albums' => 'galleryAlbum',
]);
Route::apiResource('gallery-items', GalleryItemController::class)->parameters([
    'gallery-items' => 'galleryItem',
]);
Route::post('/gallery-items/upload', [GalleryItemController::class, 'upload']);

Route::apiResource('settings', SettingController::class);
Route::post('/settings/upload-hero', [SettingController::class, 'uploadHero']);
Route::post('/settings/upload-image', [SettingController::class, 'uploadImage']);
