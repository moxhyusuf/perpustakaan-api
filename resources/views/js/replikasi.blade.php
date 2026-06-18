<script>
    document.addEventListener("DOMContentLoaded", () => {
        initReplikasi();
    });

    let chartTrenInstance = null;
    let chartSumberInstance = null;
    let dataTableInstance = null;

    async function initReplikasi() {
        try {
            const data = await window.ApiService.getReplikasi();

            if (!data || data.length === 0) {
                renderEmptyState();
                return;
            }

            renderSummary(data);
            renderCharts(data);
            renderTable(data);
        } catch (error) {
            console.error("Gagal memuat data replikasi:", error);
            document.getElementById("table-body").innerHTML = `
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-red-500">
                    <i class="fa-solid fa-triangle-exclamation mr-2"></i> Gagal memuat data.
                </td>
            </tr>
        `;
        }
    }

    function renderEmptyState() {
        document.getElementById("stat-dana").textContent = "Rp 0";
        document.getElementById("stat-desa").textContent = "0";
        document.getElementById("stat-laporan").textContent = "0";

        document.getElementById("table-body").innerHTML = `
        <tr>
            <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                Data tidak ditemukan.
            </td>
        </tr>
    `;
    }

    function renderSummary(data) {
        let totalDana = 0;
        let totalDesa = 0;
        const totalLaporan = data.length;

        data.forEach((item) => {
            totalDana += Number(item.dana) || 0;
            totalDesa += Number(item.jumlah_desa) || 0;
        });

        document.getElementById("stat-dana").textContent = formatRupiah(totalDana);
        document.getElementById("stat-desa").textContent = totalDesa.toLocaleString("id-ID");
        document.getElementById("stat-laporan").textContent = totalLaporan.toLocaleString("id-ID");
    }

    function renderCharts(data) {
        // 1. Chart Tren per Bulan (Line Chart based on `tanggal_input`)
        const bulanMap = {};
        data.forEach((item) => {
            if (item.tanggal_input && item.tanggal_input !== "-") {
                const dateParts = item.tanggal_input.split("-");
                if (dateParts.length >= 2) {
                    const year = dateParts[0];
                    const monthNum = parseInt(dateParts[1], 10);

                    const monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"];
                    const monthName = monthNames[monthNum - 1] || dateParts[1];

                    const label = `${monthName} ${year}`;
                    if (!bulanMap[label]) {
                        bulanMap[label] = 0;
                    }
                    // Menambahkan total dana per bulan
                    bulanMap[label] += Number(item.dana) || 0;
                }
            }
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
                    label: "Dana Replikasi",
                    data: dataTren,
                    borderColor: "#647d68",
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
                                return ` ${formatRupiah(context.parsed.y)}`;
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
                            callback: function(value) {
                                // Shorten large numbers for Y axis (e.g., 1M, 1Jt)
                                if (value >= 1e9) {
                                    return 'Rp ' + (value / 1e9) + ' M';
                                }
                                if (value >= 1e6) {
                                    return 'Rp ' + (value / 1e6) + ' Jt';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                    },
                },
            },
        });

        // 2. Chart Proporsi Sumber Dana (Doughnut Chart)
        const sumberMap = {};
        data.forEach((item) => {
            let sumber = item.sumber_dana || 'Lainnya';
            if (!sumberMap[sumber]) {
                sumberMap[sumber] = 0;
            }
            sumberMap[sumber] += Number(item.dana) || 0;
        });

        const labelsSumber = Object.keys(sumberMap);
        const dataSumber = Object.values(sumberMap);

        const bgColors = [
            '#647d68', '#3b82f6', '#f59e0b', '#ec4899', '#8b5cf6',
            '#10b981', '#f43f5e', '#6366f1', '#14b8a6', '#f97316'
        ];

        const ctxSumber = document.getElementById("chart-sumber").getContext("2d");
        if (chartSumberInstance) chartSumberInstance.destroy();

        chartSumberInstance = new Chart(ctxSumber, {
            type: "doughnut",
            data: {
                labels: labelsSumber,
                datasets: [{
                    data: dataSumber,
                    backgroundColor: bgColors.slice(0, labelsSumber.length),
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
                                return ` ${formatRupiah(value)} (${percentage})`;
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
            <td class="whitespace-nowrap px-4 py-3">${item.tanggal_input || "-"}</td>
            <td class="px-4 py-3 font-medium text-slate-700">${item.sumber_dana || "-"}</td>
            <td class="px-4 py-3 text-blue-600 font-semibold">${item.jumlah_desa || "0"} Desa</td>
            <td class="px-4 py-3 font-semibold text-slate-700">${formatRupiah(item.dana || 0)}</td>
            <td class="whitespace-nowrap px-4 py-3 text-center">${statusBadge}</td>
        `;

            tbody.appendChild(tr);
        });

        // Inisialisasi DataTable
        dataTableInstance = new DataTable("#tabel-replikasi", {
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
            order: [
                [0, "desc"]
            ] // Sort by date descending by default
        });
    }

    // Utility function to format Rupiah
    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }
</script>
