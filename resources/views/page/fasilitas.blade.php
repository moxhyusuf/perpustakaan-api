<!doctype html>
<html lang="id">

<head>
    @include('layout.head', ['title' => 'Fasilitas'])
</head>

<body class="dashboard-shell text-slate-800">
    <div class="w-full min-h-screen">
        @include('layout.header')

        <main class="px-3 py-3 sm:px-4 sm:py-4 lg:px-5 lg:py-5 xl:px-6">
            <!-- Summary Cards -->
            <section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-4">
                <article class="metric-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover-card">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm text-slate-500">
                                Laporan Masuk
                            </p>
                            <h3 id="stat-laporan" class="mt-2 text-3xl font-extrabold text-theme-dark">
                                -
                            </h3>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-theme-light text-xl text-theme-green">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                    </div>
                </article>
                <article class="metric-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover-card">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm text-slate-500">
                                Total Buku (Fisik & Digital)
                            </p>
                            <h3 id="stat-buku" class="mt-2 text-3xl font-extrabold text-gray-600">
                                -
                            </h3>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-50 text-xl text-gray-600">
                            <i class="fa-solid fa-book"></i>
                        </div>
                    </div>
                </article>
                <article class="metric-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover-card">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm text-slate-500">
                                Total Komputer
                            </p>
                            <h3 id="stat-komputer" class="mt-2 text-3xl font-extrabold text-blue-600">
                                -
                            </h3>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-xl text-blue-600">
                            <i class="fa-solid fa-desktop"></i>
                        </div>
                    </div>
                </article>
                <article class="metric-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover-card">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm text-slate-500">
                                Rata-rata Bandwidth (Mbps)
                            </p>
                            <h3 id="stat-bandwidth" class="mt-2 text-3xl font-extrabold text-purple-600">
                                -
                            </h3>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-50 text-xl text-purple-600">
                            <i class="fa-solid fa-wifi"></i>
                        </div>
                    </div>
                </article>
            </section>

            <!-- Charts -->
            <section class="mb-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="mb-4 text-base font-bold text-theme-dark">
                        Tren Laporan Fasilitas per Bulan
                    </h3>
                    <div class="h-64 sm:h-72 w-full relative">
                        <canvas id="chart-tren"></canvas>
                    </div>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="mb-4 text-base font-bold text-theme-dark">
                        Proporsi Koleksi (Fisik vs Digital)
                    </h3>
                    <div class="h-64 sm:h-72 w-full relative flex items-center justify-center">
                        <canvas id="chart-koleksi"></canvas>
                    </div>
                </article>
            </section>

            <!-- Table -->
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-base font-bold text-theme-dark">
                    Rincian Data Peningkatan Fasilitas
                </h3>
                <div class="overflow-x-auto custom-scrollbar">
                    <table id="tabel-peningkatan" class="w-full text-left text-sm text-slate-600 hover">
                        <thead class="bg-slate-50 text-slate-700">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold">
                                    Periode (Bulan/Tahun)
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold">
                                    Perpustakaan
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-right">
                                    Buku Fisik
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-right">
                                    Buku Digital
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-right">
                                    Komputer
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-right">
                                    Bandwidth
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-semibold text-center">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody id="table-body" class="divide-y divide-slate-100">
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-500">
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

    @include('js.api-js')
    @include('js.fasilitas-js')
</body>

</html>
