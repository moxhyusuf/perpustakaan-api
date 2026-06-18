<header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur">
    <div class="flex flex-wrap items-center justify-between gap-4 px-4 py-3 sm:px-5 lg:px-6 xl:px-8">
        <div class="flex min-w-0 items-center gap-3 lg:gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-3 flex-wrap">
                    <div class="flex h-20 w-20 sm:h-24 sm:w-24 items-center justify-center">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo TPBIS" class="h-18 w-18 sm:h-22 sm:w-22 object-contain">
                    </div>
                    <div class="hidden sm:block h-8 w-px bg-slate-200"></div>
                    <div class="min-w-0">
                        <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[0.18em] text-theme-green">
                            Kabupaten Probolinggo
                        </p>
                        <p class="text-[11px] sm:text-xs text-slate-500 leading-tight">
                            Transformasi Perpustakaan Berbasis Digital
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <nav class="hidden xl:flex items-center gap-5 2xl:gap-7 text-[13px] 2xl:text-sm font-semibold text-slate-600">
            <a href="/" class="{{ request()->is('/') ? 'border-b-2 border-theme-green pb-1 text-theme-green' : 'transition hover:text-theme-green' }}">Beranda</a>

            <a href="/perpustakaan" class="{{ request()->is('perpustakaan') ? 'border-b-2 border-theme-green pb-1 text-theme-green' : 'transition hover:text-theme-green' }}">Perpustakaan</a>

            <a href="/pengunjung" class="{{ request()->is('pengunjung') ? 'border-b-2 border-theme-green pb-1 text-theme-green' : 'transition hover:text-theme-green' }}">Pengunjung</a>

            <a href="/pelibatan" class="{{ request()->is('pelibatan') ? 'border-b-2 border-theme-green pb-1 text-theme-green' : 'transition hover:text-theme-green' }}">Pelibatan Masyarakat</a>

            <a href="/publikasi" class="{{ request()->is('publikasi') ? 'border-b-2 border-theme-green pb-1 text-theme-green' : 'transition hover:text-theme-green' }}">Publikasi</a>

            <a href="/peningkatan" class="{{ request()->is('peningkatan') ? 'border-b-2 border-theme-green pb-1 text-theme-green' : 'transition hover:text-theme-green' }}">Peningkatan</a>

            <a href="/replikasi" class="{{ request()->is('replikasi') ? 'border-b-2 border-theme-green pb-1 text-theme-green' : 'transition hover:text-theme-green' }}">Replikasi Mandiri</a>

            <a href="/kpi" class="{{ request()->is('kpi') ? 'border-b-2 border-theme-green pb-1 text-theme-green' : 'transition hover:text-theme-green' }}">KPI Perpustakaan</a>


            <a href="/rekap" class="{{ request()->is('rekap') ? 'border-b-2 border-theme-green pb-1 text-theme-green' : 'transition hover:text-theme-green' }}">Rekap</a>
        </nav>
    </div>

    <div class="xl:hidden border-t border-slate-100 bg-white">
        <nav class="flex gap-5 overflow-x-auto whitespace-nowrap px-4 py-3 text-[13px] font-semibold text-slate-600 custom-scrollbar sm:px-5 lg:px-6">
            <a href="/" class="{{ request()->is('/') ? 'text-theme-green' : '' }}">Beranda</a>
            <a href="/perpustakaan" class="{{ request()->is('perpustakaan') ? 'text-theme-green' : '' }}">Perpustakaan</a>
            <a href="/pengunjung" class="{{ request()->is('pengunjung') ? 'text-theme-green' : '' }}">Pengunjung</a>
            <a href="/pelibatan" class="{{ request()->is('pelibatan') ? 'text-theme-green' : '' }}">Pelibatan</a>
            <a href="/publikasi" class="{{ request()->is('publikasi') ? 'text-theme-green' : '' }}">Publikasi</a>
            <a href="/peningkatan" class="{{ request()->is('peningkatan') ? 'text-theme-green' : '' }}">Peningkatan</a>
            <a href="/replikasi" class="{{ request()->is('replikasi') ? 'text-theme-green' : '' }}">Replikasi</a>
            <a href="/kpi" class="{{ request()->is('kpi') ? 'text-theme-green' : '' }}">KPI</a>
            <a href="/rekap" class="{{ request()->is('rekap') ? 'text-theme-green' : '' }}">Rekap</a>
        </nav>
    </div>
</header>
