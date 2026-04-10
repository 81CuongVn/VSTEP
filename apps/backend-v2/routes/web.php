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

Route::get('/force-migrate', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true, '--database' => 'pgsql-migrate']);
        return response()->json(['output' => \Illuminate\Support\Facades\Artisan::output()]);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
    }
});
