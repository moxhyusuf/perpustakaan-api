<script>
    document.addEventListener("DOMContentLoaded", async () => {
        const loadingState = document.getElementById("loading-state");
        const emptyState = document.getElementById("empty-state");
        const perpusGrid = document.getElementById("perpus-grid");

        // Pagination states
        const CARDS_PER_PAGE = 12;
        let currentPage = 1;
        let allLibraries = [];
        let filteredLibraries = [];

        // Global library data dictionary (keyed by normalized name)
        const libraryDict = {};

        // Chart reference
        let modalChart = null;

        // Data lokasi perpustakaan
        let perpustakaanLocations = [];

        // Load koordinat perpustakaan dari JSON
        try {
            const response = await fetch("/data/lokasi-perpustakaan.json");

            if (!response.ok) {
                throw new Error("Gagal memuat lokasi perpustakaan");
            }

            perpustakaanLocations = await response.json();

            // console.log("Jumlah lokasi:", perpustakaanLocations.length);

        } catch (err) {
            console.error("Lokasi gagal dimuat", err);
        }

        try {
            // 1. Fetch seluruh data secara paralel
            const [kpiData, pengunjungData, pelibatanData, publikasiData, peningkatanData] = await Promise.all([
                ApiService.getKPI(),
                ApiService.getPengunjung(),
                ApiService.getPelibatan(),
                ApiService.getPublikasi(),
                ApiService.getFasilitas()
            ]);

            if (!kpiData) {
                throw new Error("Data utama KPI tidak dapat dimuat.");
            }

            // Helper fungsi menormalkan nama perpustakaan untuk key pencarian/pencocokan
            const getPerpusKey = (name) => {
                if (!name) return "";
                return name.trim().toLowerCase()
                    .replace(/^perpustakaan\s+desa\s+/i, "perpus ")
                    .replace(/^perpustakaan\s+/i, "perpus ")
                    .replace(/\s+/g, " ");
            };

            // 2. Inisialisasi Kamus Data Perpustakaan dari KPI (Master Data)
            kpiData.forEach(item => {
                const originalName = item.nama_perpustakaan;
                const key = getPerpusKey(originalName);

                const skorRaw = item.skor;
                const skor = (skorRaw === "-" || !skorRaw) ? "-" : parseFloat(skorRaw);

                // Kategorisasi skor: >75 Tinggi (hijau), >25-75 Sedang (kuning), <=25 Rendah (merah)
                let kategori = "Belum Dinilai";
                if (typeof skor === "number") {
                    if (skor > 75) kategori = "Tinggi";
                    else if (skor > 25) kategori = "Sedang";
                    else kategori = "Rendah";
                }

                libraryDict[key] = {
                    key: key,
                    nama: originalName,
                    provinsi: item.provinsi || "Jawa Timur",
                    kabupaten_kota: item.kabupaten_kota || "Kab. Probolinggo",
                    desa_kelurahan: (item.desa_kelurahan === "-" || !item.desa_kelurahan) ? "Desa" : item.desa_kelurahan,
                    kecamatan: "-", // akan diisi jika ditemukan di data laporan lain
                    skor: skor,
                    kategori: kategori,
                    pengunjung: [],
                    pelibatan: [],
                    publikasi: [],
                    peningkatan: [],
                    stats: {
                        total_pengunjung: 0,
                        total_pengunjung_laki: 0,
                        total_pengunjung_perempuan: 0,
                        total_kegiatan: 0,
                        total_publikasi: 0,
                        total_buku: 0,
                        total_buku_digital: 0,
                        total_komputer: 0,
                        bandwidth: 0
                    }
                };
            });

            // 3. Gabungkan Data Pengunjung
            if (pengunjungData) {
                pengunjungData.forEach(item => {
                    const key = getPerpusKey(item.perpus_nama);

                    // Jika perpustakaan belum ada di master KPI, buat entri baru (opsional, tapi bagus untuk kelengkapan)
                    if (!libraryDict[key]) {
                        libraryDict[key] = createEmptyLibraryEntry(key, item.perpus_nama);
                    }

                    libraryDict[key].pengunjung.push(item);

                    // Agregasi statistik
                    libraryDict[key].stats.total_pengunjung += parseInt(item.total_pengunjung) || 0;
                    libraryDict[key].stats.total_pengunjung_laki += parseInt(item.pengunjung_laki) || 0;
                    libraryDict[key].stats.total_pengunjung_perempuan += parseInt(item.pengunjung_perempuan) || 0;

                    // Deteksi kecamatan
                    if (item.kecamatan_name && item.kecamatan_name.trim() !== "-" && libraryDict[key].kecamatan === "-") {
                        libraryDict[key].kecamatan = item.kecamatan_name.trim();
                    }

                    // Isi desa jika di master masih "-"
                    if (item.desa_name && item.desa_name.trim() !== "-" && libraryDict[key].desa_kelurahan === "Desa") {
                        libraryDict[key].desa_kelurahan = item.desa_name.trim();
                    }
                });
            }

            // 4. Gabungkan Data Pelibatan Masyarakat
            if (pelibatanData) {
                pelibatanData.forEach(item => {
                    const key = getPerpusKey(item.perpus_nama);

                    if (!libraryDict[key]) {
                        libraryDict[key] = createEmptyLibraryEntry(key, item.perpus_nama);
                    }

                    libraryDict[key].pelibatan.push(item);
                    libraryDict[key].stats.total_kegiatan += 1;

                    // Deteksi kecamatan
                    if (item.kecamatan_name && item.kecamatan_name.trim() !== "-" && libraryDict[key].kecamatan === "-") {
                        libraryDict[key].kecamatan = item.kecamatan_name.trim();
                    }

                    // Isi desa jika di master masih "-"
                    if (item.desa_name && item.desa_name.trim() !== "-" && libraryDict[key].desa_kelurahan === "Desa") {
                        libraryDict[key].desa_kelurahan = item.desa_name.trim();
                    }
                });
            }

            // 5. Gabungkan Data Publikasi
            if (publikasiData) {
                publikasiData.forEach(item => {
                    const key = getPerpusKey(item.perpus_nama);

                    if (!libraryDict[key]) {
                        libraryDict[key] = createEmptyLibraryEntry(key, item.perpus_nama);
                    }

                    libraryDict[key].publikasi.push(item);
                    libraryDict[key].stats.total_publikasi += 1;

                    // Deteksi kecamatan
                    if (item.kecamatan_name && item.kecamatan_name.trim() !== "-" && libraryDict[key].kecamatan === "-") {
                        libraryDict[key].kecamatan = item.kecamatan_name.trim();
                    }

                    // Isi desa jika di master masih "-"
                    if (item.desa_name && item.desa_name.trim() !== "-" && libraryDict[key].desa_kelurahan === "Desa") {
                        libraryDict[key].desa_kelurahan = item.desa_name.trim();
                    }
                });
            }

            if (peningkatanData) {
                peningkatanData.forEach(item => {
                    const key = getPerpusKey(item.perpus_nama);

                    if (!libraryDict[key]) {
                        libraryDict[key] = createEmptyLibraryEntry(key, item.perpus_nama);
                    }

                    libraryDict[key].peningkatan.push(item);

                    // Ambil data fasilitas yang paling update (berdasarkan periode input terakhir / nilai max)
                    libraryDict[key].stats.total_buku = Math.max(libraryDict[key].stats.total_buku, parseInt(item.jumlah_buku) || 0);
                    libraryDict[key].stats.total_buku_digital = Math.max(libraryDict[key].stats.total_buku_digital, parseInt(item.jumlah_buku_digital) || 0);
                    libraryDict[key].stats.total_komputer = Math.max(libraryDict[key].stats.total_komputer, parseInt(item.jumlah_komputer) || 0);
                    libraryDict[key].stats.bandwidth = Math.max(libraryDict[key].stats.bandwidth, parseInt(item.bandwidth) || 0);

                    // Deteksi kecamatan
                    if (item.kecamatan_name && item.kecamatan_name.trim() !== "-" && libraryDict[key].kecamatan === "-") {
                        libraryDict[key].kecamatan = item.kecamatan_name.trim();
                    }

                    // Isi desa jika di master masih "-"
                    if (item.desa_name && item.desa_name.trim() !== "-" && libraryDict[key].desa_kelurahan === "Desa") {
                        libraryDict[key].desa_kelurahan = item.desa_name.trim();
                    }
                });
            }

            // Simpan dictionary menjadi list array perpustakaan utuh
            allLibraries = Object.values(libraryDict);

            // console.log(
            //     allLibraries.map(x => x.nama).join("\n")
            // );

            // Sorting perpustakaan berdasarkan Nama secara alfabetis agar rapi
            allLibraries.sort((a, b) => a.nama.localeCompare(b.nama));

            // Sorting perpustakaan berdasarkan Nama secara alfabetis agar rapi
            allLibraries.sort((a, b) => a.nama.localeCompare(b.nama));

            // Set list filtered ke kondisi awal (semua perpustakaan)
            filteredLibraries = [...allLibraries];

            // 7. Sembunyikan loader & render dashboard counters
            loadingState.classList.add("hidden");
            renderSummaryCounters(allLibraries);

            // 8. Isi filter Kecamatan secara dinamis
            populateKecamatanFilter(allLibraries);

            // 9. Setup Filter Event Listeners
            setupFilters();
            initMapPerpustakaan();

            // 10. Render Cards Halaman Pertama
            currentPage = 1;
            updateUI();

        } catch (error) {
            console.error("Gagal menginisialisasi Direktori Perpustakaan:", error);
            loadingState.innerHTML = `
            <div class="text-red-500 py-10">
                <i class="fa-solid fa-triangle-exclamation text-3xl mb-3"></i>
                <p class="font-bold">Terjadi Kesalahan</p>
                <p class="text-xs text-slate-500 mt-1">${error.message}</p>
            </div>
        `;
        }

        function getMarkerColor(kategori) {

            switch ((kategori || "").toLowerCase()) {

                case "tinggi":
                    return "#16a34a"; // Hijau

                case "sedang":
                    return "#f59e0b"; // Kuning

                case "rendah":
                    return "#dc2626"; // Merah

                default:
                    return "#9ca3af"; // Abu
            }

        }

        function createMarkerIcon(color) {

            return L.divIcon({

                className: "",

                html: `
                <div style="
                    width:18px;
                    height:18px;
                    border-radius:50%;
                    background:${color};
                    border:3px solid #fff;
                    box-shadow:0 4px 12px rgba(0,0,0,.25);
                "></div>
                `,

                iconSize: [18, 18],
                iconAnchor: [9, 9]

            });

        }

        // letakkan di sini
        function normalizeName(nama) {
            return nama
                .toLowerCase()
                .replace(/^b24\s+/g, "")
                .replace(/^tbm\s+/g, "perpustakaan ")
                .replace(/^rumah baca/g, "perpustakaan")
                .replace(/\(.*?\)/g, "")
                .replace(/kecamatan\s+[a-z\s]+/g, "")
                .replace(/kab(\.|upaten)?\s+probolinggo/g, "")
                .replace(/\s+/g, " ")
                .trim();

            const aliasNama = {
                "perpustakaan patriot desa brumbungan lor": "perpustakaan brumbungan lor",

                "rumah baca cahaya probolinggo": "b24 perpustakaan rumah cahaya (rumah baca hasilkan karya)",

                "perpustakaan desa alasnyiur": "perpustakaan alas nyiur",

                "perpustakaan desa sentul": "perpustakaan sentul kab probolinggo",

                "perpustakaan desa setulus hati": "perpustakaan desa sutulus hati"
            };
        }



        function initMapPerpustakaan() {

            if (!perpustakaanLocations.length) {
                console.warn("Data lokasi belum tersedia");
                return;
            }

            const mapEl = document.getElementById("map-perpustakaan");

            if (!mapEl || typeof L === "undefined") return;

            // Jika map sudah pernah dibuat
            if (window.mapPerpustakaanInstance) {
                window.mapPerpustakaanInstance.remove();
            }

            window.mapPerpustakaanInstance = L.map("map-perpustakaan");

            L.tileLayer(
                "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png", {
                    attribution: "&copy; OpenStreetMap &copy; CARTO",
                    maxZoom: 19
                }
            ).addTo(window.mapPerpustakaanInstance);

            // Batas wilayah Kabupaten Probolinggo
            const probolinggoBounds = L.latLngBounds(
                [-8.15, 112.90], // Barat Daya
                [-7.45, 113.75] // Timur Laut
            );

            // Batasi agar peta fokus di wilayah Kabupaten Probolinggo
            window.mapPerpustakaanInstance.setMaxBounds(probolinggoBounds);

            // Tampilan awal
            window.mapPerpustakaanInstance.fitBounds(probolinggoBounds, {
                padding: [20, 20]
            });

            // ==========================
            // Marker Icon
            // ==========================

            const markerIcons = {

                tinggi: new L.Icon({
                    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png",
                    shadowUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                }),

                sedang: new L.Icon({
                    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-orange.png",
                    shadowUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                }),

                rendah: new L.Icon({
                    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png",
                    shadowUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                }),

                default: new L.Icon({
                    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png",
                    shadowUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                })

            };

            const markers = [];

            perpustakaanLocations.forEach((lokasi) => {

                const library = allLibraries.find(item =>
                    normalizeName(item.nama) === normalizeName(lokasi.nama)
                );

                let icon = markerIcons.default;

                if (library) {

                    switch (library.kategori) {

                        case "Tinggi":
                            icon = markerIcons.tinggi;
                            break;

                        case "Sedang":
                            icon = markerIcons.sedang;
                            break;

                        case "Rendah":
                            icon = markerIcons.rendah;
                            break;

                        default:
                            icon = markerIcons.default;
                    }

                }

                // console.log(
                //     lokasi.nama,
                //     library ? "✅ MATCH" : "❌ TIDAK MATCH"
                // );

                const marker = L.marker(
                        [lokasi.lat, lokasi.lng], {
                            icon: icon
                        }
                    )
                    .addTo(window.mapPerpustakaanInstance)
                    .bindPopup(`
                <div class="map-popup">

                    <div class="popup-title">
                        📚 ${lokasi.nama}
                    </div>

                    <div class="popup-divider"></div>

                    <div class="popup-item">
                        <span>Kategori</span>
                        <b>${library ? library.kategori : "Belum Dinilai"}</b>
                    </div>

                    <div class="popup-item">
                        <span>Skor KPI</span>
                        <b>${library ? library.skor : "-"}</b>
                    </div>

                    <div class="popup-divider"></div>

                    <a
                        href="https://www.google.com/maps?q=${lokasi.lat},${lokasi.lng}"
                        target="_blank"
                        class="popup-btn-map"
                        style="color:#ffffff !important; text-decoration:none;">
                        <i class="fa-solid fa-location-dot"></i>
                        Buka di Google Maps
                    </a>

                    <button
                        class="popup-btn-detail"
                        onclick="${library ? `openDetailModal('${library.key}')` : ''}"
                        ${library ? "" : "disabled"}>

                        <i class="fa-solid fa-book-open"></i>
                        Lihat Detail

                    </button>

                </div>
            `);

                markers.push(marker);

            });

            if (markers.length) {

                const group = L.featureGroup(markers);

                window.mapPerpustakaanInstance.fitBounds(group.getBounds(), {
                    padding: [30, 30],
                    maxZoom: 10
                });

            }

        }
        // Helper membuat entri kosong jika perpustakaan tidak ada di data KPI tapi ada di data lain
        function createEmptyLibraryEntry(key, rawName) {
            return {
                key: key,
                nama: rawName,
                provinsi: "Jawa Timur",
                kabupaten_kota: "Kab. Probolinggo",
                desa_kelurahan: "Desa",
                kecamatan: "-",
                skor: "-",
                kategori: "Belum Dinilai",
                pengunjung: [],
                pelibatan: [],
                publikasi: [],
                peningkatan: [],
                stats: {
                    total_pengunjung: 0,
                    total_pengunjung_laki: 0,
                    total_pengunjung_perempuan: 0,
                    total_kegiatan: 0,
                    total_publikasi: 0,
                    total_buku: 0,
                    total_buku_digital: 0,
                    total_komputer: 0,
                    bandwidth: 0
                }
            };
        }

        // Menghitung & Menampilkan Rangkuman Counter Status
        function renderSummaryCounters(data) {

            const total = data.length;

            let tinggi = 0;
            let sedang = 0;
            let rendah = 0;
            let belum = 0;

            data.forEach(item => {

                switch (item.kategori) {

                    case "Tinggi":
                        tinggi++;
                        break;

                    case "Sedang":
                        sedang++;
                        break;

                    case "Rendah":
                        rendah++;
                        break;

                    default:
                        belum++;
                        break;

                }

            });

            document.getElementById("count-total").innerText = total.toLocaleString("id-ID");
            document.getElementById("count-sangat-baik").innerText = tinggi.toLocaleString("id-ID");
            document.getElementById("count-cukup").innerText = sedang.toLocaleString("id-ID");
            document.getElementById("count-kurang").innerText = rendah.toLocaleString("id-ID");
            document.getElementById("count-belum").innerText = belum.toLocaleString("id-ID");

        }

        // Populasikan list filter kecamatan
        function populateKecamatanFilter(data) {
            const kecamatanSelect = document.getElementById("filter-kecamatan");
            const kecamatanSet = new Set();

            data.forEach(item => {
                if (item.kecamatan && item.kecamatan !== "-") {
                    kecamatanSet.add(item.kecamatan);
                }
            });

            const sortedKecamatan = Array.from(kecamatanSet).sort();
            sortedKecamatan.forEach(kec => {
                const option = document.createElement("option");
                option.value = kec;
                option.innerText = kec;
                kecamatanSelect.appendChild(option);
            });
        }

        // Set up filter-filter & event listener pencarian
        function setupFilters() {
            const searchInput = document.getElementById("search-input");
            const filterKategori = document.getElementById("filter-kategori");
            const filterKecamatan = document.getElementById("filter-kecamatan");
            const btnReset = document.getElementById("btn-reset-filter");
            const btnEmptyReset = document.getElementById("btn-empty-reset");

            const applyFilters = () => {
                const keyword = searchInput.value.toLowerCase().trim();
                const kategoriValue = filterKategori.value;
                const kecamatanValue = filterKecamatan.value;

                filteredLibraries = allLibraries.filter(item => {
                    // Search Keyword
                    const matchKeyword = item.nama.toLowerCase().includes(keyword) ||
                        item.desa_kelurahan.toLowerCase().includes(keyword) ||
                        item.kecamatan.toLowerCase().includes(keyword);

                    // KPI Kategori Filter
                    const matchKategori = !kategoriValue || item.kategori === kategoriValue;

                    // Kecamatan Filter
                    const matchKecamatan = !kecamatanValue || item.kecamatan === kecamatanValue;

                    return matchKeyword && matchKategori && matchKecamatan;
                });

                currentPage = 1; // Kembalikan ke halaman pertama setiap kali filter berubah
                updateUI();
            };

            // Event listener
            searchInput.addEventListener("input", applyFilters);
            filterKategori.addEventListener("change", applyFilters);
            filterKecamatan.addEventListener("change", applyFilters);

            const resetAllFilters = () => {
                searchInput.value = "";
                filterKategori.value = "";
                filterKecamatan.value = "";
                filteredLibraries = [...allLibraries];
                currentPage = 1;
                updateUI();
            };

            btnReset.addEventListener("click", resetAllFilters);
            btnEmptyReset.addEventListener("click", resetAllFilters);
        }

        // Fungsi Render Cards & Pagination Controls utama
        function updateUI() {
            perpusGrid.innerHTML = "";

            if (filteredLibraries.length === 0) {
                emptyState.classList.remove("hidden");
                perpusGrid.classList.add("hidden");
                document.getElementById("pagination-container").classList.add("hidden");
                return;
            }

            emptyState.classList.add("hidden");
            perpusGrid.classList.remove("hidden");
            document.getElementById("pagination-container").classList.remove("hidden");

            // Hitung index slice pagination
            const startIndex = (currentPage - 1) * CARDS_PER_PAGE;
            const endIndex = Math.min(startIndex + CARDS_PER_PAGE, filteredLibraries.length);
            const paginatedData = filteredLibraries.slice(startIndex, endIndex);

            // Render card individu
            paginatedData.forEach(item => {
                const card = createPerpusCardHTML(item);
                perpusGrid.appendChild(card);
            });

            // Render Pagination Info
            document.getElementById("pagination-info").innerHTML = `
            Menampilkan <span class="font-semibold text-slate-800">${startIndex + 1}</span> sampai <span class="font-semibold text-slate-800">${endIndex}</span> dari <span class="font-semibold text-slate-800">${filteredLibraries.length}</span> perpustakaan
        `;

            // Render Pagination Buttons
            renderPaginationButtons();

            // Sinkronkan marker di peta sesuai hasil filter
            if (typeof updateMapMarkers === "function") {
                updateMapMarkers(filteredLibraries);
            }
        }

        // Membuat elemen Card HTML perpustakaan
        function createPerpusCardHTML(item) {
            const div = document.createElement("article");
            div.className = "bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-soft hover:border-slate-300/80 transition duration-300 cursor-pointer flex flex-col justify-between hover-card";
            div.setAttribute("onclick", `openDetailModal('${item.key}')`);

            // Tentukan warna badge kpi
            let badgeColorClass = "bg-slate-100 text-slate-600";
            if (item.kategori === "Tinggi") badgeColorClass = "bg-emerald-50 text-emerald-700 border border-emerald-100";
            else if (item.kategori === "Sedang") badgeColorClass = "bg-amber-50 text-amber-700 border border-amber-100";
            else if (item.kategori === "Rendah") badgeColorClass = "bg-red-50 text-red-700 border border-red-100";

            // Format skor kpi
            const displaySkor = typeof item.skor === "number" ? item.skor.toFixed(1) : "-";

            div.innerHTML = `
            <div>
                <!-- Header Card -->
                <div class="flex items-start justify-between mb-5">

                    <!-- Icon Perpustakaan -->
                    <div class="flex items-center gap-3">

                        <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shadow-lg">
                            <i class="fa-solid fa-book-open-reader text-2xl"></i>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">
                                Perpustakaan Desa
                            </p>

                            <p class="text-xs text-slate-500 mt-1">
                                Kabupaten Probolinggo
                            </p>
                        </div>

                    </div>

                    <!-- Badge KPI -->
                    <span class="px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 ${badgeColorClass}">

                        ${
                            item.kategori === "Tinggi"
                            ? '<i class="fa-solid fa-circle text-[8px]"></i>'
                            : item.kategori === "Sedang"
                            ? '<i class="fa-solid fa-circle text-[8px]"></i>'
                            : item.kategori === "Rendah"
                            ? '<i class="fa-solid fa-circle text-[8px]"></i>'
                            : '<i class="fa-regular fa-circle text-[8px]"></i>'
                        }

                        ${item.kategori}

                    </span>

                </div>
                <!-- Card Body -->
                <h4 class="font-bold text-slate-800 text-base line-clamp-2 min-h-[3rem]" title="${item.nama}">
                    ${item.nama}
                </h4>

                <p class="text-xs text-slate-400 mt-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-map-pin text-slate-300 text-sm"></i>
                    <span class="truncate" title="${item.desa_kelurahan}, Kec. ${item.kecamatan}">
                        ${item.desa_kelurahan} ${item.kecamatan !== "-" ? `• Kec. ${item.kecamatan}` : ""}
                    </span>
                </p>

                <!-- KPI Mini-Score -->
                <div class="mt-4 flex items-center justify-between bg-slate-50 border border-slate-100 p-2.5 rounded-xl">
                    <span class="text-xs font-semibold text-slate-500">Skor IKU KPI</span>
                    <span class="text-sm font-extrabold text-theme-dark">${displaySkor}</span>
                </div>
            </div>

            <!-- Card Footer (Mini-Stats) -->
            <div class="mt-5 pt-4 border-t border-slate-100 grid grid-cols-3 gap-2 text-center text-slate-500">
                <div class="flex flex-col items-center justify-center" title="Total Pengunjung">
                    <i class="fa-solid fa-users text-slate-400 text-xs mb-1"></i>
                    <span class="text-xs font-bold text-slate-800">${item.stats.total_pengunjung.toLocaleString("id-ID")}</span>
                </div>
                <div class="flex flex-col items-center justify-center" title="Jumlah Kegiatan Pelibatan">
                    <i class="fa-solid fa-calendar-check text-slate-400 text-xs mb-1"></i>
                    <span class="text-xs font-bold text-slate-800">${item.stats.total_kegiatan}</span>
                </div>
                <div class="flex flex-col items-center justify-center" title="Laporan Publikasi Media">
                    <i class="fa-solid fa-newspaper text-slate-400 text-xs mb-1"></i>
                    <span class="text-xs font-bold text-slate-800">${item.stats.total_publikasi}</span>
                </div>
            </div>
        `;

            return div;
        }

        // Render tombol-tombol pagination
        function renderPaginationButtons() {
            const container = document.getElementById("pagination-buttons");
            container.innerHTML = "";

            const totalPages = Math.ceil(filteredLibraries.length / CARDS_PER_PAGE);
            if (totalPages <= 1) {
                container.classList.add("hidden");
                return;
            }
            container.classList.remove("hidden");

            // Tombol Prev
            const btnPrev = document.createElement("button");
            btnPrev.className = `p-2 border border-slate-200 rounded-lg text-xs font-bold transition flex items-center justify-center ${currentPage === 1 ? "text-slate-300 cursor-not-allowed" : "text-slate-600 hover:bg-slate-100"}`;
            btnPrev.innerHTML = `<i class="fa-solid fa-chevron-left mr-1"></i> Prev`;
            btnPrev.disabled = currentPage === 1;
            btnPrev.addEventListener("click", () => {
                if (currentPage > 1) {
                    currentPage--;
                    updateUI();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });
            container.appendChild(btnPrev);

            // Angka halaman
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                const btnPage = document.createElement("button");
                btnPage.className = `h-8 w-8 rounded-lg text-xs font-bold transition border ${i === currentPage ? "bg-theme-green border-theme-green text-white" : "border-slate-200 text-slate-600 hover:bg-slate-100"}`;
                btnPage.innerText = i;
                btnPage.addEventListener("click", () => {
                    currentPage = i;
                    updateUI();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
                container.appendChild(btnPage);
            }

            // Tombol Next
            const btnNext = document.createElement("button");
            btnNext.className = `p-2 border border-slate-200 rounded-lg text-xs font-bold transition flex items-center justify-center ${currentPage === totalPages ? "text-slate-300 cursor-not-allowed" : "text-slate-600 hover:bg-slate-100"}`;
            btnNext.innerHTML = `Next <i class="fa-solid fa-chevron-right ml-1"></i>`;
            btnNext.disabled = currentPage === totalPages;
            btnNext.addEventListener("click", () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    updateUI();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });
            container.appendChild(btnNext);
        }

        // ==========================================
        // LOGIKA MODAL DETAIL (EKSKLUSIF FULLSCREEN)
        // ==========================================

        window.openDetailModal = (key) => {
            const item = libraryDict[key];
            if (!item) return;

            // 1. Tampilkan Judul & Subtitle
            document.getElementById("modal-perpus-title").innerText = item.nama;

            let formattedLoc = `${item.desa_kelurahan}`;
            if (item.kecamatan && item.kecamatan !== "-") {
                formattedLoc += `, Kec. ${item.kecamatan}`;
            }
            formattedLoc += `, ${item.kabupaten_kota}, ${item.provinsi}`;
            document.getElementById("modal-perpus-subtitle").innerText = formattedLoc;

            // 2. Set Kategori Badge
            const modalBadge = document.getElementById("modal-perpus-badge");
            modalBadge.innerText = item.kategori;

            // Hapus class warna sebelumnya
            modalBadge.className = "px-3 py-1 rounded-full text-xs font-bold shadow-sm ";
            if (item.kategori === "Tinggi") modalBadge.className += "bg-emerald-100 text-emerald-700";
            else if (item.kategori === "Sedang") modalBadge.className += "bg-amber-100 text-amber-700";
            else if (item.kategori === "Rendah") modalBadge.className += "bg-red-100 text-red-700";
            else modalBadge.className += "bg-slate-100 text-slate-600";

            // 3. Set Profile Details
            document.getElementById("modal-prof-provinsi").innerText = item.provinsi;
            document.getElementById("modal-prof-kabupaten").innerText = item.kabupaten_kota;
            document.getElementById("modal-prof-desa").innerText = item.desa_kelurahan;

            // 4. Set KPI Scores
            const scoreVal = typeof item.skor === "number" ? item.skor.toFixed(1) : "-";
            document.getElementById("modal-kpi-score").innerText = scoreVal;

            const kpiCategoryBadge = document.getElementById("modal-kpi-category");
            kpiCategoryBadge.innerText = item.kategori;
            kpiCategoryBadge.className = "mt-2 px-3 py-1 rounded-full text-xs font-bold ";
            if (item.kategori === "Tinggi") kpiCategoryBadge.className += "bg-emerald-50 text-emerald-700 border border-emerald-100";
            else if (item.kategori === "Sedang") kpiCategoryBadge.className += "bg-amber-50 text-amber-700 border border-amber-100";
            else if (item.kategori === "Rendah") kpiCategoryBadge.className += "bg-red-50 text-red-700 border border-red-100";
            else kpiCategoryBadge.className += "bg-slate-100 text-slate-500 border border-slate-200";

            // 5. Set Stats Overview Cards
            document.getElementById("modal-stat-pengunjung").innerText = item.stats.total_pengunjung.toLocaleString("id-ID");
            document.getElementById("modal-stat-kegiatan").innerText = item.stats.total_kegiatan.toLocaleString("id-ID");
            document.getElementById("modal-stat-publikasi").innerText = item.stats.total_publikasi.toLocaleString("id-ID");
            document.getElementById("modal-stat-komputer").innerText = `${item.stats.total_komputer} Unit / ${item.stats.total_buku.toLocaleString("id-ID")} Buku`;

            // 6. Populasikan Tab-Tab Data
            populatePengunjungTab(item);
            populatePelibatanTab(item);
            populatePublikasiTab(item);
            populateFasilitasTab(item);

            // 7. Reset Tab aktif ke 'pengunjung' pertama kali dibuka
            switchModalTab("pengunjung");

            // 8. Tampilkan modal dengan menghapus kelas 'hidden'
            const modal = document.getElementById("perpus-detail-modal");
            modal.classList.remove("hidden");
            document.body.classList.add("overflow-hidden"); // Kunci scroll halaman utama
        };

        window.closeDetailModal = () => {
            const modal = document.getElementById("perpus-detail-modal");
            modal.classList.add("hidden");
            document.body.classList.remove("overflow-hidden"); // Buka scroll halaman utama
        };

        // Fungsi Switch Tab di Modal
        window.switchModalTab = (tabName) => {
            // Daftar nama tab yang ada
            const tabs = ["pengunjung", "pelibatan", "publikasi", "fasilitas"];

            tabs.forEach(tab => {
                const btn = document.getElementById(`tab-btn-${tab}`);
                const pane = document.getElementById(`tab-content-${tab}`);

                if (tab === tabName) {
                    // Aktif
                    btn.className = "border-b-2 border-theme-green pb-3 pt-2 text-theme-green font-bold text-sm flex items-center gap-2 shrink-0 transition";
                    pane.classList.remove("hidden");
                } else {
                    // Non-aktif
                    btn.className = "border-b-2 border-transparent pb-3 pt-2 text-slate-500 hover:text-slate-800 font-semibold text-sm flex items-center gap-2 shrink-0 transition";
                    pane.classList.add("hidden");
                }
            });
        };

        // Populasi Tab Pengunjung & Gambar Grafik Chart
        function populatePengunjungTab(item) {
            const tbody = document.querySelector("#modal-table-pengunjung tbody");
            tbody.innerHTML = "";

            if (item.pengunjung.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center py-6 text-slate-400 text-xs italic">Tidak ada laporan data kunjungan pengunjung.</td></tr>`;
                // Kosongkan chart
                if (modalChart) {
                    modalChart.destroy();
                    modalChart = null;
                }
                // Kosongkan kanvas
                const canvas = document.getElementById("modal-pengunjung-chart");
                const ctx = canvas.getContext("2d");
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                return;
            }

            // Urutkan data pengunjung berdasarkan periode (Januari ke Desember jika format periodenya 'Jan 2026')
            // Untuk data ini karena langsung berurutan, kita asumsikan urut, tapi bisa disorting jika perlu.
            const sortedPengunjung = [...item.pengunjung];

            sortedPengunjung.forEach(p => {
                const tr = document.createElement("tr");
                tr.className = "border-b border-slate-100 hover:bg-slate-50/50";
                tr.innerHTML = `
                <td class="px-4 py-3 font-semibold text-slate-700">${p.periode}</td>
                <td class="px-4 py-3 text-right">${(parseInt(p.pengunjung_laki) || 0).toLocaleString("id-ID")}</td>
                <td class="px-4 py-3 text-right">${(parseInt(p.pengunjung_perempuan) || 0).toLocaleString("id-ID")}</td>
                <td class="px-4 py-3 text-right font-bold text-theme-dark">${(parseInt(p.total_pengunjung) || 0).toLocaleString("id-ID")}</td>
            `;
                tbody.appendChild(tr);
            });

            // Gambar Grafik Chart.js
            renderPengunjungChart(sortedPengunjung);
        }

        // Fungsi menggambar Chart Kunjungan
        function renderPengunjungChart(data) {
            if (modalChart) {
                modalChart.destroy();
            }

            const ctx = document.getElementById("modal-pengunjung-chart").getContext("2d");

            // Ekstrak label & dataset
            const labels = data.map(p => p.periode);
            const dataTotal = data.map(p => parseInt(p.total_pengunjung) || 0);
            const dataLaki = data.map(p => parseInt(p.pengunjung_laki) || 0);
            const dataPerempuan = data.map(p => parseInt(p.pengunjung_perempuan) || 0);

            modalChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Total Pengunjung',
                            data: dataTotal,
                            borderColor: '#647d68', // Sage green
                            backgroundColor: 'rgba(100, 125, 104, 0.08)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#647d68',
                            pointRadius: 3
                        },
                        {
                            label: 'Laki-Laki',
                            data: dataLaki,
                            borderColor: '#3b82f6', // Biru
                            backgroundColor: 'transparent',
                            borderWidth: 1.5,
                            fill: false,
                            tension: 0.3,
                            pointRadius: 2,
                            hidden: true // sembunyikan default agar bersih
                        },
                        {
                            label: 'Perempuan',
                            data: dataPerempuan,
                            borderColor: '#ec4899', // Pink
                            backgroundColor: 'transparent',
                            borderWidth: 1.5,
                            fill: false,
                            tension: 0.3,
                            pointRadius: 2,
                            hidden: true // sembunyikan default
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                font: {
                                    size: 10,
                                    family: 'Inter'
                                }
                            }
                        },
                        tooltip: {
                            padding: 10,
                            bodyFont: {
                                family: 'Inter',
                                size: 11
                            },
                            titleFont: {
                                family: 'Inter',
                                size: 11,
                                weight: 'bold'
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'Inter',
                                    size: 9
                                }
                            }
                        },
                        y: {
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                font: {
                                    family: 'Inter',
                                    size: 9
                                }
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Populasi Tab Pelibatan Masyarakat
        function populatePelibatanTab(item) {
            const tbody = document.querySelector("#modal-table-pelibatan tbody");
            tbody.innerHTML = "";

            document.getElementById("modal-pelibatan-count-badge").innerText = `${item.pelibatan.length} Kegiatan`;

            if (item.pelibatan.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-6 text-slate-400 text-xs italic">Tidak ada laporan kegiatan pelibatan masyarakat.</td></tr>`;
                return;
            }

            // Urutkan kegiatan berdasarkan tanggal terbaru
            const sortedPelibatan = [...item.pelibatan].sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));

            sortedPelibatan.forEach(k => {
                const tr = document.createElement("tr");
                tr.className = "border-b border-slate-100 hover:bg-slate-50/50";

                // Format tanggal lokal id-ID
                let displayDate = k.tanggal;
                try {
                    displayDate = new Date(k.tanggal).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                } catch (e) {}

                // Badge status verifikasi
                let verifyBadge = `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-50 text-slate-500">Belum Verif</span>`;
                if (k.is_verified === "Sudah Diverifikasi") {
                    verifyBadge = `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center gap-1 w-fit mx-auto"><i class="fa-solid fa-circle-check text-[9px]"></i> Verifikasi</span>`;
                }

                tr.innerHTML = `
                <td class="px-4 py-3 text-slate-500 text-xs whitespace-nowrap">${displayDate}</td>
                <td class="px-4 py-3 font-semibold text-slate-700 min-w-[200px] max-w-xs truncate" title="${k.nama_kegiatan}">${k.nama_kegiatan}</td>
                <td class="px-4 py-3 text-slate-600">
                    <span class="text-xs px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200/50 font-medium">${k.bidang_kegiatan || 'Umum'}</span>
                    <p class="text-[10px] text-slate-400 mt-1">${k.jenis || 'Promosi'}</p>
                </td>
                <td class="px-4 py-3 text-xs text-slate-500">${k.sasaran || 'Umum'}</td>
                <td class="px-4 py-3 text-right font-bold text-slate-700">${(parseInt(k.jumlah_peserta) || 0).toLocaleString("id-ID")}</td>
                <td class="px-4 py-3 text-center">${verifyBadge}</td>
            `;
                tbody.appendChild(tr);
            });
        }

        function populatePublikasiTab(item) {
            const tbody = document.querySelector("#modal-table-publikasi tbody");
            tbody.innerHTML = "";

            document.getElementById("modal-publikasi-count-badge").innerText = `${item.publikasi.length} Publikasi`;

            if (item.publikasi.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-6 text-slate-400 text-xs italic">Tidak ada laporan publikasi media.</td></tr>`;
                return;
            }

            const sortedPublikasi = [...item.publikasi].sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));

            sortedPublikasi.forEach(p => {
                const tr = document.createElement("tr");
                tr.className = "border-b border-slate-100 hover:bg-slate-50/50";

                let displayDate = p.tanggal;
                try {
                    displayDate = new Date(p.tanggal).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                } catch (e) {}

                let linkHTML = `<span class="text-slate-400 text-xs">-</span>`;
                if (p.link && p.link !== "-" && p.link.startsWith("http")) {
                    linkHTML = `<a href="${p.link}" target="_blank" class="inline-flex items-center text-xs text-theme-green hover:underline font-semibold gap-1"><i class="fa-solid fa-arrow-up-right-from-square"></i> Kunjungi Link</a>`;
                }

                let verifyBadge = `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-50 text-slate-500">Belum Verif</span>`;
                if (p.is_verified === "Sudah Diverifikasi") {
                    verifyBadge = `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center gap-1 w-fit mx-auto"><i class="fa-solid fa-circle-check text-[9px]"></i> Verifikasi</span>`;
                }

                tr.innerHTML = `
                <td class="px-4 py-3 text-slate-500 text-xs whitespace-nowrap">${displayDate}</td>
                <td class="px-4 py-3 font-semibold text-slate-700 min-w-[200px] max-w-xs truncate" title="${p.judul}">${p.judul}</td>
                <td class="px-4 py-3 text-slate-600">
                    <span class="text-xs px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200/50 font-medium">${p.jenis_publikasi || 'Online'}</span>
                    <p class="text-[10px] text-slate-400 mt-1">${p.nama_media || 'Website'}</p>
                </td>
                <td class="px-4 py-3 text-slate-500">${linkHTML}</td>
                <td class="px-4 py-3 text-center">${verifyBadge}</td>
            `;
                tbody.appendChild(tr);
            });
        }

        function populateFasilitasTab(item) {
            const tbody = document.querySelector("#modal-table-fasilitas tbody");
            tbody.innerHTML = "";

            document.getElementById("modal-fasilitas-count-badge").innerText = `${item.peningkatan.length} Laporan`;

            if (item.peningkatan.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-6 text-slate-400 text-xs italic">Tidak ada laporan peningkatan fasilitas.</td></tr>`;
                return;
            }

            const sortedFasilitas = [...item.peningkatan].sort((a, b) => new Date(b.tanggal_input) - new Date(a.tanggal_input));

            sortedFasilitas.forEach(f => {
                const tr = document.createElement("tr");
                tr.className = "border-b border-slate-100 hover:bg-slate-50/50";

                let verifyBadge = `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-50 text-slate-500">Belum Verif</span>`;
                if (f.is_verified === "Sudah Diverifikasi") {
                    verifyBadge = `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center gap-1 w-fit mx-auto"><i class="fa-solid fa-circle-check text-[9px]"></i> Verifikasi</span>`;
                }

                tr.innerHTML = `
                <td class="px-4 py-3 font-semibold text-slate-700 whitespace-nowrap">${f.bulan} ${f.tahun}</td>
                <td class="px-4 py-3 text-right font-bold text-slate-700">${(parseInt(f.jumlah_buku) || 0).toLocaleString("id-ID")}</td>
                <td class="px-4 py-3 text-right text-slate-600">${(parseInt(f.jumlah_buku_digital) || 0).toLocaleString("id-ID")}</td>
                <td class="px-4 py-3 text-right font-bold text-blue-600">${(parseInt(f.jumlah_komputer) || 0)} Unit</td>
                <td class="px-4 py-3 text-right text-slate-600 font-semibold">${f.bandwidth || 0} Mbps</td>
                <td class="px-4 py-3 text-center">${verifyBadge}</td>
            `;
                tbody.appendChild(tr);
            });
        }
    });
</script>
