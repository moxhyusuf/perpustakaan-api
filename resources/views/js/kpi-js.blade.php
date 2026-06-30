<script>
    // Logic untuk halaman KPI
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            // Ambil data dari API Global
            const kpiData = await ApiService.getKPI();

            if (!kpiData || kpiData.length === 0) {
                console.warn("Data KPI tidak tersedia.");
                return;
            }

            // --- 1. Hitung Ringkasan Data (Cards) ---
            const totalPerpustakaan = kpiData.length;

            // Bersihkan data skor (pastikan angka)
            const validKpiData = kpiData.map(item => ({
                ...item,
                skor: parseFloat(item.skor) || 0
            }));

            const totalSkor = validKpiData.reduce((sum, item) => sum + item.skor, 0);
            const rataRataSkor = totalPerpustakaan > 0 ? (totalSkor / totalPerpustakaan).toFixed(1) : 0;

            const maxSkor = Math.max(...validKpiData.map(item => item.skor));

            // Update DOM Cards
            document.getElementById('stat-total').innerText = totalPerpustakaan.toLocaleString('id-ID');
            document.getElementById('stat-rata').innerText = rataRataSkor;
            document.getElementById('stat-tinggi').innerText = maxSkor.toFixed(1);

            // --- 2. Siapkan Data untuk Grafik ---

            // A. Bar Chart: Top 10 Perpustakaan
            const top10Data = [...validKpiData]
                .sort((a, b) => b.skor - a.skor)
                .slice(0, 10);

            const top10Labels = top10Data.map(item => {
                // Singkat nama jika terlalu panjang
                let nama = item.nama_perpustakaan;
                if (nama.toLowerCase().startsWith('perpustakaan ')) {
                    nama = nama.substring(13);
                }
                return nama;
            });
            const top10Scores = top10Data.map(item => item.skor);

            const ctxTop = document.getElementById('chart-top').getContext('2d');
            new Chart(ctxTop, {
                type: 'bar',
                data: {
                    labels: top10Labels,
                    datasets: [{
                        label: 'Skor KPI',
                        data: top10Scores,
                        backgroundColor: '#647d68',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45,
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });

            // B. Doughnut Chart: Distribusi Skor (Disesuaikan)
            let countTinggi = 0; // >75 (Hijau)
            let countSedang = 0; // >25-75 (Kuning)
            let countRendah = 0; // <=25 (Merah)

            validKpiData.forEach(item => {
                if (item.skor > 75) countTinggi++;
                else if (item.skor > 25) countSedang++;
                else countRendah++;
            });

            const ctxDistribusi = document.getElementById('chart-distribusi').getContext('2d');
            new Chart(ctxDistribusi, {
                type: 'doughnut',
                data: {
                    labels: ['Tinggi (>75)', 'Sedang (26-75)', 'Rendah (≤25)'],
                    datasets: [{
                        data: [countTinggi, countSedang, countRendah],
                        backgroundColor: [
                            '#10b981', // emerald-500 (Hijau)
                            '#f59e0b', // amber-500 (Kuning)
                            '#ef4444' // red-500 (Merah)
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                padding: 15,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });

            // --- 3. Render DataTables ---
            const tableBody = document.querySelector('#table-kpi tbody');
            tableBody.innerHTML = ''; // Clear loading

            validKpiData.forEach((item, index) => {
                const tr = document.createElement('tr');

                // Tentukan kategori dan warna badge berdasarkan rule baru
                let kategori = '';
                let badgeClass = '';
                if (item.skor > 75) {
                    kategori = 'Tinggi';
                    badgeClass = 'bg-emerald-100 text-emerald-700 border-emerald-200';
                } else if (item.skor > 25) {
                    kategori = 'Sedang';
                    badgeClass = 'bg-amber-100 text-amber-700 border-amber-200';
                } else {
                    kategori = 'Rendah';
                    badgeClass = 'bg-red-100 text-red-700 border-red-200';
                }

                tr.innerHTML = `
                    <td class="whitespace-nowrap">${index + 1}</td>
                    <td class="font-medium text-slate-700">${item.nama_perpustakaan || '-'}</td>
                    <td>${item.desa_kelurahan || '-'}</td>
                    <td>${item.kabupaten_kota || '-'}</td>
                    <td class="font-semibold">${item.skor}</td>
                    <td>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border ${badgeClass}">
                            ${kategori}
                        </span>
                    </td>
                `;
                tableBody.appendChild(tr);
            });

            // Initialize DataTable
            $('#table-kpi').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    zeroRecords: "Tidak ada data yang ditemukan"
                },
                pageLength: 10,
                ordering: true,
                order: [
                    [4, 'desc']
                ], // Default order by Skor menurun
                columnDefs: [{
                        orderable: false,
                        targets: 0
                    } // No tidak bisa disort
                ]
            });

        } catch (error) {
            console.error("Gagal memuat data KPI:", error);
            const tableBody = document.querySelector('#table-kpi tbody');
            if (tableBody) {
                tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-red-500 py-4">Gagal memuat data. Silakan coba lagi nanti.</td></tr>`;
            }
        }
    });
</script>
