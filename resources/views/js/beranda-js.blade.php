<script>
    let pengunjungChartInstance = null;
    let kecamatanChartInstance = null;

    let rawData = {
        pengunjung: null,
        pelibatan: null,
        publikasi: null,
        peningkatan: null,
        replikasi: null,
        kpi: null,
    };

    const MONTH_MAP = {
        jan: 0,
        januari: 0,
        feb: 1,
        februari: 1,
        mar: 2,
        maret: 2,
        apr: 3,
        april: 3,
        mei: 4,
        jun: 5,
        juni: 5,
        jul: 6,
        juli: 6,
        agu: 7,
        agustus: 7,
        sep: 8,
        september: 8,
        okt: 9,
        oktober: 9,
        nov: 10,
        november: 10,
        des: 11,
        desember: 11,
    };


    document.addEventListener("DOMContentLoaded", () => {
        initDashboard();
    });

    async function initDashboard() {
        try {
            const [
                pengunjungData,
                pelibatanData,
                publikasiData,
                peningkatanData,
                replikasiData,
                kpiData,
            ] = await Promise.all([
                ApiService.getPengunjung(),
                ApiService.getPelibatan(),
                ApiService.getPublikasi(),
                ApiService.getPeningkatan(),
                ApiService.getReplikasi(),
                ApiService.getKPI(),
            ]);

            rawData.pengunjung = pengunjungData;
            rawData.pelibatan = pelibatanData;
            rawData.publikasi = publikasiData;
            rawData.peningkatan = peningkatanData;
            rawData.replikasi = replikasiData;
            rawData.kpi = kpiData;

            renderDashboard(rawData);
            setupFilterHandlers();
        } catch (error) {
            console.error("Gagal menginisialisasi dashboard:", error);
        }
    }

    function renderDashboard(data) {
        updateSummaryCards(
            data.pengunjung,
            data.pelibatan,
            data.publikasi,
            data.peningkatan,
            data.replikasi,
        );
        renderPengunjungChart(data.pengunjung);
        renderBidangKegiatan(data.pelibatan);
        renderKegiatanTerbaru(data.pelibatan);
        renderKPI(data.kpi);
        renderPublikasi(data.publikasi);
        renderReplikasi(data.replikasi);

        if (data.kpi) {
            const uniqueLibs = new Set(
                data.kpi.map((item) => item.nama_perpustakaan).filter(Boolean),
            ).size;
            document.getElementById("stat-perpus-aktif").innerText =
                `${uniqueLibs} Perpustakaan`;
        }
    }

    function setupFilterHandlers() {
        const applyBtn = document.getElementById("apply-filter");
        const resetBtn = document.getElementById("reset-filter");

        if (!applyBtn || !resetBtn) return;

        applyBtn.addEventListener("click", () => {
            const startStr = document.getElementById("filter-start").value;
            const endStr = document.getElementById("filter-end").value;

            const start = startStr ? new Date(startStr).getTime() : 0;
            const end = endStr ?
                new Date(endStr + "T23:59:59.999").getTime() :
                Number.MAX_SAFE_INTEGER;

            const filteredData = filterDataByDate(rawData, start, end);
            renderDashboard(filteredData);
        });

        resetBtn.addEventListener("click", () => {
            document.getElementById("filter-start").value = "";
            document.getElementById("filter-end").value = "";
            renderDashboard(rawData);
        });
    }

    function filterDataByDate(data, start, end) {
        const isWithin = (dateValue) => {
            if (!dateValue) return false;
            const t = parseDate(dateValue);
            return t >= start && t <= end;
        };

        const isPeriodeWithin = (periodeStr) => {
            if (!periodeStr) return false;
            const t = parsePeriode(periodeStr);
            const date = new Date(t);
            const endOfMonth = new Date(
                date.getFullYear(),
                date.getMonth() + 1,
                0,
                23,
                59,
                59,
            ).getTime();
            return t <= end && endOfMonth >= start;
        };

        return {
            pengunjung: data.pengunjung ?
                data.pengunjung.filter((item) => isPeriodeWithin(item.periode)) : null,
            pelibatan: data.pelibatan ?
                data.pelibatan.filter((item) => isWithin(item.tanggal)) : null,
            publikasi: data.publikasi ?
                data.publikasi.filter((item) => isWithin(item.tanggal)) : null,
            peningkatan: data.peningkatan ?
                data.peningkatan.filter((item) => isWithin(item.tanggal_input)) : null,
            replikasi: data.replikasi ?
                data.replikasi.filter((item) => isWithin(item.tanggal_input)) : null,
            kpi: data.kpi,
        };
    }

    function updateSummaryCards(
        pengunjung,
        pelibatan,
        publikasi,
        peningkatan,
        replikasi,
    ) {
        const totalPengunjung = pengunjung ?
            pengunjung.reduce(
                (sum, item) => sum + Number(item.total_pengunjung || 0),
                0,
            ) :
            0;
        document.getElementById("stat-pengunjung").innerText =
            totalPengunjung.toLocaleString("id-ID");

        document.getElementById("stat-kegiatan").innerText = (
            pelibatan ? pelibatan.length : 0
        ).toLocaleString("id-ID");

        document.getElementById("stat-publikasi").innerText = (
            publikasi ? publikasi.length : 0
        ).toLocaleString("id-ID");

        const totalBuku = peningkatan ?
            peningkatan.reduce(
                (sum, item) =>
                sum +
                Number(item.jumlah_buku || 0) +
                Number(item.jumlah_buku_digital || 0),
                0,
            ) :
            0;
        document.getElementById("stat-buku").innerText =
            totalBuku.toLocaleString("id-ID");

        const totalReplikasi = replikasi ?
            replikasi.reduce((sum, item) => sum + Number(item.jumlah_desa || 0), 0) :
            0;
        document.getElementById("stat-replikasi").innerText =
            totalReplikasi.toLocaleString("id-ID");
    }

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = "#64748b";

    function renderPengunjungChart(data) {
        if (pengunjungChartInstance) {
            pengunjungChartInstance.destroy();
            pengunjungChartInstance = null;
        }

        if (!data || !data.length) return;

        const grouped = {};
        data.forEach((item) => {
            if (!grouped[item.periode]) grouped[item.periode] = 0;
            grouped[item.periode] += Number(item.total_pengunjung || 0);
        });

        const entries = Object.entries(grouped).sort(
            (a, b) => parsePeriode(a[0]) - parsePeriode(b[0]),
        );
        const labels = entries.map((item) => item[0]);
        const values = entries.map((item) => item[1]);

        const ctx = document.getElementById("pengunjungChart").getContext("2d");
        pengunjungChartInstance = new Chart(ctx, {
            type: "line",
            data: {
                labels,
                datasets: [{
                    label: "Pengunjung",
                    data: values,
                    borderColor: "#447153",
                    backgroundColor: "rgba(68, 113, 83, 0.10)",
                    borderWidth: 2,
                    pointBackgroundColor: "#447153",
                    pointBorderColor: "#ffffff",
                    pointBorderWidth: 2,
                    pointRadius: window.innerWidth < 640 ? 3 : 4,
                    fill: true,
                    tension: 0.36,
                }, ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "#eef2f7",
                            drawBorder: false,
                        },
                        ticks: {
                            font: {
                                size: window.innerWidth < 640 ? 10 : 11
                            },
                            callback(value) {
                                return value >= 1000 ? `${Math.round(value / 1000)}K` : value;
                            },
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: window.innerWidth < 640 ? 10 : 11
                            },
                        },
                    },
                },
            },
        });
    }

    function renderKecamatanChart(data) {
        if (!data) return;

        const total = data.length;
        document.getElementById("donut-total").innerText =
            total.toLocaleString("id-ID");

        const grouped = {};
        data.forEach((item) => {
            const kecamatan = (item.kecamatan_name || "Lainnya").trim() || "Lainnya";
            if (!grouped[kecamatan]) grouped[kecamatan] = 0;
            grouped[kecamatan] += 1;
        });

        const sorted = Object.entries(grouped).sort((a, b) => b[1] - a[1]);
        const topItems = sorted.slice(0, 5);
        const remaining = sorted.slice(5).reduce((sum, item) => sum + item[1], 0);
        if (remaining > 0) {
            topItems.push(["Lainnya", remaining]);
        }

        if (kecamatanChartInstance) {
            kecamatanChartInstance.destroy();
        }
    }

    function renderBidangKegiatan(data) {
        const container = document.getElementById("bidang-bars-container");
        if (!data || !data.length) {
            container.innerHTML =
                '<p class="text-sm text-slate-500">Tidak ada data untuk periode ini.</p>';
            return;
        }

        const total = data.length;
        const grouped = {};

        data.forEach((item) => {
            const bidang = (item.bidang_kegiatan || "Lainnya").trim() || "Lainnya";
            if (!grouped[bidang]) grouped[bidang] = 0;
            grouped[bidang] += 1;
        });

        const sorted = Object.entries(grouped)
            .sort((a, b) => b[1] - a[1])
            .slice(0, 5);

        container.innerHTML = sorted
            .map(([bidang, count]) => {
                const percent = Math.round((count / total) * 100);
                return `
            <div class="space-y-1.5">
                <div class="flex items-center justify-between gap-3 text-xs sm:text-sm">
                    <span class="max-w-[60%] truncate font-medium text-slate-700" title="${bidang}">${bidang}</span>
                    <span class="text-slate-500">${count} (${percent}%)</span>
                </div>
                <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full rounded-full bg-theme-green" style="width:${percent}%"></div>
                </div>
            </div>
        `;
            })
            .join("");
    }

    function renderKegiatanTerbaru(data) {
        const container = document.getElementById("kegiatan-list-container");
        if (!data || !data.length) {
            container.innerHTML =
                '<p class="text-sm text-slate-500">Tidak ada kegiatan terbaru.</p>';
            return;
        }

        const latest = [...data]
            .sort((a, b) => parseDate(b.tanggal) - parseDate(a.tanggal))
            .slice(0, 6);

        container.innerHTML = latest
            .map(
                (item) => `
        <article class="flex items-start gap-3 rounded-2xl border border-slate-100 p-3 transition hover:border-emerald-100 hover:bg-slate-50 sm:gap-4 sm:p-4">
            <div class="activity-placeholder">
                <i class="fa-solid fa-book-open-reader text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h4 class="truncate text-sm sm:text-[15px] font-bold leading-5 text-slate-800" title="${item.nama_kegiatan}">${item.nama_kegiatan}</h4>
                <p class="mt-1 truncate text-xs sm:text-sm text-slate-500">${item.perpus_nama}</p>
                <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-400">
                    <span><i class="fa-regular fa-calendar mr-1"></i>${formatDateID(item.tanggal)}</span>
                    <span><i class="fa-solid fa-location-dot mr-1"></i>${item.kecamatan_name || "-"}</span>
                </div>
            </div>
            <div class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] sm:text-xs font-semibold text-theme-green whitespace-nowrap">
                ${Number(item.jumlah_peserta || 0).toLocaleString("id-ID")} Peserta
            </div>
        </article>
    `,
            )
            .join("");
    }

    function renderKPI(data) {
        const tbody = document.getElementById("kpi-table-body");
        if (!data || !data.length) {
            tbody.innerHTML =
                '<tr><td colspan="3" class="px-3 py-4 text-center text-sm text-slate-500">Tidak ada data.</td></tr>';
            return;
        }

        const top5 = [...data]
            .sort((a, b) => normalizeScore(b.skor) - normalizeScore(a.skor))
            .slice(0, 5);

        tbody.innerHTML = top5
            .map((item, index) => {
                const score = normalizeScore(item.skor);
                const scoreClass =
                    score >= 90 ? "text-theme-green font-semibold" : "text-slate-700";
                return `
            <tr class="hover:bg-slate-50">
                <td class="px-3 py-3 text-xs sm:text-sm">${index + 1}</td>
                <td class="max-w-[220px] truncate px-3 py-3 text-xs sm:text-sm text-slate-700" title="${item.nama_perpustakaan}">${item.nama_perpustakaan}</td>
                <td class="px-3 py-3 text-xs sm:text-sm ${scoreClass}">${formatScore(item.skor)}</td>
            </tr>
        `;
            })
            .join("");
    }

    function renderPublikasi(data) {
        const container = document.getElementById("publikasi-container");
        if (!data || !data.length) {
            container.innerHTML =
                '<p class="text-sm text-slate-500 col-span-2">Tidak ada publikasi.</p>';
            return;
        }

        const latest = [...data]
            .sort((a, b) => parseDate(b.tanggal) - parseDate(a.tanggal))
            .slice(0, 4);

        container.innerHTML = latest
            .map(
                (item) => `
        <article class="hover-card rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
            <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-theme-green shadow-sm">
                <i class="fa-solid fa-newspaper"></i>
            </div>
            <h4 class="line-clamp-2 min-h-[42px] text-sm font-bold leading-5 text-slate-800" title="${item.judul}">${item.judul}</h4>
            <p class="mt-2 text-xs text-slate-500"><i class="fa-regular fa-clock mr-1"></i>${formatDateID(item.tanggal)}</p>
            <p class="mt-1 line-clamp-2 text-xs text-slate-500">${item.jenis_publikasi || "-"} · ${item.nama_media || "-"}</p>
        </article>
    `,
            )
            .join("");
    }

    function renderReplikasi(data) {
        const tbody = document.getElementById("replikasi-table-body");
        if (!data || !data.length) {
            tbody.innerHTML =
                '<tr><td colspan="3" class="px-3 py-4 text-center text-sm text-slate-500">Tidak ada data.</td></tr>';
            return;
        }

        const latest = [...data]
            .sort((a, b) => parseDate(b.tanggal_input) - parseDate(a.tanggal_input))
            .slice(0, 5);

        tbody.innerHTML = latest
            .map((item) => {
                const verified = String(item.is_verified || "")
                    .toLowerCase()
                    .includes("sudah");
                const badgeClass = verified ?
                    "bg-emerald-100 text-emerald-700" :
                    "bg-amber-100 text-amber-700";
                return `
            <tr class="hover:bg-slate-50">
                <td class="px-3 py-3 text-xs sm:text-sm text-slate-600">${formatDateID(item.tanggal_input)}</td>
                <td class="px-3 py-3 text-xs sm:text-sm">
                    <div class="font-semibold text-slate-700">${Number(item.jumlah_desa || 0).toLocaleString("id-ID")} Desa</div>
                    <div class="mt-1 text-[11px] text-slate-500">Dana: ${item.sumber_dana || "-"}</div>
                </td>
                <td class="px-3 py-3 text-xs sm:text-sm">
                    <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] sm:text-xs font-semibold ${badgeClass}">${item.is_verified || "-"}</span>
                </td>
            </tr>
        `;
            })
            .join("");
    }

    function parseDate(value) {
        const date = new Date(value);
        return Number.isNaN(date.getTime()) ? 0 : date.getTime();
    }

    function parsePeriode(value) {
        if (!value) return 0;
        const parts = String(value).trim().split(/\s+/);
        const monthName = (parts[0] || "").toLowerCase();
        const year = Number(parts[1] || 0);
        const month = MONTH_MAP[monthName] ?? 0;
        return new Date(year || 0, month, 1).getTime();
    }

    function formatDateID(value) {
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return value || "-";
        return new Intl.DateTimeFormat("id-ID", {
            day: "2-digit",
            month: "short",
            year: "numeric",
        }).format(date);
    }

    function normalizeScore(value) {
        const normalized = Number(String(value).replace(",", "."));
        return Number.isNaN(normalized) ? 0 : normalized;
    }

    function formatScore(value) {
        const score = normalizeScore(value);
        return score ?
            score.toLocaleString("id-ID", {
                minimumFractionDigits: 1,
                maximumFractionDigits: 2,
            }) :
            "-";
    }
</script>
