<?php

use Illuminate\Support\Facades\Route;

// テストルート
Route::get('/test', function () {
    return response()->json(['status' => 'ok', 'message' => 'Server is running']);
});

// Vue.jsでルーティングを処理するため、すべてのルートでapp.blade.phpを返す
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
