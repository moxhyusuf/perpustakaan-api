<!doctype html>
<html lang="id">


<head>
    @include('layout.head', ['title' => 'Beranda'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>

<body class="dashboard-shell text-slate-800">
    <div class="w-full min-h-screen">
        @include('layout.header')

        <main class="px-3 py-3 sm:px-4 sm:py-4 lg:px-5 lg:py-5 xl:px-6">
            <section class="grid grid-cols-1 gap-4 xl:grid-cols-12 xl:gap-5 2xl:gap-6">
                <div class="space-y-4 xl:col-span-8 xl:space-y-5">
                    <section class="hero-panel hero-ornament relative overflow-hidden rounded-[24px] border border-emerald-50 min-h-[300px] sm:min-h-[340px] lg:min-h-[360px] shadow-soft">
                        <div class="absolute inset-0">
                            <img src="https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=1600&q=80" alt="" class="h-full w-full object-cover opacity-25 mix-blend-multiply" loading="lazy" />
                            <div class="absolute inset-0 bg-gradient-to-r from-white via-white/80 to-transparent"></div>
                        </div>

                        <div class="relative z-10 flex h-full max-w-2xl flex-col justify-center px-5 py-7 sm:px-7 sm:py-9 lg:px-8 lg:py-10">
                            <p class="mb-3 inline-flex w-fit items-center gap-2 rounded-full bg-white/80 px-3 py-2 text-[10px] sm:text-xs font-bold uppercase tracking-[0.18em] text-theme-green shadow-sm">
                                <i class="fa-solid fa-chart-line"></i>
                                Dashboard Monitoring
                            </p>
                            <h2 class="max-w-[10ch] text-[2rem] sm:text-[2.6rem] lg:text-[3.1rem] xl:text-[3.3rem] font-extrabold leading-[0.95] tracking-tight text-theme-dark">
                                Informasi Perpustakaan Kabupaten Probolinggo
                            </h2>
                            <p class="mt-4 max-w-xl text-sm sm:text-[15px] leading-6 sm:leading-7 text-slate-600">
                                Akses data kegiatan, layanan, publikasi, dan
                                capaian perpustakaan di Kabupaten
                                Probolinggo secara terbuka, ringkas, dan
                                mudah dipantau.
                            </p>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="#ringkasan" class="inline-flex items-center gap-2 rounded-full bg-theme-green px-5 py-3 text-sm font-semibold text-white transition hover:bg-theme-dark">
                                    Lihat Selengkapnya
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                                <a href="/rekap" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white/90 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-theme-green hover:text-theme-green">
                                    Buka Rekap
                                </a>
                            </div>
                        </div>
                    </section>

                    <section id="ringkasan" class="grid grid-cols-2 gap-3 lg:grid-cols-5 lg:gap-4">
                        <article class="metric-card rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover-card sm:p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm text-slate-500">
                                        Total Pengunjung
                                    </p>
                                    <h3 id="stat-pengunjung" class="mt-2 text-2xl sm:text-[30px] font-extrabold text-theme-dark">
                                        -
                                    </h3>
                                    <p class="mt-2 text-[10px] sm:text-xs text-slate-400">
                                        Periode data aktif
                                    </p>
                                </div>
                                <div class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-2xl bg-theme-light text-base sm:text-lg text-theme-green">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                            </div>
                        </article>

                        <article class="metric-card rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover-card sm:p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm text-slate-500">
                                        Total Kegiatan
                                    </p>
                                    <h3 id="stat-kegiatan" class="mt-2 text-2xl sm:text-[30px] font-extrabold text-theme-dark">
                                        -
                                    </h3>
                                    <p class="mt-2 text-[10px] sm:text-xs text-slate-400">
                                        Pelibatan masyarakat
                                    </p>
                                </div>
                                <div class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-2xl bg-theme-light text-base sm:text-lg text-theme-green">
                                    <i class="fa-regular fa-calendar-check"></i>
                                </div>
                            </div>
                        </article>

                        <article class="metric-card rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover-card sm:p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm text-slate-500">
                                        Publikasi
                                    </p>
                                    <h3 id="stat-publikasi" class="mt-2 text-2xl sm:text-[30px] font-extrabold text-theme-dark">
                                        -
                                    </h3>
                                    <p class="mt-2 text-[10px] sm:text-xs text-slate-400">
                                        Dokumen terdata
                                    </p>
                                </div>
                                <div class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-2xl bg-theme-light text-base sm:text-lg text-theme-green">
                                    <i class="fa-solid fa-book-open"></i>
                                </div>
                            </div>
                        </article>

                        <article class="metric-card rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover-card sm:p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm text-slate-500">
                                        Peningkatan
                                    </p>
                                    <h3 id="stat-advokasi" class="mt-2 text-2xl sm:text-[30px] font-extrabold text-theme-dark">
                                        -
                                    </h3>
                                    <p class="mt-2 text-[10px] sm:text-xs text-slate-400">
                                        Data peningkatan
                                    </p>
                                </div>
                                <div class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-2xl bg-theme-light text-base sm:text-lg text-theme-green">
                                    <i class="fa-solid fa-bullhorn"></i>
                                </div>
                            </div>
                        </article>

                        <article class="metric-card rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover-card sm:p-5 col-span-2 lg:col-span-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm text-slate-500">
                                        Replikasi Mandiri
                                    </p>
                                    <h3 id="stat-replikasi" class="mt-2 text-2xl sm:text-[30px] font-extrabold text-theme-dark">
                                        -
                                    </h3>
                                    <p class="mt-2 text-[10px] sm:text-xs text-slate-400">
                                        Total desa tercatat
                                    </p>
                                </div>
                                <div class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-2xl bg-theme-light text-base sm:text-lg text-theme-green">
                                    <i class="fa-solid fa-rotate"></i>
                                </div>
                            </div>
                        </article>
                    </section>

                    <section class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-4 xl:gap-5">
                        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <h3 class="text-base sm:text-lg font-bold text-theme-dark">
                                    Grafik Pengunjung
                                </h3>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] sm:text-xs font-semibold text-slate-500">Periode
                                    aktif</span>
                            </div>
                            <div class="dashboard-chart">
                                <canvas id="pengunjungChart"></canvas>
                            </div>
                        </article>
                        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                            <h3 class="mb-4 text-base sm:text-lg font-bold text-theme-dark">
                                Bidang Kegiatan Terbanyak
                            </h3>
                            <div id="bidang-bars-container" class="space-y-3.5"></div>
                            <div class="mt-5 rounded-2xl border border-emerald-100 bg-theme-light p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-theme-green shadow-sm">
                                        <i class="fa-solid fa-building"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                                            Perpustakaan Aktif
                                        </p>
                                        <p id="stat-perpus-aktif" class="mt-1 text-xl sm:text-2xl font-extrabold text-theme-dark">
                                            -
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </section>
                </div>

                <aside class="space-y-4 xl:col-span-4 xl:space-y-5">
                    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                        <div class="mb-4 flex items-center gap-2 text-base sm:text-lg font-bold text-theme-dark">
                            <i class="fa-regular fa-calendar-days text-theme-green"></i>
                            <h3>Filter Data</h3>
                        </div>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4">
                            <label class="block">
                                <span class="mb-2 block text-[10px] sm:text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Periode
                                    Tanggal</span>
                                <input id="filter-start" type="date" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-theme-green focus:ring-2 focus:ring-emerald-100" />
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-[10px] sm:text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Tanggal
                                    Akhir</span>
                                <input id="filter-end" type="date" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-theme-green focus:ring-2 focus:ring-emerald-100" />
                            </label>
                        </div>
                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <button id="apply-filter" class="rounded-xl bg-theme-green px-4 py-3 text-sm font-semibold text-white transition hover:bg-theme-dark">
                                Terapkan Filter
                            </button>
                            <button id="reset-filter" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-theme-green hover:text-theme-green">
                                Reset
                            </button>
                        </div>
                    </section>
                    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <h3 class="text-base sm:text-lg font-bold text-theme-dark">
                                Kegiatan Terbaru di Kabupaten Probolinggo
                            </h3>
                            <a href="/pelibatan" class="rounded-full bg-theme-light px-3 py-1.5 text-[11px] sm:text-xs font-semibold text-theme-green transition hover:bg-emerald-100">Lihat
                                Semua</a>
                        </div>
                        <div id="kegiatan-list-container" class="max-h-[700px] space-y-3 overflow-y-auto custom-scrollbar pr-1"></div>
                    </section>
                </aside>
            </section>

            <section class="mt-4 grid grid-cols-1 gap-4 xl:mt-5 xl:grid-cols-12 xl:gap-5 2xl:gap-6">
                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5 xl:col-span-4">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 class="text-base sm:text-lg font-bold text-theme-dark">
                            KPI Perpustakaan Kabupaten Probolinggo
                        </h3>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] sm:text-xs font-semibold text-slate-500">2026</span>
                    </div>
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50 text-[10px] sm:text-xs uppercase tracking-[0.12em] text-slate-500">
                                    <th class="px-3 py-3">No</th>
                                    <th class="px-3 py-3">Perpustakaan</th>
                                    <th class="px-3 py-3">Skor KPI</th>
                                </tr>
                            </thead>
                            <tbody id="kpi-table-body" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                    <div class="mt-4 border-t border-slate-100 pt-4">
                        <a href="kpi.html" class="inline-flex items-center gap-2 text-sm font-semibold text-theme-green hover:underline">
                            Lihat Selengkapnya
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5 xl:col-span-4">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 class="text-base sm:text-lg font-bold text-theme-dark">
                            Publikasi Terbaru
                        </h3>
                        <a href="/publikasi" class="text-sm font-semibold text-theme-green hover:underline">Lihat
                            Semua</a>
                    </div>
                    <div id="publikasi-container" class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4"></div>
                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5 xl:col-span-4">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 class="text-base sm:text-lg font-bold text-theme-dark">
                            Replikasi Mandiri Terbaru
                        </h3>
                        <a href="/replikasi" class="text-sm font-semibold text-theme-green hover:underline">Lihat
                            Semua</a>
                    </div>
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50 text-[10px] sm:text-xs uppercase tracking-[0.12em] text-slate-500">
                                    <th class="px-3 py-3">Tanggal</th>
                                    <th class="px-3 py-3">Ringkasan</th>
                                    <th class="px-3 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody id="replikasi-table-body" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                </article>
            </section>
            {{-- <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
                <h3 class="mb-4 text-base font-bold text-slate-800">Lokasi Perpustakaan</h3>
                <div id="map-perpustakaan" style="height: 400px; border-radius: 0.75rem;"></div>
            </div> --}}
        </main>

        @include('layout.footer')
    </div>

    @include('js.api')
    @include('js.beranda')
</body>

</html>
