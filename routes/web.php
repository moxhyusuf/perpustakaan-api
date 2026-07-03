<?php
// routes/web.php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::middleware('sync.perpusnas')
    ->controller(PageController::class)
    ->group(function () {
        Route::get('/', 'beranda');
        Route::get('/pengunjung', 'pengunjung');
        Route::get('/pelibatan', 'pelibatan');
        Route::get('/publikasi', 'publikasi');
        Route::get('/fasilitas', 'fasilitas');
        Route::get('/replikasi', 'replikasi');
        Route::get('/kpi', 'kpi');
        Route::get('/perpustakaan', 'perpustakaan');
        Route::get('/rekap', 'rekap');
    });
