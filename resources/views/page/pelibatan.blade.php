<!doctype html>
<html lang="id">

<head>
    @include('layout.head', ['title' => 'Data Pelibatan'])
</head>

<body class="dashboard-shell text-slate-800">
    <div class="w-full min-h-screen">
        @include('layout.header')

        <main class="px-3 py-3 sm:px-4 sm:py-4 lg:px-5 lg:py-5 xl:px-6">
            <!-- Summary Cards -->
            <section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <article class="metric-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover-card">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm text-slate-500">
                                Total Kegiatan
                            </p>
                            <h3 id="stat-kegiatan" class="mt-2 text-3xl font-extrabold text-theme-dark">
                                -
                            </h3>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-theme-light text-xl text-theme-green">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                    </div>
                </article>
                <article class="metric-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover-card">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm text-slate-500">
                                Total Peserta
                            </p>
                            <h3 id="stat-peserta" class="mt-2 text-3xl font-extrabold text-blue-600">
                                -
                            </h3>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-xl text-blue-600">
                            <i class="fa-solid fa-people-group"></i>
                        </div>
                    </div>
                </article>
                <article class="metric-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover-card">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm text-slate-500">
                                Rata-rata Peserta/Kegiatan
                            </p>
                            <h3 id="stat-rata" class="mt-2 text-3xl font-extrabold text-amber-500">
                                -
                            </h3>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-xl text-amber-500">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                    </div>
                </article>
            </section>

            <!-- Charts -->
            <section class="mb-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="mb-4 text-base font-bold text-theme-dark">
                        Bidang Kegiatan Terbanyak
                    </h3>
                    <div class="h-64 sm:h-72 w-full relative">
                        <canvas id="chart-bidang"></canvas>
                    </div>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="mb-4 text-base font-bold text-theme-dark">
                        Sebaran Kegiatan per Kecamatan
                    </h3>
                    <div class="h-64 sm:h-72 w-full relative flex items-center justify-center">
                        <canvas id="chart-kecamatan"></canvas>
                    </div>
                </article>
            </section>

            <!-- Table -->
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-base font-bold text-theme-dark">
                    Daftar Kegiatan Pelibatan Masyarakat
                </h3>
                <div class="overflow-x-auto custom-scrollbar">
                    <table id="tabel-pelibatan" class="w-full text-left text-sm text-slate-600 hover">
                        <thead class="bg-slate-50 text-slate-700">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold">
                                    Tanggal
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold">
                                    Nama Kegiatan
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold">
                                    foto Kegitan
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold">
                                    Perpustakaan
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold">
                                    Kecamatan
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold">
                                    Bidang
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-right">
                                    Peserta
                                </th>
                            </tr>
                        </thead>
                        <tbody id="table-body" class="divide-y divide-slate-100">
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                    <i class="fa-solid fa-circle-notch fa-spin mr-2"></i>
                                    Memuat data...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        @include('layout.footer')
    </div>

    <div id="gallery-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4">
        <div class="relative w-full max-w-3xl">
            <button id="gallery-close" type="button" class="absolute -top-12 right-0 text-white/80 hover:text-white text-2xl">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="mb-3 flex items-center justify-between text-white">
                <h4 id="gallery-title" class="text-sm font-semibold truncate pr-4"></h4>
                <span id="gallery-counter" class="text-xs text-white/70 whitespace-nowrap"></span>
            </div>

            <div class="relative flex items-center justify-center bg-black/40 rounded-xl overflow-hidden" style="min-height:300px;">
                <button id="gallery-prev" type="button" class="absolute left-2 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-black/50 text-white hover:bg-black/70">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <img id="gallery-image" src="" alt="Foto kegiatan" class="max-h-[70vh] w-auto object-contain" />

                <button id="gallery-next" type="button" class="absolute right-2 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-black/50 text-white hover:bg-black/70">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>

            <div id="gallery-thumbs" class="mt-3 flex gap-2 overflow-x-auto custom-scrollbar pb-1"></div>
        </div>
    </div>

    @include('js.api-js')
    @include('js.pelibatan-js')
</body>

</html>
