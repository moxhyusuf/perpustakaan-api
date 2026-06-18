<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::controller(PageController::class)
    ->group(function () {
        Route::get('/', 'beranda');
        Route::get('/pengunjung', 'pengunjung');
        Route::get('/pelibatan', 'pelibatan');
        Route::get('/publikasi', 'publikasi');
        Route::get('/peningkatan', 'peningkatan');
        Route::get('/replikasi', 'replikasi');
        Route::get('/kpi', 'kpi');
        Route::get('/perpustakaan', 'perpustakaan');
        Route::get('/rekap', 'rekap');
    });
