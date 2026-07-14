<header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur">
    <div class="flex flex-wrap items-center justify-between gap-2 px-4 py-2 sm:px-5 lg:px-6 xl:px-8">
        <div class="flex min-w-0 items-center gap-2 lg:gap-3">
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <div class="flex h-14 w-14 sm:h-16 sm:w-16 items-center justify-center shrink-0">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo TPBIS" class="h-12 w-12 sm:h-14 sm:w-14 object-contain">
                    </div>
                    <div class="hidden sm:block h-7 w-px bg-slate-200"></div>
                    <div class="min-w-0">
                        <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[0.14em] text-theme-green leading-tight">
                            Kabupaten Probolinggo
                        </p>
                        <p class="text-[11px] sm:text-xs text-slate-500 leading-tight">
                            Transformasi Perpustakaan Berbasis Inklusi Sosial
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <nav class="hidden xl:flex items-center gap-3 2xl:gap-4 text-[13px] 2xl:text-sm font-semibold text-slate-600">
            <a href="/" class="{{ request()->is('/') ? 'border-b-2 border-theme-green pb-0.5 text-theme-green' : 'transition hover:text-theme-green' }}">Beranda</a>

            <a href="/perpustakaan" class="{{ request()->is('perpustakaan') ? 'border-b-2 border-theme-green pb-0.5 text-theme-green' : 'transition hover:text-theme-green' }}">Perpustakaan</a>

            <a href="/pengunjung" class="{{ request()->is('pengunjung') ? 'border-b-2 border-theme-green pb-0.5 text-theme-green' : 'transition hover:text-theme-green' }}">Pengunjung</a>

            <a href="/pelibatan" class="{{ request()->is('pelibatan') ? 'border-b-2 border-theme-green pb-0.5 text-theme-green' : 'transition hover:text-theme-green' }}">Pelibatan Masyarakat</a>

            <a href="/publikasi" class="{{ request()->is('publikasi') ? 'border-b-2 border-theme-green pb-0.5 text-theme-green' : 'transition hover:text-theme-green' }}">Publikasi</a>

            <a href="/advokasi" class="{{ request()->is('advokasi') ? 'border-b-2 border-theme-green pb-0.5 text-theme-green' : 'transition hover:text-theme-green' }}">Advokasi</a>

            <a href="/fasilitas" class="{{ request()->is('fasilitas') ? 'border-b-2 border-theme-green pb-0.5 text-theme-green' : 'transition hover:text-theme-green' }}">Fasilitas</a>

            <a href="/replikasi" class="{{ request()->is('replikasi') ? 'border-b-2 border-theme-green pb-0.5 text-theme-green' : 'transition hover:text-theme-green' }}">Replikasi Mandiri</a>

            <a href="/kpi" class="{{ request()->is('kpi') ? 'border-b-2 border-theme-green pb-0.5 text-theme-green' : 'transition hover:text-theme-green' }}">KPI Perpustakaan</a>

            <a href="/rekap" class="{{ request()->is('rekap') ? 'border-b-2 border-theme-green pb-0.5 text-theme-green' : 'transition hover:text-theme-green' }}">Rekap</a>
        </nav>
    </div>

    <div class="xl:hidden border-t border-slate-100 bg-white">
        <nav class="flex gap-4 overflow-x-auto whitespace-nowrap px-4 py-2 text-[13px] font-semibold text-slate-600 custom-scrollbar sm:px-5 lg:px-6">
            <a href="/" class="{{ request()->is('/') ? 'text-theme-green' : '' }}">Beranda</a>
            <a href="/perpustakaan" class="{{ request()->is('perpustakaan') ? 'text-theme-green' : '' }}">Perpustakaan</a>
            <a href="/pengunjung" class="{{ request()->is('pengunjung') ? 'text-theme-green' : '' }}">Pengunjung</a>
            <a href="/pelibatan" class="{{ request()->is('pelibatan') ? 'text-theme-green' : '' }}">Pelibatan</a>
            <a href="/publikasi" class="{{ request()->is('publikasi') ? 'text-theme-green' : '' }}">Publikasi</a>
            <a href="/advokasi" class="{{ request()->is('advokasi') ? 'text-theme-green' : '' }}">Advokasi</a>
            <a href="/fasilitas" class="{{ request()->is('fasilitas') ? 'text-theme-green' : '' }}">Fasilitas</a>
            <a href="/replikasi" class="{{ request()->is('replikasi') ? 'text-theme-green' : '' }}">Replikasi</a>
            <a href="/kpi" class="{{ request()->is('kpi') ? 'text-theme-green' : '' }}">KPI</a>
            <a href="/rekap" class="{{ request()->is('rekap') ? 'text-theme-green' : '' }}">Rekap</a>
        </nav>
    </div>
</header>
