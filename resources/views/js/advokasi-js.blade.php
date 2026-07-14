<script>
    (function() {
        const rupiah = (num) =>
            new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(num || 0);

        const formatTanggal = (dateStr) => {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            if (isNaN(d)) return '-';
            return d.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
            });
        };

        const bulanLabel = (dateStr) => {
            const d = new Date(dateStr);
            if (isNaN(d)) return null;
            return d.toLocaleDateString('id-ID', {
                month: 'short',
                year: '2-digit'
            });
        };

        const hasilBadge = (hasil) => {
            const map = {
                Dana: 'bg-emerald-50 text-emerald-600',
                NonDana: 'bg-blue-50 text-blue-600',
                Regulasi: 'bg-purple-50 text-purple-600',
            };
            const cls = map[hasil] || 'bg-slate-100 text-slate-600';
            return `<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ${cls}">${hasil || '-'}</span>`;
        };

        const statusBadge = (status) => {
            const isVerified = status === 'Sudah Diverifikasi';
            const cls = isVerified ?
                'bg-emerald-50 text-emerald-600' :
                'bg-amber-50 text-amber-600';
            return `<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ${cls}">${status || '-'}</span>`;
        };

        const renderCards = (data) => {
            const totalAdvokasi = data.length;
            const totalDana = data.reduce((sum, item) => sum + (item.nominal || 0), 0);
            const totalVerified = data.filter((item) => item.is_verified === 'Sudah Diverifikasi').length;

            document.getElementById('stat-total').textContent = totalAdvokasi.toLocaleString('id-ID');
            document.getElementById('stat-dana').textContent = rupiah(totalDana);
            document.getElementById('stat-verified').textContent = totalVerified.toLocaleString('id-ID');
        };

        const renderTrenChart = (data) => {
            const grouped = {};
            data.forEach((item) => {
                const label = bulanLabel(item.tanggal);
                if (!label) return;
                grouped[label] = (grouped[label] || 0) + 1;
            });

            const sorted = Object.keys(grouped).sort((a, b) => new Date('01 ' + a) - new Date('01 ' + b));
            const labels = sorted;
            const values = sorted.map((k) => grouped[k]);

            const ctx = document.getElementById('chart-tren');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Jumlah Advokasi',
                        data: values,
                        backgroundColor: '#16a34a',
                        borderRadius: 6,
                        maxBarThickness: 36,
                    }, ],
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
                            ticks: {
                                precision: 0
                            }
                        },
                    },
                },
            });
        };

        const renderHasilChart = (data) => {
            const counts = {
                Dana: 0,
                NonDana: 0,
                Regulasi: 0
            };
            data.forEach((item) => {
                if (counts[item.hasil] !== undefined) {
                    counts[item.hasil]++;
                } else {
                    counts[item.hasil] = (counts[item.hasil] || 0) + 1;
                }
            });

            const labels = Object.keys(counts);
            const values = Object.values(counts);

            const ctx = document.getElementById('chart-hasil');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: ['#10b981', '#3b82f6', '#a855f7'],
                        borderWidth: 0,
                    }, ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                    },
                },
            });
        };

        const renderTable = (data) => {
            const tbody = document.getElementById('table-body');

            if (!data || data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                            Tidak ada data advokasi.
                        </td>
                    </tr>`;
                return;
            }

            const sorted = [...data].sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));

            tbody.innerHTML = sorted
                .map((item) => {
                    const sasaran = Array.isArray(item.sasaran) ? item.sasaran.join(', ') : (item.sasaran || '-');
                    const lokasi = [item.kecamatan_name, item.desa_name]
                        .filter((v) => v && v.trim() !== '-' && v.trim() !== '')
                        .join(', ');

                    const attachmentLink = item.attachment && item.attachment !== '-' ?
                        `<a href="${item.attachment}" target="_blank" class="mt-1 inline-flex items-center gap-1 text-xs text-theme-green hover:underline"><i class="fa-solid fa-paperclip"></i> Lampiran</a>` :
                        '';

                    return `
                        <tr class="hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3 align-top">${formatTanggal(item.tanggal)}</td>
                            <td class="px-4 py-3 align-top">
                                <p class="font-medium text-slate-700 max-w-md">${item.judul || '-'}</p>
                                ${attachmentLink}
                            </td>
                            <td class="px-4 py-3 align-top">
                                <p class="font-medium text-slate-700">${item.perpus_nama || '-'}</p>
                                ${lokasi ? `<p class="text-xs text-slate-400">${lokasi}</p>` : ''}
                            </td>
                            <td class="px-4 py-3 align-top">${sasaran}</td>
                            <td class="px-4 py-3 align-top">${hasilBadge(item.hasil)}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right align-top">${item.nominal ? rupiah(item.nominal) : '-'}</td>
                            <td class="px-4 py-3 text-center align-top">${statusBadge(item.is_verified)}</td>
                        </tr>`;
                })
                .join('');
        };

        const initDataTable = () => {
            if (!window.jQuery || !window.jQuery.fn.DataTable) return;

            if (window.jQuery.fn.dataTable.isDataTable('#tabel-advokasi')) {
                window.jQuery('#tabel-advokasi').DataTable().destroy();
            }

            window.jQuery('#tabel-advokasi').DataTable({
                order: [],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                columnDefs: [{
                    orderable: false,
                    targets: [1, 3]
                }, ],
                language: {
                    search: '',
                    searchPlaceholder: 'Cari data advokasi...',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_-_END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(disaring dari _MAX_ total data)',
                    zeroRecords: 'Data tidak ditemukan',
                    paginate: {
                        first: 'Awal',
                        last: 'Akhir',
                        next: 'Selanjutnya',
                        previous: 'Sebelumnya',
                    },
                },
            });
        };

        const init = async () => {
            const data = await window.ApiService.getAdvokasi();

            if (!data) {
                document.getElementById('table-body').innerHTML = `
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-red-500">
                            Gagal memuat data advokasi.
                        </td>
                    </tr>`;
                return;
            }

            renderCards(data);
            renderTrenChart(data);
            renderHasilChart(data);
            renderTable(data);
            initDataTable();
        };

        document.addEventListener('DOMContentLoaded', init);
    })();
</script>
