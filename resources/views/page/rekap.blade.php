<!doctype html>
<html lang="id">

<head>
    @include('layout.head', ['title' => 'Data Pengunjung'])
</head>

<body class="dashboard-shell text-slate-800">
    <div class="w-full min-h-screen">
        @include('layout.header')

        <main class="flex-1 px-3 py-3 sm:px-4 sm:py-4 lg:px-5 lg:py-5 xl:px-6">
            <div class="mb-5 flex flex-col sm:flex-row sm:items-end justify-between gap-4 no-print">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-theme-dark">
                        Rekapitulasi Data
                    </h2>
                    <p class="mt-1 text-sm sm:text-base text-slate-500">
                        Kombinasi performa seluruh perpustakaan desa
                    </p>
                </div>
                <div class="flex gap-2">
                    <button id="btn-export-pdf" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">
                        <i class="fa-solid fa-file-pdf"></i>
                        Export PDF
                    </button>
                </div>
            </div>

            <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm no-print">
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="w-full sm:w-1/3">
                        <label class="mb-1.5 block text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori KPI</label>
                        <select id="filter-kpi" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-medium text-slate-700 outline-none focus:border-theme-green focus:ring-1 focus:ring-theme-green">
                            <option value="all">Semua Kategori</option>
                            <option value="Sangat Baik">Baik (76-100)</option>
                            <option value="Cukup">Cukup (26-75)</option>
                            <option value="Kurang">Kurang (≤25)</option>
                        </select>
                    </div>
                    <div class="w-full sm:w-1/3">
                        <label class="mb-1.5 block text-xs font-semibold text-slate-500 uppercase tracking-wider">Aktivitas Pelibatan</label>
                        <select id="filter-kegiatan" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-medium text-slate-700 outline-none focus:border-theme-green focus:ring-1 focus:ring-theme-green">
                            <option value="all">Semua</option>
                            <option value="aktif">Aktif (Punya Kegiatan)</option>
                            <option value="pasif">Pasif (0 Kegiatan)</option>
                        </select>
                    </div>
                    <div class="w-full sm:w-1/3 flex items-end">
                        <button id="btn-reset-filter" class="w-full rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-200">
                            Reset Filter
                        </button>
                    </div>
                </div>
            </section>

            <div id="print-header" class="hidden mb-6 text-center">
                <h1 class="text-2xl font-bold uppercase text-slate-800">
                    Laporan Rekapitulasi Monitoring Perpustakaan
                </h1>
                <h2 class="text-lg font-semibold text-slate-700">
                    Transformasi Perpustakaan Berbasis Inklusi Sosial (TPBIS)
                </h2>
                <p class="text-slate-600">Kabupaten Probolinggo</p>
                <div class="border-b-2 border-slate-800 mt-4 mb-4"></div>
            </div>

            <div id="export-container" class="bg-white rounded-2xl border border-slate-200 p-0 sm:p-5 shadow-sm">
                <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4 px-4 pt-4 sm:px-0 sm:pt-0">
                    <div class="rounded-xl bg-theme-light/50 p-4 border border-theme-light">
                        <p class="text-xs font-medium text-slate-500">Total Perpustakaan</p>
                        <h4 id="sum-perpus" class="mt-1 text-xl font-bold text-theme-dark">-</h4>
                    </div>
                    <div class="rounded-xl bg-blue-50 p-4 border border-blue-100">
                        <p class="text-xs font-medium text-slate-500">Total Pengunjung</p>
                        <h4 id="sum-pengunjung" class="mt-1 text-xl font-bold text-blue-700">-</h4>
                    </div>
                    <div class="rounded-xl bg-emerald-50 p-4 border border-emerald-100">
                        <p class="text-xs font-medium text-slate-500">Total Kegiatan</p>
                        <h4 id="sum-kegiatan" class="mt-1 text-xl font-bold text-emerald-700">-</h4>
                    </div>
                    <div class="rounded-xl bg-amber-50 p-4 border border-amber-100">
                        <p class="text-xs font-medium text-slate-500">Total Publikasi</p>
                        <h4 id="sum-publikasi" class="mt-1 text-xl font-bold text-amber-700">-</h4>
                    </div>
                </div>

                <div class="overflow-x-auto px-4 pb-4 sm:px-0 sm:pb-0">
                    <h3 class="mb-4 text-base font-bold text-theme-dark print-title">
                        Rincian Data Perpustakaan
                    </h3>
                    <table id="table-rekap" class="display responsive nowrap w-full" style="width: 100%">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="text-left text-xs font-semibold uppercase tracking-wider">Perpustakaan</th>
                                <th class="text-center text-xs font-semibold uppercase tracking-wider">KPI</th>
                                <th class="text-center text-xs font-semibold uppercase tracking-wider">Kategori</th>
                                <th class="text-center text-xs font-semibold uppercase tracking-wider">
                                    <i class="fa-solid fa-users" title="Pengunjung"></i>
                                </th>
                                <th class="text-center text-xs font-semibold uppercase tracking-wider">
                                    <i class="fa-solid fa-calendar-check" title="Kegiatan"></i>
                                </th>
                                <th class="text-center text-xs font-semibold uppercase tracking-wider">
                                    <i class="fa-solid fa-bulletin" title="Publikasi"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fa-solid fa-spinner fa-spin text-theme-green text-xl"></i>
                                    <p class="mt-2">Menggabungkan data...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        @include('layout.footer')
    </div>

    @include('js.api')
    @include('js.rekap')
</body>

</html>
