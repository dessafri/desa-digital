<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Desa Digital') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @php
        $hotFile = storage_path('framework/vite.hot');
        $hotUrl = $isHot = file_exists($hotFile) ? trim(file_get_contents($hotFile)) : null;
        $manifestPath = public_path('build/manifest.json');
    @endphp

    @if ($isHot)
        <script type="module" src="{{ $hotUrl }}/resources/js/app.js"></script>
        <link rel="stylesheet" href="{{ $hotUrl }}/resources/css/app.css">
    @elseif (file_exists($manifestPath))
        @php
            $manifest = json_decode(file_get_contents($manifestPath), true);
            $css = $manifest['resources/css/app.css']['file'] ?? null;
            $js = $manifest['resources/js/app.js']['file'] ?? null;
        @endphp
        @if ($css)
            <link rel="stylesheet" href="{{ asset('build/' . $css) }}">
        @endif
        @if ($js)
            <script type="module" src="{{ asset('build/' . $js) }}"></script>
        @endif
    @else
        <script type="module" src="http://127.0.0.1:5173/resources/js/app.js"></script>
        <link rel="stylesheet" href="http://127.0.0.1:5173/resources/css/app.css">
    @endif
</head>
<body class="antialiased">
    <div id="app"></div>
</body>
</html>
