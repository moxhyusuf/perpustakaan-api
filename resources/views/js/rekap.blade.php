<script>
    // Logic untuk halaman Rekapitulasi (Kombinasi Semua Data)
    document.addEventListener("DOMContentLoaded", async () => {
        try {
            // 1. Fetch Semua Data secara paralel
            const [pengunjungData, pelibatanData, publikasiData, kpiData] =
            await Promise.all([
                ApiService.getPengunjung(),
                ApiService.getPelibatan(),
                ApiService.getPublikasi(),
                ApiService.getKPI(),
            ]);

            if (!kpiData) {
                throw new Error("Data utama (KPI) tidak tersedia.");
            }

            // 2. Gabungkan Data berdasarkan Nama Perpustakaan
            const combinedData = {};

            // Inisialisasi dari KPI (sebagai master data perpustakaan)
            kpiData.forEach((item) => {
                const key = item.nama_perpustakaan.trim().toLowerCase();
                let kategori = "";
                const skor = parseFloat(item.skor) || 0;

                // DISESUAIKAN: Menggunakan standard rentang kategori baru
                if (skor >= 76) kategori = "Sangat Baik";
                else if (skor >= 26) kategori = "Cukup";
                else kategori = "Kurang";

                combinedData[key] = {
                    nama_asli: item.nama_perpustakaan,
                    skor: skor,
                    kategori: kategori,
                    total_pengunjung: 0,
                    total_kegiatan: 0,
                    total_publikasi: 0,
                };
            });

            // Agregasi Pengunjung
            if (pengunjungData) {
                pengunjungData.forEach((item) => {
                    const key = item.perpus_nama.trim().toLowerCase();
                    if (!combinedData[key]) {
                        combinedData[key] = {
                            nama_asli: item.perpus_nama,
                            skor: 0,
                            kategori: "-",
                            total_pengunjung: 0,
                            total_kegiatan: 0,
                            total_publikasi: 0,
                        };
                    }
                    combinedData[key].total_pengunjung +=
                        parseInt(item.total_pengunjung) || 0;
                });
            }

            // Agregasi Pelibatan (Kegiatan)
            if (pelibatanData) {
                pelibatanData.forEach((item) => {
                    const key = item.perpus_nama.trim().toLowerCase();
                    if (!combinedData[key]) {
                        combinedData[key] = {
                            nama_asli: item.perpus_nama,
                            skor: 0,
                            kategori: "-",
                            total_pengunjung: 0,
                            total_kegiatan: 0,
                            total_publikasi: 0,
                        };
                    }
                    combinedData[key].total_kegiatan += 1; // hitung jumlah kegiatan
                });
            }

            // Agregasi Publikasi
            if (publikasiData) {
                publikasiData.forEach((item) => {
                    const key = item.perpus_nama.trim().toLowerCase();
                    if (!combinedData[key]) {
                        combinedData[key] = {
                            nama_asli: item.perpus_nama,
                            skor: 0,
                            kategori: "-",
                            total_pengunjung: 0,
                            total_kegiatan: 0,
                            total_publikasi: 0,
                        };
                    }
                    combinedData[key].total_publikasi += 1;
                });
            }

            // Konversi object ke array
            let finalData = Object.values(combinedData);

            // --- 3. Render DataTables & Filter ---
            let dataTable = null;

            const updateSummaryCards = (dataToRender) => {
                const sumPerpus = dataToRender.length;
                const sumPengunjung = dataToRender.reduce(
                    (a, b) => a + b.total_pengunjung,
                    0,
                );
                const sumKegiatan = dataToRender.reduce(
                    (a, b) => a + b.total_kegiatan,
                    0,
                );
                const sumPublikasi = dataToRender.reduce(
                    (a, b) => a + b.total_publikasi,
                    0,
                );

                document.getElementById("sum-perpus").innerText =
                    sumPerpus.toLocaleString("id-ID");
                document.getElementById("sum-pengunjung").innerText =
                    sumPengunjung.toLocaleString("id-ID");
                document.getElementById("sum-kegiatan").innerText =
                    sumKegiatan.toLocaleString("id-ID");
                document.getElementById("sum-publikasi").innerText =
                    sumPublikasi.toLocaleString("id-ID");
            };

            const renderTable = (dataToRender) => {
                updateSummaryCards(dataToRender);

                // Jika DataTables belum diinisialisasi
                if (!dataTable) {
                    // Buat HTML pertama kali
                    const tableBody = document.querySelector("#table-rekap tbody");
                    tableBody.innerHTML = "";

                    if (dataToRender.length === 0) {
                        tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-slate-500">Tidak ada data yang sesuai dengan filter.</td></tr>`;
                    } else {
                        dataToRender.forEach((item) => {
                            const tr = document.createElement("tr");

                            // DISESUAIKAN: Class badge disesuaikan dengan kategori baru
                            let badgeClass = "";
                            if (item.kategori === "Sangat Baik")
                                badgeClass = "bg-emerald-100 text-emerald-700 border-emerald-200";
                            else if (item.kategori === "Cukup")
                                badgeClass = "bg-amber-100 text-amber-700 border-amber-200";
                            else if (item.kategori === "Kurang")
                                badgeClass = "bg-red-100 text-red-700 border-red-200";
                            else badgeClass = "bg-slate-100 text-slate-700 border-slate-200";

                            tr.innerHTML = `
                <td class="font-medium text-slate-700 whitespace-normal min-w-[200px]">${item.nama_asli}</td>
                <td class="text-center font-semibold text-slate-800">${item.skor > 0 ? item.skor : "-"}</td>
                <td class="text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border ${badgeClass}">
                        ${item.kategori}
                    </span>
                </td>
                <td class="text-center">${item.total_pengunjung.toLocaleString("id-ID")}</td>
                <td class="text-center">${item.total_kegiatan.toLocaleString("id-ID")}</td>
                <td class="text-center">${item.total_publikasi.toLocaleString("id-ID")}</td>
            `;
                            tableBody.appendChild(tr);
                        });
                    }

                    dataTable = $("#table-rekap").DataTable({
                        responsive: true,
                        language: {
                            search: "Cari Perpustakaan:",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                            infoEmpty: "Data tidak ditemukan",
                            emptyTable: "Tidak ada data yang tersedia",
                            paginate: {
                                first: "Pertama",
                                last: "Terakhir",
                                next: "Selanjutnya",
                                previous: "Sebelumnya",
                            },
                        },
                        pageLength: 25,
                        bLengthChange: false,
                    });
                } else {
                    // Jika sudah ada instance DataTables, cukup perbarui datanya secara internal
                    dataTable.clear();

                    const rows = dataToRender.map((item) => {
                        // DISESUAIKAN: Class badge internal DataTables re-draw
                        let badgeClass = "";
                        if (item.kategori === "Sangat Baik")
                            badgeClass = "bg-emerald-100 text-emerald-700 border-emerald-200";
                        else if (item.kategori === "Cukup")
                            badgeClass = "bg-amber-100 text-amber-700 border-amber-200";
                        else if (item.kategori === "Kurang")
                            badgeClass = "bg-red-100 text-red-700 border-red-200";
                        else badgeClass = "bg-slate-100 text-slate-700 border-slate-200";

                        return [
                            `<span class="font-medium text-slate-700 whitespace-normal">${item.nama_asli}</span>`,
                            `<div class="text-center font-semibold text-slate-800">${item.skor > 0 ? item.skor : "-"}</div>`,
                            `<div class="text-center"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border ${badgeClass}">${item.kategori}</span></div>`,
                            `<div class="text-center">${item.total_pengunjung.toLocaleString("id-ID")}</div>`,
                            `<div class="text-center">${item.total_kegiatan.toLocaleString("id-ID")}</div>`,
                            `<div class="text-center">${item.total_publikasi.toLocaleString("id-ID")}</div>`,
                        ];
                    });

                    if (rows.length > 0) {
                        dataTable.rows.add(rows);
                    }
                    dataTable.draw();
                }
            };

            // Render awal
            renderTable(finalData);

            // --- 4. Logic Filter ---
            const filterKpi = document.getElementById("filter-kpi");
            const filterKegiatan = document.getElementById("filter-kegiatan");

            const applyFilters = () => {
                const kpiVal = filterKpi.value;
                const kegVal = filterKegiatan.value;

                let filtered = finalData.filter((item) => {
                    let matchKpi = true;
                    let matchKeg = true;

                    if (kpiVal !== "all") {
                        matchKpi = item.kategori === kpiVal;
                    }

                    if (kegVal !== "all") {
                        if (kegVal === "aktif") matchKeg = item.total_kegiatan > 0;
                        if (kegVal === "pasif") matchKeg = item.total_kegiatan === 0;
                    }

                    return matchKpi && matchKeg;
                });

                renderTable(filtered);
            };

            filterKpi.addEventListener("change", applyFilters);
            filterKegiatan.addEventListener("change", applyFilters);

            document
                .getElementById("btn-reset-filter")
                .addEventListener("click", () => {
                    filterKpi.value = "all";
                    filterKegiatan.value = "all";
                    renderTable(finalData);
                    if (dataTable) {
                        dataTable.search("").draw();
                    }
                });

            // --- 5. Export PDF menggunakan html2pdf ---
            document.getElementById("btn-export-pdf").addEventListener("click", () => {
                // Tampilkan Kop Surat Khusus Print
                const printHeader = document.getElementById("print-header");
                printHeader.classList.remove("hidden");

                // Hancurkan DataTable sementara agar semua row dirender (tidak kena pagination)
                if (dataTable) dataTable.destroy();

                const element = document.getElementById("export-container");
                const opt = {
                    margin: [15, 15, 15, 15], // margin dalam mm
                    filename: "Rekapitulasi_TPBIS_Probolinggo.pdf",
                    image: {
                        type: "jpeg",
                        quality: 0.98
                    },
                    html2canvas: {
                        scale: 2,
                        useCORS: true
                    },
                    jsPDF: {
                        unit: "mm",
                        format: "a4",
                        orientation: "portrait"
                    },
                };

                // Tambahkan elemen Kop ke dalam element yang akan di-print sementara
                element.insertBefore(printHeader, element.firstChild);

                // Ganti style sementara untuk print agar lebih pas
                element.classList.add("border-none", "shadow-none");

                html2pdf()
                    .set(opt)
                    .from(element)
                    .save()
                    .then(() => {
                        // Kembalikan ke keadaan semula setelah export
                        element.removeChild(printHeader);
                        printHeader.classList.add("hidden");
                        element.classList.remove("border-none", "shadow-none");

                        // Re-init DataTable
                        dataTable = $("#table-rekap").DataTable({
                            responsive: true,
                            language: {
                                search: "Cari Perpustakaan:",
                                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                            },
                            pageLength: 25,
                            bLengthChange: false,
                        });
                    });
            });
        } catch (error) {
            console.error("Error pada halaman rekap:", error);
            document.querySelector("#table-rekap tbody").innerHTML =
                `<tr><td colspan="6" class="text-center text-red-500 py-4">Gagal memuat data: ${error.message}</td></tr>`;
        }
    });
</script>
