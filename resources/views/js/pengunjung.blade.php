<script>
    document.addEventListener("DOMContentLoaded", () => {
        initPengunjung();
    });

    let chartTrenInstance = null;
    let chartGenderInstance = null;
    let dataTableInstance = null;

    async function initPengunjung() {
        try {
            const data = await window.ApiService.getPengunjung();

            if (!data || data.length === 0) {
                renderEmptyState();
                return;
            }

            renderSummary(data);
            renderCharts(data);
            renderTable(data);
        } catch (error) {
            console.error("Gagal memuat data pengunjung:", error);
            document.getElementById("table-body").innerHTML = `
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-red-500">
                    <i class="fa-solid fa-triangle-exclamation mr-2"></i> Gagal memuat data.
                </td>
            </tr>
        `;
        }
    }

    function renderEmptyState() {
        document.getElementById("stat-total").textContent = "0";
        document.getElementById("stat-laki").textContent = "0";
        document.getElementById("stat-perempuan").textContent = "0";

        document.getElementById("table-body").innerHTML = `
        <tr>
            <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                Data tidak ditemukan.
            </td>
        </tr>
    `;
    }

    function renderSummary(data) {
        let totalLaki = 0;
        let totalPerempuan = 0;
        let totalAll = 0;

        data.forEach((item) => {
            totalLaki += item.pengunjung_laki || 0;
            totalPerempuan += item.pengunjung_perempuan || 0;
            totalAll += item.total_pengunjung || 0;
        });

        document.getElementById("stat-total").textContent =
            totalAll.toLocaleString("id-ID");
        document.getElementById("stat-laki").textContent =
            totalLaki.toLocaleString("id-ID");
        document.getElementById("stat-perempuan").textContent =
            totalPerempuan.toLocaleString("id-ID");
    }

    function renderCharts(data) {
        // 1. Chart Tren Periode
        const periodeMap = {};
        data.forEach((item) => {
            const p = item.periode || "Tidak Diketahui";
            if (!periodeMap[p]) {
                periodeMap[p] = 0;
            }
            periodeMap[p] += item.total_pengunjung || 0;
        });

        // Kita asumsikan periode sudah berurutan secara temporal di JSON atau kita bisa urutkan
        // Tapi karena format "Jan 2026", kita biarkan urutan dari kemunculan jika itu merepresentasikan data secara alami
        const labelsTren = Object.keys(periodeMap);
        const dataTren = Object.values(periodeMap);

        const ctxTren = document.getElementById("chart-tren").getContext("2d");
        if (chartTrenInstance) chartTrenInstance.destroy();

        chartTrenInstance = new Chart(ctxTren, {
            type: "line",
            data: {
                labels: labelsTren,
                datasets: [{
                    label: "Total Pengunjung",
                    data: dataTren,
                    borderColor: "#647d68", // theme.green
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
                                return ` ${context.parsed.y.toLocaleString("id-ID")} pengunjung`;
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
                    },
                    x: {
                        grid: {
                            display: false
                        },
                    },
                },
            },
        });

        // 2. Chart Gender
        let totalLaki = 0;
        let totalPerempuan = 0;
        data.forEach((item) => {
            totalLaki += item.pengunjung_laki || 0;
            totalPerempuan += item.pengunjung_perempuan || 0;
        });

        const ctxGender = document.getElementById("chart-gender").getContext("2d");
        if (chartGenderInstance) chartGenderInstance.destroy();

        chartGenderInstance = new Chart(ctxGender, {
            type: "doughnut",
            data: {
                labels: ["Laki-laki", "Perempuan"],
                datasets: [{
                    data: [totalLaki, totalPerempuan],
                    backgroundColor: ["#3b82f6", "#ec4899"], // blue-500, pink-500
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

            tr.innerHTML = `
            <td class="whitespace-nowrap px-4 py-3">${item.periode || "-"}</td>
            <td class="px-4 py-3 font-medium text-slate-700">${item.perpus_nama || "-"}</td>
            <td class="px-4 py-3">${item.kecamatan_name || "-"}</td>
            <td class="whitespace-nowrap px-4 py-3 text-right">${(item.pengunjung_laki || 0).toLocaleString("id-ID")}</td>
            <td class="whitespace-nowrap px-4 py-3 text-right">${(item.pengunjung_perempuan || 0).toLocaleString("id-ID")}</td>
            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-theme-dark">${(item.total_pengunjung || 0).toLocaleString("id-ID")}</td>
        `;

            tbody.appendChild(tr);
        });

        // Inisialisasi DataTable
        dataTableInstance = new DataTable("#tabel-pengunjung", {
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
