<script>
    document.addEventListener("DOMContentLoaded", () => {
        initPelibatan();
        initGalleryModalEvents();
    });

    let chartBidangInstance = null;
    let chartKecamatanInstance = null;
    let dataTableInstance = null;

    async function initPelibatan() {
        try {
            const data = await window.ApiService.getPelibatan();

            if (!data || data.length === 0) {
                renderEmptyState();
                return;
            }

            renderSummary(data);
            renderCharts(data);
            renderTable(data);
        } catch (error) {
            console.error("Gagal memuat data pelibatan:", error);
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
        document.getElementById("stat-kegiatan").textContent = "0";
        document.getElementById("stat-peserta").textContent = "0";
        document.getElementById("stat-rata").textContent = "0";

        document.getElementById("table-body").innerHTML = `
        <tr>
            <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                Data tidak ditemukan.
            </td>
        </tr>
    `;
    }

    function renderSummary(data) {
        const totalKegiatan = data.length;
        let totalPeserta = 0;

        data.forEach((item) => {
            totalPeserta += item.jumlah_peserta || 0;
        });

        const rataRata = totalKegiatan > 0 ? Math.round(totalPeserta / totalKegiatan) : 0;

        document.getElementById("stat-kegiatan").textContent = totalKegiatan.toLocaleString("id-ID");
        document.getElementById("stat-peserta").textContent = totalPeserta.toLocaleString("id-ID");
        document.getElementById("stat-rata").textContent = rataRata.toLocaleString("id-ID");
    }

    function renderCharts(data) {
        // 1. Chart Bidang Kegiatan (Horizontal Bar)
        const bidangMap = {};
        data.forEach((item) => {
            let bidang = item.bidang_kegiatan || 'Tidak Diketahui';
            if (bidang.trim() === '-' || bidang.trim() === '') bidang = 'Lainnya';
            if (!bidangMap[bidang]) {
                bidangMap[bidang] = 0;
            }
            bidangMap[bidang]++;
        });

        // Sort by count descending
        const sortedBidang = Object.entries(bidangMap).sort((a, b) => b[1] - a[1]);
        // Limit to top 7 for readability
        const topBidang = sortedBidang.slice(0, 7);
        const labelsBidang = topBidang.map(item => item[0]);
        const dataBidang = topBidang.map(item => item[1]);

        const ctxBidang = document.getElementById("chart-bidang").getContext("2d");
        if (chartBidangInstance) chartBidangInstance.destroy();

        chartBidangInstance = new Chart(ctxBidang, {
            type: "bar",
            data: {
                labels: labelsBidang,
                datasets: [{
                    label: "Jumlah Kegiatan",
                    data: dataBidang,
                    backgroundColor: "rgba(100, 125, 104, 0.8)", // theme green with opacity
                    borderColor: "#647d68", // theme green
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.8
                }, ],
            },
            options: {
                indexAxis: 'y', // horizontal bar chart
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ` ${context.parsed.x.toLocaleString("id-ID")} Kegiatan`;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [4, 4]
                        },
                    },
                    y: {
                        grid: {
                            display: false
                        },
                    },
                },
            },
        });

        // 2. Chart Sebaran per Kecamatan (Doughnut)
        const kecamatanMap = {};
        data.forEach((item) => {
            let kec = item.kecamatan_name || 'Tidak Diketahui';
            if (kec.trim() === '-' || kec.trim() === '') kec = 'Lainnya';
            if (!kecamatanMap[kec]) {
                kecamatanMap[kec] = 0;
            }
            kecamatanMap[kec]++;
        });

        const labelsKecamatan = Object.keys(kecamatanMap);
        const dataKecamatan = Object.values(kecamatanMap);

        // Generate dynamic colors for doughnut
        const bgColors = [
            '#647d68', '#f59e0b', '#3b82f6', '#ec4899', '#8b5cf6',
            '#10b981', '#f43f5e', '#6366f1', '#14b8a6', '#f97316'
        ];

        const ctxKecamatan = document.getElementById("chart-kecamatan").getContext("2d");
        if (chartKecamatanInstance) chartKecamatanInstance.destroy();

        chartKecamatanInstance = new Chart(ctxKecamatan, {
            type: "doughnut",
            data: {
                labels: labelsKecamatan,
                datasets: [{
                    data: dataKecamatan,
                    backgroundColor: bgColors.slice(0, labelsKecamatan.length),
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
                        position: "right",
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            boxWidth: 8
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed;
                                const total = context.chart._metasets[context.datasetIndex].total;
                                const percentage = Math.round((value / total) * 100) + "%";
                                return ` ${value.toLocaleString("id-ID")} Kegiatan (${percentage})`;
                            },
                        },
                    },
                },
            },
        });
    }

    function renderAttachment(attachmentStr, namaKegiatan) {
        if (!attachmentStr) {
            return `<div class="w-20 h-20 flex items-center justify-center rounded-lg border border-dashed border-slate-300 bg-slate-50 text-slate-400">
                    <i class="fa-solid fa-image text-lg"></i>
                </div>`;
        }

        const urls = attachmentStr.split(',').map(u => u.trim()).filter(Boolean);
        const firstUrl = urls[0];
        const totalCount = urls.length;
        const safeTitle = (namaKegiatan || '').replace(/"/g, '&quot;');
        const encodedUrls = encodeURIComponent(JSON.stringify(urls));

        return `
        <div class="relative inline-block">
            <img src="${firstUrl}" alt="Attachment"
                 class="attachment-thumb w-24 h-24 object-cover rounded-lg shadow-sm border border-slate-200 cursor-pointer hover:opacity-80 transition"
                 data-urls="${encodedUrls}"
                 data-title="${safeTitle}"
                 onerror="this.onerror=null; this.closest('.relative').outerHTML='<div class=\\'w-20 h-20 flex items-center justify-center rounded-lg border border-dashed border-slate-300 bg-slate-50 text-slate-400\\'><i class=\\'fa-solid fa-image-slash text-lg\\'></i></div>';" />
            ${totalCount > 1 ? `<span class="absolute -top-1 -right-1 bg-theme-dark text-white text-[10px] font-semibold px-1.5 py-0.5 rounded-full leading-none">+${totalCount - 1}</span>` : ''}
        </div>
    `;
    }

    // ==================== GALLERY MODAL ====================
    let galleryUrls = [];
    let galleryIndex = 0;

    function openGalleryModal(urls, startIndex, title) {
        galleryUrls = urls;
        galleryIndex = startIndex;

        document.getElementById("gallery-title").textContent = title || "Foto Kegiatan";
        document.getElementById("gallery-modal").classList.remove("hidden");
        document.body.style.overflow = "hidden";

        renderGalleryThumbs();
        showGalleryImage(galleryIndex);
    }

    function closeGalleryModal() {
        document.getElementById("gallery-modal").classList.add("hidden");
        document.body.style.overflow = "";
        galleryUrls = [];
        galleryIndex = 0;
    }

    function showGalleryImage(index) {
        if (galleryUrls.length === 0) return;
        galleryIndex = (index + galleryUrls.length) % galleryUrls.length;

        document.getElementById("gallery-image").src = galleryUrls[galleryIndex];
        document.getElementById("gallery-counter").textContent =
            `${galleryIndex + 1} / ${galleryUrls.length}`;

        document.querySelectorAll("#gallery-thumbs img").forEach((thumb, i) => {
            thumb.classList.toggle("ring-2", i === galleryIndex);
            thumb.classList.toggle("ring-theme-green", i === galleryIndex);
            thumb.classList.toggle("opacity-50", i !== galleryIndex);
        });

        const showNav = galleryUrls.length > 1;
        document.getElementById("gallery-prev").classList.toggle("hidden", !showNav);
        document.getElementById("gallery-next").classList.toggle("hidden", !showNav);
    }

    function renderGalleryThumbs() {
        const container = document.getElementById("gallery-thumbs");
        if (galleryUrls.length <= 1) {
            container.innerHTML = "";
            return;
        }
        container.innerHTML = galleryUrls.map((url, i) => `
        <img src="${url}" data-index="${i}"
             class="gallery-thumb-item h-14 w-14 flex-shrink-0 object-cover rounded-md cursor-pointer border border-white/20 transition"
             onerror="this.style.display='none';" />
    `).join("");
    }

    function initGalleryModalEvents() {
        document.getElementById("gallery-close").addEventListener("click", closeGalleryModal);

        document.getElementById("gallery-modal").addEventListener("click", (e) => {
            if (e.target.id === "gallery-modal") closeGalleryModal(); // klik backdrop
        });

        document.getElementById("gallery-prev").addEventListener("click", () => showGalleryImage(galleryIndex - 1));
        document.getElementById("gallery-next").addEventListener("click", () => showGalleryImage(galleryIndex + 1));

        document.getElementById("gallery-thumbs").addEventListener("click", (e) => {
            const thumb = e.target.closest(".gallery-thumb-item");
            if (thumb) showGalleryImage(parseInt(thumb.dataset.index, 10));
        });

        document.addEventListener("keydown", (e) => {
            const modal = document.getElementById("gallery-modal");
            if (modal.classList.contains("hidden")) return;

            if (e.key === "Escape") closeGalleryModal();
            if (e.key === "ArrowLeft") showGalleryImage(galleryIndex - 1);
            if (e.key === "ArrowRight") showGalleryImage(galleryIndex + 1);
        });

        document.getElementById("table-body").addEventListener("click", (e) => {
            const thumb = e.target.closest(".attachment-thumb");
            if (!thumb) return;

            const urls = JSON.parse(decodeURIComponent(thumb.dataset.urls || "[]"));
            const title = thumb.dataset.title || "";
            openGalleryModal(urls, 0, title);
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
            <td class="whitespace-nowrap px-4 py-3">${item.tanggal || "-"}</td>
            <td class="px-4 py-3 font-medium text-slate-700 max-w-[200px] truncate" title="${item.nama_kegiatan || '-'}">${item.nama_kegiatan || "-"}</td>
            <td class="px-4 py-3">${renderAttachment(item.attachment, item.nama_kegiatan)}</td>
            <td class="px-4 py-3">${item.perpus_nama || "-"}</td>
            <td class="px-4 py-3">${item.kecamatan_name || "-"}</td>
            <td class="px-4 py-3">${item.bidang_kegiatan || "-"}</td>
            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-theme-dark">${(item.jumlah_peserta || 0).toLocaleString("id-ID")}</td>
        `;

            tbody.appendChild(tr);
        });

        dataTableInstance = new DataTable("#tabel-pelibatan", {
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
