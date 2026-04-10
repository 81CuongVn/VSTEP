<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['status' => 'ok']);
});

Route::get('/internal/e2e-demo', function () {
    return view('internal.e2e-demo');
});

Route::get('/debug-cache', function () {
    return response()->json([
        'cache_default' => config('cache.default'),
        'session_driver' => config('session.driver'),
    ]);
});
