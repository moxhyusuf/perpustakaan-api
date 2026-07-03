<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function beranda(): View
    {
        return view('page.beranda');
    }

    public function pengunjung(): View
    {
        return view('page.pengunjung');
    }

    public function pelibatan(): View
    {
        return view('page.pelibatan');
    }

    public function publikasi(): View
    {
        return view('page.publikasi');
    }

    public function fasilitas(): View
    {
        return view('page.fasilitas');
    }

    public function replikasi(): View
    {
        return view('page.replikasi');
    }

    public function kpi(): View
    {
        return view('page.kpi');
    }

    public function perpustakaan(): View
    {
        return view('page.perpustakaan');
    }

    public function rekap(): View
    {
        return view('page.rekap');
    }
}
