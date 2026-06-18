<!doctype html>
<html lang="id">

<head>
    @include('layout.head', ['title' => 'Data Pengunjung'])
</head>

<body class="dashboard-shell text-slate-800">
    <div class="w-full min-h-screen">
        @include('layout.header')

        <main class="flex-1 px-3 py-3 sm:px-4 sm:py-4 lg:px-5 lg:py-5 xl:px-6">
            <!-- Summary Cards -->
            <section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <article class="metric-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover-card">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm text-slate-500">
                                Total Perpustakaan
                            </p>
                            <h3 id="stat-total" class="mt-2 text-2xl lg:text-3xl font-extrabold text-theme-dark">
                                -
                            </h3>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-theme-light text-xl text-theme-green shrink-0">
                            <i class="fa-solid fa-building-user"></i>
                        </div>
                    </div>
                </article>
                <article class="metric-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover-card">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm text-slate-500">
                                Rata-Rata Skor KPI
                            </p>
                            <h3 id="stat-rata" class="mt-2 text-3xl font-extrabold text-blue-600">
                                -
                            </h3>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-xl text-blue-600 shrink-0">
                            <i class="fa-solid fa-star"></i>
                        </div>
                    </div>
                </article>
                <article class="metric-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover-card">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm text-slate-500">
                                Skor Tertinggi
                            </p>
                            <h3 id="stat-tinggi" class="mt-2 text-3xl font-extrabold text-amber-500">
                                -
                            </h3>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-xl text-amber-500 shrink-0">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                    </div>
                </article>
            </section>

            <!-- Charts -->
            <section class="mb-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="mb-4 text-base font-bold text-theme-dark">
                        Top 10 Perpustakaan Terbaik
                    </h3>
                    <div class="h-64 sm:h-72 w-full relative">
                        <canvas id="chart-top"></canvas>
                    </div>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="mb-4 text-base font-bold text-theme-dark">
                        Distribusi Skor KPI
                    </h3>
                    <div class="h-64 sm:h-72 w-full relative flex items-center justify-center">
                        <canvas id="chart-distribusi"></canvas>
                    </div>
                </article>
            </section>

            <!-- Data Table -->
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-5 text-base font-bold text-theme-dark">
                    Rincian Skor KPI Perpustakaan
                </h3>
                <div class="overflow-hidden">
                    <table id="table-kpi" class="display responsive nowrap w-full" style="width: 100%">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="text-left text-xs font-semibold uppercase tracking-wider">
                                    No
                                </th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wider">
                                    Nama Perpustakaan
                                </th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wider">
                                    Desa/Kelurahan
                                </th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wider">
                                    Kabupaten/Kota
                                </th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wider">
                                    Skor
                                </th>
                                <th class="text-left text-xs font-semibold uppercase tracking-wider">
                                    Kategori
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                            <!-- Data akan diisi via JS -->
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fa-solid fa-spinner fa-spin text-theme-green text-xl"></i>
                                    <p class="mt-2">Memuat data...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
        @include('layout.footer')
    </div>

    @include('js.api')
    @include('js.kpi')
</body>

</html>
