<!doctype html>
<html lang="id">

<head>
    @include('layout.head', ['title' => 'Data Perpustakaan'])
    <style>
        .modal-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .modal-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .modal-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .modal-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .leaflet-pane,
        .leaflet-top,
        .leaflet-bottom,
        .leaflet-control {
            z-index: 10 !important;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>

<body class="dashboard-shell text-slate-800">
    <div class="w-full min-h-screen">
        @include('layout.header')

        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 xl:px-10">
            <!-- Summary Counter Cards -->
            <section class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4 mb-8">
                <div class="bg-white rounded-3xl border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-5">
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg sm:text-xl shrink-0">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">
                            Total Perpustakaan
                        </p>
                        <p class="text-xs text-slate-400 mt-2">
                            Data aktif dari API TPBIS
                        </p>
                        <h4 id="count-total" class="text-lg sm:text-xl font-bold text-slate-800 mt-0.5">
                            -
                        </h4>
                    </div>
                </div>
                <div class="bg-white rounded-3xl border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-5">
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg sm:text-xl shrink-0">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">
                            Tinggi
                        </p>
                        <p class="text-xs text-slate-400 mt-2">
                            Kategori KPI
                        </p>
                        <h4 id="count-sangat-baik" class="text-lg sm:text-xl font-bold text-emerald-600 mt-0.5">
                            -
                        </h4>
                    </div>
                </div>
                <div class="bg-white rounded-3xl border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-5">
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg sm:text-xl shrink-0">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">
                            Sedang
                        </p>
                        <p class="text-xs text-slate-400 mt-2">
                            Kategori KPI
                        </p>
                        <h4 id="count-cukup" class="text-lg sm:text-xl font-bold text-amber-600 mt-0.5">
                            -
                        </h4>
                    </div>
                </div>
                <div class="bg-white rounded-3xl border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-5">
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg sm:text-xl shrink-0">
                        <i class="fa-solid fa-circle-minus"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">
                            Rendah
                        </p>
                        <p class="text-xs text-slate-400 mt-2">
                            Kategori KPI
                        </p>
                        <h4 id="count-kurang" class="text-lg sm:text-xl font-bold text-rose-600 mt-0.5">
                            -
                        </h4>
                    </div>
                </div>
                <div class="bg-white rounded-3xl border border-slate-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-5">
                    <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center text-lg sm:text-xl shrink-0">
                        <i class="fa-solid fa-circle-question"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">
                            Belum Dinilai
                        </p>
                        <p class="text-xs text-slate-400 mt-2">
                            Kategori KPI
                        </p>
                        <h4 id="count-belum" class="text-lg sm:text-xl font-bold text-slate-600 mt-0.5">
                            -
                        </h4>
                    </div>
                </div>
            </section>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between px-6 pt-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">
                        Lokasi Perpustakaan
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Sebaran lokasi perpustakaan di Kabupaten Probolinggo.
                    </p>
                </div>
            </div>
            <div class="p-6 pt-4">

                <div class="map-wrapper">

                    <div id="map-perpustakaan" class="rounded-2xl overflow-hidden border border-slate-200" style="height:550px">
                    </div>

                    <!-- Legend -->
                    <div class="map-legend">

                        <div class="legend-title">
                            Keterangan KPI
                        </div>

                        <div class="legend-item">
                            <span class="legend-dot legend-green"></span>
                            <span>Tinggi</span>
                        </div>

                        <div class="legend-item">
                            <span class="legend-dot legend-orange"></span>
                            <span>Sedang</span>
                        </div>

                        <div class="legend-item">
                            <span class="legend-dot legend-red"></span>
                            <span>Rendah</span>
                        </div>

                        <div class="legend-item">
                            <span class="legend-dot legend-blue"></span>
                            <span>Belum Dinilai</span>
                        </div>

                    </div>

                </div>

            </div>

            <!-- Filters Section -->
            <section class="bg-white rounded-2xl border border-slate-200/60 p-5 shadow-sm mb-8">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <!-- Search Input -->
                    <div class="relative w-full md:max-w-md">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" id="search-input" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-theme-green/30 focus:border-theme-green transition" placeholder="Cari nama perpustakaan atau desa..." />
                    </div>

                    <!-- Filter Dropdowns -->
                    <div class="flex flex-wrap gap-3 w-full md:w-auto">
                        <!-- KPI Filter -->
                        <div class="w-full sm:w-48">
                            <select id="filter-kategori" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-theme-green/30 focus:border-theme-green transition">
                                <option value="">Semua Kategori KPI</option>
                                <option value="Tinggi">
                                    Kategori: Tinggi
                                </option>
                                <option value="Sedang">
                                    Kategori: Sedang
                                </option>
                                <option value="Rendah">
                                    Kategori: Rendah
                                </option>
                                <option value="Belum Dinilai">
                                    Kategori: Belum Dinilai
                                </option>
                            </select>
                        </div>

                        <!-- Kecamatan Filter (Dinamis dari JS) -->
                        <div class="w-full sm:w-48">
                            <select id="filter-kecamatan" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-theme-green/30 focus:border-theme-green transition">
                                <option value="">Semua Kecamatan</option>
                            </select>
                        </div>

                        <!-- Reset Filter Button -->
                        <button id="btn-reset-filter" class="w-full sm:w-auto px-4 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-arrow-rotate-left"></i>
                            Reset
                        </button>
                    </div>
                </div>
            </section>

            <!-- Cards Grid -->
            <section>
                <div id="loading-state" class="py-20 text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-theme-green border-t-transparent mb-4"></div>
                    <p class="text-slate-500 text-sm font-medium">
                        Memuat dan memetakan data perpustakaan...
                    </p>
                </div>

                <div id="empty-state" class="hidden py-16 text-center bg-white rounded-2xl border border-slate-200/60 p-8 shadow-sm">
                    <div class="mx-auto w-16 h-16 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center text-2xl mb-4">
                        <i class="fa-solid fa-building-circle-exclamation"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">
                        Perpustakaan Tidak Ditemukan
                    </h3>
                    <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                        Maaf, kami tidak dapat menemukan perpustakaan dengan
                        kriteria pencarian atau filter yang Anda pilih. Coba
                        sesuaikan filter Anda.
                    </p>
                    <button id="btn-empty-reset" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-theme-green text-white rounded-xl text-sm font-semibold hover:bg-theme-green/90 transition shadow-sm">
                        <i class="fa-solid fa-arrow-rotate-left text-xs"></i>
                        Reset Filter
                    </button>
                </div>

                <!-- Perpus Cards Container -->
                <div id="perpus-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Dibuat secara dinamis lewat JS -->
                </div>

                <!-- Pagination Section -->
                <div id="pagination-container" class="mt-10 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200 pt-6">
                    <p id="pagination-info" class="text-sm text-slate-500">
                        Menampilkan
                        <span class="font-semibold text-slate-800">1</span>
                        sampai
                        <span class="font-semibold text-slate-800">12</span>
                        dari
                        <span class="font-semibold text-slate-800">100</span>
                        perpustakaan
                    </p>
                    <div id="pagination-buttons" class="flex items-center gap-2">
                        <!-- Dibuat secara dinamis lewat JS -->
                    </div>
                </div>
            </section>
        </main>

        @include('layout.footer')
    </div>

    <div id="perpus-detail-modal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm hidden flex items-center justify-center p-0 md:p-4">
        <!-- Inner Container -->
        <div class="bg-slate-50 w-full h-full md:max-w-7xl md:h-[92vh] md:rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-[fadeIn_0.2s_ease-out]">
            <!-- Header Modal (Sticky) -->
            <header class="sticky top-0 bg-white border-b border-slate-200 px-4 py-4 sm:px-6 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <button onclick="closeDetailModal()" class="p-2 -ml-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition flex items-center justify-center">
                        <i class="fa-solid fa-arrow-left text-lg"></i>
                        <span class="hidden sm:inline ml-2 text-sm font-semibold">Kembali</span>
                    </button>
                    <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>
                    <div class="min-w-0">
                        <h3 id="modal-perpus-title" class="text-lg sm:text-xl font-bold text-slate-800 truncate">
                            Nama Perpustakaan
                        </h3>
                        <p id="modal-perpus-subtitle" class="text-xs text-slate-500 truncate mt-0.5">
                            Desa/Kelurahan, Kecamatan, Kab. Probolinggo
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div id="modal-perpus-badge" class="px-3 py-1 rounded-full text-xs font-bold shadow-sm">
                        Kategori
                    </div>
                    <button onclick="closeDetailModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-xl transition">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
            </header>

            <!-- Body Modal (Scrollable) -->
            <div class="flex-1 overflow-y-auto modal-scrollbar p-4 sm:p-6 lg:p-8 space-y-6">
                <!-- Top Section: Profil & KPI Overview -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Profile Card -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/60 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-theme-light text-xl text-theme-green mb-4">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                            <h4 class="text-base font-bold text-slate-800 mb-3">
                                Profil Perpustakaan
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between border-b border-slate-100 pb-2">
                                    <span class="text-slate-400">Provinsi</span>
                                    <span id="modal-prof-provinsi" class="font-medium text-slate-700">-</span>
                                </div>
                                <div class="flex justify-between border-b border-slate-100 pb-2">
                                    <span class="text-slate-400">Kabupaten/Kota</span>
                                    <span id="modal-prof-kabupaten" class="font-medium text-slate-700">-</span>
                                </div>
                                <div class="flex justify-between border-b border-slate-100 pb-2">
                                    <span class="text-slate-400">Desa/Kelurahan</span>
                                    <span id="modal-prof-desa" class="font-medium text-slate-700">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-building-circle-check text-theme-green"></i>
                            Mitra Aktif Program TPBIS Probolinggo
                        </div>
                    </div>

                    <!-- KPI Score Card -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/60 shadow-sm lg:col-span-2 flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div class="space-y-3 text-center sm:text-left">
                            <h4 class="text-base font-bold text-slate-800">
                                Evaluasi Kinerja (KPI)
                            </h4>
                            <p class="text-sm text-slate-500 max-w-md">
                                Penilaian Indikator Kinerja Utama (IKU)
                                perpustakaan dilakukan berdasarkan
                                kelengkapan pelaporan, jumlah pengunjung,
                                pelibatan masyarakat, dan perluasan jaringan
                            </p>
                            <div class="inline-flex items-center gap-2 text-xs text-slate-500">
                                <i class="fa-solid fa-circle-info text-blue-500"></i>
                                Penilaian Tahun Anggaran 2026
                            </div>
                        </div>
                        <div class="flex flex-col items-center justify-center bg-slate-50 border border-slate-200/50 p-6 rounded-2xl shrink-0 w-full sm:w-48">
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                                Skor IKU
                            </div>
                            <div id="modal-kpi-score" class="text-4xl font-extrabold text-theme-dark">
                                93.5
                            </div>
                            <div id="modal-kpi-category" class="mt-2 px-3 py-1 rounded-full text-xs font-bold bg-theme-light text-theme-green">
                                Tinggi
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Counters for selected Library -->
                <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-4 rounded-xl border border-slate-200/50 shadow-sm flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-base shrink-0">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                Total Kunjungan
                            </p>
                            <h4 id="modal-stat-pengunjung" class="text-base sm:text-lg font-bold text-slate-800">
                                0
                            </h4>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-200/50 shadow-sm flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-base shrink-0">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                Kegiatan Pelibatan
                            </p>
                            <h4 id="modal-stat-kegiatan" class="text-base sm:text-lg font-bold text-slate-800">
                                0
                            </h4>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-200/50 shadow-sm flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-base shrink-0">
                            <i class="fa-solid fa-newspaper"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                Publikasi Media
                            </p>
                            <h4 id="modal-stat-publikasi" class="text-base sm:text-lg font-bold text-slate-800">
                                0
                            </h4>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-200/50 shadow-sm flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-base shrink-0">
                            <i class="fa-solid fa-laptop-code"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                Komputer / Buku
                            </p>
                            <h4 id="modal-stat-komputer" class="text-base sm:text-lg font-bold text-slate-800">
                                0 / 0
                            </h4>
                        </div>
                    </div>
                </section>

                <!-- Tabs Section -->
                <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden flex flex-col min-h-[400px]">
                    <!-- Tab Headers -->
                    <div class="border-b border-slate-200 bg-slate-50/50 px-4 pt-2">
                        <nav id="modal-tabs-nav" class="flex gap-4 overflow-x-auto whitespace-nowrap custom-scrollbar">
                            <button onclick="switchModalTab('pengunjung')" id="tab-btn-pengunjung" class="border-b-2 border-theme-green pb-3 pt-2 text-theme-green font-bold text-sm flex items-center gap-2 shrink-0 transition">
                                <i class="fa-solid fa-users text-xs"></i>
                                Pengunjung
                            </button>
                            <button onclick="switchModalTab('pelibatan')" id="tab-btn-pelibatan" class="border-b-2 border-transparent pb-3 pt-2 text-slate-500 hover:text-slate-800 font-semibold text-sm flex items-center gap-2 shrink-0 transition">
                                <i class="fa-solid fa-calendar-check text-xs"></i>
                                Pelibatan Masyarakat
                            </button>
                            <button onclick="switchModalTab('publikasi')" id="tab-btn-publikasi" class="border-b-2 border-transparent pb-3 pt-2 text-slate-500 hover:text-slate-800 font-semibold text-sm flex items-center gap-2 shrink-0 transition">
                                <i class="fa-solid fa-newspaper text-xs"></i>
                                Publikasi Media
                            </button>
                            <button onclick="switchModalTab('fasilitas')" id="tab-btn-fasilitas" class="border-b-2 border-transparent pb-3 pt-2 text-slate-500 hover:text-slate-800 font-semibold text-sm flex items-center gap-2 shrink-0 transition">
                                <i class="fa-solid fa-laptop-code text-xs"></i>
                                Fasilitas
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Contents Container -->
                    <div class="p-4 sm:p-6 flex-1 flex flex-col justify-between">
                        <!-- Tab Pengunjung Content -->
                        <div id="tab-content-pengunjung" class="tab-pane space-y-6">
                            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                                <!-- Chart Area -->
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/40 lg:col-span-2 flex flex-col justify-between">
                                    <div>
                                        <h5 class="text-sm font-bold text-slate-800">
                                            Tren Pengunjung Bulanan
                                        </h5>
                                        <p class="text-xs text-slate-400 mt-0.5">
                                            Grafik perbandingan total
                                            pengunjung per periode
                                        </p>
                                    </div>
                                    <div class="h-48 w-full mt-4 flex items-center justify-center relative">
                                        <canvas id="modal-pengunjung-chart"></canvas>
                                    </div>
                                </div>
                                <!-- Table Area -->
                                <div class="lg:col-span-3 space-y-3">
                                    <h5 class="text-sm font-bold text-slate-800">
                                        Rincian Data Kunjungan
                                    </h5>
                                    <div class="overflow-x-auto border border-slate-100 rounded-xl">
                                        <table id="modal-table-pengunjung" class="w-full text-sm text-left text-slate-600">
                                            <thead class="bg-slate-50 text-xs text-slate-500 uppercase font-semibold">
                                                <tr>
                                                    <th class="px-4 py-3">
                                                        Periode
                                                    </th>
                                                    <th class="px-4 py-3 text-right">
                                                        Laki-Laki
                                                    </th>
                                                    <th class="px-4 py-3 text-right">
                                                        Perempuan
                                                    </th>
                                                    <th class="px-4 py-3 text-right">
                                                        Total Pengunjung
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Dinamis JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Pelibatan Content -->
                        <div id="tab-content-pelibatan" class="tab-pane hidden space-y-4">
                            <div class="flex items-center justify-between">
                                <h5 class="text-sm font-bold text-slate-800">
                                    Laporan Kegiatan Pelibatan Masyarakat
                                </h5>
                                <span id="modal-pelibatan-count-badge" class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-600">0 Kegiatan</span>
                            </div>
                            <div class="overflow-x-auto border border-slate-100 rounded-xl">
                                <table id="modal-table-pelibatan" class="w-full text-sm text-left text-slate-600">
                                    <thead class="bg-slate-50 text-xs text-slate-500 uppercase font-semibold">
                                        <tr>
                                            <th class="px-4 py-3">
                                                Tanggal
                                            </th>
                                            <th class="px-4 py-3">
                                                Nama Kegiatan
                                            </th>
                                            <th class="px-4 py-3">
                                                Bidang / Jenis
                                            </th>
                                            <th class="px-4 py-3">
                                                Sasaran
                                            </th>
                                            <th class="px-4 py-3 text-right">
                                                Peserta
                                            </th>
                                            <th class="px-4 py-3 text-center">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Dinamis JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab Publikasi Content -->
                        <div id="tab-content-publikasi" class="tab-pane hidden space-y-4">
                            <div class="flex items-center justify-between">
                                <h5 class="text-sm font-bold text-slate-800">
                                    Laporan Publikasi & Promosi Media
                                </h5>
                                <span id="modal-publikasi-count-badge" class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600">0 Publikasi</span>
                            </div>
                            <div class="overflow-x-auto border border-slate-100 rounded-xl">
                                <table id="modal-table-publikasi" class="w-full text-sm text-left text-slate-600">
                                    <thead class="bg-slate-50 text-xs text-slate-500 uppercase font-semibold">
                                        <tr>
                                            <th class="px-4 py-3">
                                                Tanggal
                                            </th>
                                            <th class="px-4 py-3">
                                                Judul Publikasi
                                            </th>
                                            <th class="px-4 py-3">
                                                Media / Jenis
                                            </th>
                                            <th class="px-4 py-3">
                                                Link Sumber
                                            </th>
                                            <th class="px-4 py-3 text-center">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Dinamis JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab Fasilitas Content -->
                        <div id="tab-content-fasilitas" class="tab-pane hidden space-y-4">
                            <div class="flex items-center justify-between">
                                <h5 class="text-sm font-bold text-slate-800">
                                    Peningkatan Fasilitas
                                </h5>
                                <span id="modal-fasilitas-count-badge" class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-600">0 Laporan</span>
                            </div>
                            <div class="overflow-x-auto border border-slate-100 rounded-xl">
                                <table id="modal-table-fasilitas" class="w-full text-sm text-left text-slate-600">
                                    <thead class="bg-slate-50 text-xs text-slate-500 uppercase font-semibold">
                                        <tr>
                                            <th class="px-4 py-3">
                                                Periode
                                            </th>
                                            <th class="px-4 py-3 text-right">
                                                Buku Fisik
                                            </th>
                                            <th class="px-4 py-3 text-right">
                                                Buku Digital
                                            </th>
                                            <th class="px-4 py-3 text-right">
                                                Komputer
                                            </th>
                                            <th class="px-4 py-3 text-right">
                                                Bandwidth
                                            </th>
                                            <th class="px-4 py-3 text-center">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Dinamis JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Modal -->
            <footer class="bg-slate-100 border-t border-slate-200 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                <p class="text-xs text-slate-500 flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-theme-green"></i>
                    Seluruh data berasal langsung dari sinkronisasi resmi
                    sistem monitoring TPBIS Perpusnas RI.
                </p>
                <button onclick="closeDetailModal()" class="w-full sm:w-auto px-4 py-2 bg-theme-green text-white hover:bg-theme-green/90 text-sm font-semibold rounded-xl shadow-sm transition">
                    Tutup Detail
                </button>
            </footer>
        </div>
    </div>

    @include('js.api-js')
    @include('js.perpustakaan-js')
</body>

</html>
