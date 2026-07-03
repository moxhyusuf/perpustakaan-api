<script>
    document.addEventListener("DOMContentLoaded", () => {
        initFasilitas();
    });

    let chartTrenInstance = null;
    let chartKoleksiInstance = null;
    let dataTableInstance = null;

    async function initFasilitas() {
        try {
            const data = await window.ApiService.getFasilitas();

            if (!data || data.length === 0) {
                renderEmptyState();
                return;
            }

            renderSummary(data);
            renderCharts(data);
            renderTable(data);
        } catch (error) {
            console.error("Gagal memuat data peningkatan fasilitas:", error);
            document.getElementById("table-body").innerHTML = `
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-red-500">
                    <i class="fa-solid fa-triangle-exclamation mr-2"></i> Gagal memuat data.
                </td>
            </tr>
        `;
        }
    }

    function renderEmptyState() {
        document.getElementById("stat-laporan").textContent = "0";
        document.getElementById("stat-komputer").textContent = "0";
        document.getElementById("stat-buku").textContent = "0";
        document.getElementById("stat-bandwidth").textContent = "0";

        document.getElementById("table-body").innerHTML = `
        <tr>
            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                Data tidak ditemukan.
            </td>
        </tr>
    `;
    }

    function renderSummary(data) {
        const totalLaporan = data.length;
        let totalKomputer = 0;
        let totalBuku = 0;
        let totalBandwidth = 0;
        let bandwidthCount = 0;

        data.forEach((item) => {
            totalKomputer += item.jumlah_komputer || 0;
            totalBuku += (item.jumlah_buku || 0) + (item.jumlah_buku_digital || 0);
            if (item.bandwidth != null && item.bandwidth > 0) {
                totalBandwidth += item.bandwidth;
                bandwidthCount++;
            }
        });

        const rataBandwidth = bandwidthCount > 0 ? Math.round(totalBandwidth / bandwidthCount) : 0;

        document.getElementById("stat-laporan").textContent = totalLaporan.toLocaleString("id-ID");
        document.getElementById("stat-komputer").textContent = totalKomputer.toLocaleString("id-ID");
        document.getElementById("stat-buku").textContent = totalBuku.toLocaleString("id-ID");
        document.getElementById("stat-bandwidth").textContent = rataBandwidth.toLocaleString("id-ID");
    }

    function renderCharts(data) {
        // 1. Chart Tren Laporan per Bulan (Line Chart)
        const bulanMap = {};
        data.forEach((item) => {
            const bulan = item.bulan || 'Tidak Diketahui';
            const tahun = item.tahun || '';
            const label = `${bulan} ${tahun}`.trim();

            if (!bulanMap[label]) {
                bulanMap[label] = 0;
            }
            bulanMap[label]++;
        });

        const labelsTren = Object.keys(bulanMap);
        const dataTren = Object.values(bulanMap);

        const ctxTren = document.getElementById("chart-tren").getContext("2d");
        if (chartTrenInstance) chartTrenInstance.destroy();

        chartTrenInstance = new Chart(ctxTren, {
            type: "line",
            data: {
                labels: labelsTren,
                datasets: [{
                    label: "Jumlah Laporan",
                    data: dataTren,
                    borderColor: "#647d68", // theme green
                    backgroundColor: "rgba(100, 125, 104, 0.1)",
                    borderWidth: 2,
                    pointBackgroundColor: "#fff",
                    pointBorderColor: "#647d68",
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.3,
                }, ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ` ${context.parsed.y.toLocaleString("id-ID")} Laporan`;
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [4, 4]
                        },
                        ticks: {
                            precision: 0
                        } // whole numbers only
                    },
                    x: {
                        grid: {
                            display: false
                        },
                    },
                },
            },
        });

        // 2. Chart Proporsi Koleksi (Doughnut Chart)
        let totalBukuFisik = 0;
        let totalBukuDigital = 0;

        const maxBukuPerPerpus = {};

        data.forEach((item) => {
            const perpus = item.perpus_nama || 'Unknown';
            if (!maxBukuPerPerpus[perpus]) {
                maxBukuPerPerpus[perpus] = {
                    fisik: 0,
                    digital: 0
                };
            }
            const fisik = item.jumlah_buku || 0;
            const digital = item.jumlah_buku_digital || 0;

            if (fisik > maxBukuPerPerpus[perpus].fisik) {
                maxBukuPerPerpus[perpus].fisik = fisik;
            }
            if (digital > maxBukuPerPerpus[perpus].digital) {
                maxBukuPerPerpus[perpus].digital = digital;
            }
        });

        Object.values(maxBukuPerPerpus).forEach((koleksi) => {
            totalBukuFisik += koleksi.fisik;
            totalBukuDigital += koleksi.digital;
        });

        const ctxKoleksi = document.getElementById("chart-koleksi").getContext("2d");
        if (chartKoleksiInstance) chartKoleksiInstance.destroy();

        chartKoleksiInstance = new Chart(ctxKoleksi, {
            type: "doughnut",
            data: {
                labels: ["Buku Fisik", "Buku Digital"],
                datasets: [{
                    data: [totalBukuFisik, totalBukuDigital],
                    backgroundColor: ["#647d68", "#3b82f6"],
                    borderWidth: 0,
                    hoverOffset: 4,
                }, ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "70%",
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed;
                                const total = context.chart._metasets[context.datasetIndex].total;
                                const percentage = Math.round((value / total) * 100) + "%";
                                return ` ${value.toLocaleString("id-ID")} (${percentage})`;
                            },
                        },
                    },
                },
            },
        });
    }

    function renderTable(data) {
        if (dataTableInstance) {
            dataTableInstance.destroy();
        }

        const tbody = document.getElementById("table-body");
        tbody.innerHTML = "";

        data.forEach((item) => {
            const tr = document.createElement("tr");
            tr.className = "hover:bg-slate-50 transition border-b border-slate-100";

            const isVerified = item.is_verified === "Sudah Diverifikasi";
            const statusBadge = isVerified ?
                `<span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-600"><i class="fa-solid fa-check-circle"></i> Terverifikasi</span>` :
                `<span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-500"><i class="fa-solid fa-clock"></i> Menunggu</span>`;

            tr.innerHTML = `
            <td class="whitespace-nowrap px-4 py-3">${item.bulan || "-"} ${item.tahun || ""}</td>
            <td class="px-4 py-3 font-medium text-slate-700 max-w-[200px] truncate" title="${item.perpus_nama || '-'}">${item.perpus_nama || "-"}</td>
            <td class="whitespace-nowrap px-4 py-3 text-right">${(item.jumlah_buku || 0).toLocaleString("id-ID")}</td>
            <td class="whitespace-nowrap px-4 py-3 text-right">${(item.jumlah_buku_digital || 0).toLocaleString("id-ID")}</td>
            <td class="whitespace-nowrap px-4 py-3 text-right">${(item.jumlah_komputer || 0).toLocaleString("id-ID")}</td>
            <td class="whitespace-nowrap px-4 py-3 text-right font-medium text-theme-dark">${(item.bandwidth || 0).toLocaleString("id-ID")} <span class="text-xs text-slate-400 font-normal">Mbps</span></td>
            <td class="whitespace-nowrap px-4 py-3 text-center">${statusBadge}</td>
        `;

            tbody.appendChild(tr);
        });

        // Inisialisasi DataTable
        dataTableInstance = new DataTable("#tabel-peningkatan", {
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Data tidak ditemukan",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya",
                },
            },
            pageLength: 10,
            ordering: true,
            responsive: true,
        });
    }
</script>
