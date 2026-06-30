<script>
    document.addEventListener("DOMContentLoaded", () => {
        initPublikasi();
    });

    let chartTrenInstance = null;
    let chartJenisInstance = null;
    let dataTableInstance = null;

    async function initPublikasi() {
        try {
            const data = await window.ApiService.getPublikasi();

            if (!data || data.length === 0) {
                renderEmptyState();
                return;
            }

            renderSummary(data);
            renderCharts(data);
            renderTable(data);
        } catch (error) {
            console.error("Gagal memuat data publikasi:", error);
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
        document.getElementById("stat-total").textContent = "0";
        document.getElementById("stat-verified").textContent = "0";
        document.getElementById("stat-jenis").textContent = "-";

        document.getElementById("table-body").innerHTML = `
        <tr>
            <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                Data tidak ditemukan.
            </td>
        </tr>
    `;
    }

    function renderSummary(data) {
        const totalPublikasi = data.length;
        let verifiedCount = 0;
        const jenisMap = {};

        data.forEach((item) => {
            if (item.is_verified === "Sudah Diverifikasi") {
                verifiedCount++;
            }
            const jenis = item.jenis_publikasi || "Tidak Diketahui";
            if (!jenisMap[jenis]) jenisMap[jenis] = 0;
            jenisMap[jenis]++;
        });

        let topJenis = "-";
        let maxCount = 0;
        for (const [jenis, count] of Object.entries(jenisMap)) {
            if (count > maxCount) {
                maxCount = count;
                topJenis = jenis;
            }
        }

        document.getElementById("stat-total").textContent = totalPublikasi.toLocaleString("id-ID");
        document.getElementById("stat-verified").textContent = verifiedCount.toLocaleString("id-ID");
        document.getElementById("stat-jenis").textContent = topJenis;
        document.getElementById("stat-jenis").title = topJenis; // hover tooltip if truncated
    }

    function renderCharts(data) {
        // 1. Chart Tren per Bulan (Line Chart based on `tanggal`)
        const bulanMap = {};
        data.forEach((item) => {
            if (item.tanggal && item.tanggal !== "-") {
                // Assuming format YYYY-MM-DD
                const dateParts = item.tanggal.split("-");
                if (dateParts.length >= 2) {
                    const year = dateParts[0];
                    const monthNum = parseInt(dateParts[1], 10);

                    // Convert month number to month name for better UI
                    const monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"];
                    const monthName = monthNames[monthNum - 1] || dateParts[1];

                    const label = `${monthName} ${year}`;
                    if (!bulanMap[label]) {
                        bulanMap[label] = 0;
                    }
                    bulanMap[label]++;
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
                    label: "Jumlah Publikasi",
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
                                return ` ${context.parsed.y.toLocaleString("id-ID")} Publikasi`;
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

        // 2. Chart Proporsi Jenis (Doughnut Chart)
        const jenisMap = {};
        data.forEach((item) => {
            let jenis = item.jenis_publikasi || 'Tidak Diketahui';
            if (!jenisMap[jenis]) {
                jenisMap[jenis] = 0;
            }
            jenisMap[jenis]++;
        });

        const labelsJenis = Object.keys(jenisMap);
        const dataJenis = Object.values(jenisMap);

        const bgColors = [
            '#647d68', '#3b82f6', '#ec4899', '#f59e0b', '#8b5cf6',
            '#10b981', '#f43f5e', '#6366f1', '#14b8a6', '#f97316'
        ];

        const ctxJenis = document.getElementById("chart-jenis").getContext("2d");
        if (chartJenisInstance) chartJenisInstance.destroy();

        chartJenisInstance = new Chart(ctxJenis, {
            type: "doughnut",
            data: {
                labels: labelsJenis,
                datasets: [{
                    data: dataJenis,
                    backgroundColor: bgColors.slice(0, labelsJenis.length),
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
                                return ` ${value.toLocaleString("id-ID")} Publikasi (${percentage})`;
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

            // Title with link logic
            let judulDisplay = `<span class="font-medium text-slate-700  inline-block truncate" title="${item.judul || '-'}">${item.judul || "-"}</span>`;
            if (item.link && item.link !== "-" && item.link !== "") {
                judulDisplay = `
            <a href="${item.link}" target="_blank" rel="noopener noreferrer" class="font-medium text-blue-600 hover:text-blue-800 transition  inline-block break-words" title="Buka Tautan: ${item.judul} ">
                ${item.judul || "-"} <i class="fa-solid fa-arrow-up-right-from-square text-[10px] ml-1 opacity-70"></i>
            </a >
        `;
            }

            tr.innerHTML = `
        <td td class="whitespace-nowrap px-4 py-3" > ${item.tanggal || "-"}</td >
            <td class="px-4 py-3">${judulDisplay}</td>
            <td class="px-4 py-3">
                <div class="text-slate-700 font-medium">${item.nama_media || "-"}</div>
                <div class="text-xs text-slate-400 mt-0.5">${item.jenis_publikasi || "-"}</div>
            </td>
            <td class="px-4 py-3">${item.perpus_nama || "-"}</td>
            <td class="whitespace-nowrap px-4 py-3 text-center">${statusBadge}</td>
      `;

            tbody.appendChild(tr);
        });

        // Inisialisasi DataTable
        dataTableInstance = new DataTable("#tabel-publikasi", {
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
</script>
