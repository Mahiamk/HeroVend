<?php

use App\Models\BenefitSlide;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    try {
        $slides = BenefitSlide::orderBy('sort_order', 'asc')->get();
    } catch (QueryException $exception) {
        // A DB hiccup shouldn't take the whole landing page down; render it
        // with an empty benefits section instead of a 500 error.
        Log::error('Failed to load benefit slides for the homepage.', ['exception' => $exception]);
        $slides = collect();
    }

    // Benefit slides are only refreshed on a full page load, so make sure a
    // browser reload always hits the server instead of showing a stale
    // back/forward-cache snapshot of the page.
    return response()
        ->view('welcome', compact('slides'))
        ->header('Cache-Control', 'no-store, must-revalidate');
});

Route::get('/robots.txt', function () {
    return response("User-agent: *\nDisallow:\n\nSitemap: " . url('/sitemap.xml') . "\n")
        ->header('Content-Type', 'text/plain');
});

Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => url('/'), 'changefreq' => 'weekly', 'priority' => '1.0'],
    ];

    return response()
        ->view('sitemap', compact('urls'))
        ->header('Content-Type', 'application/xml');
});
