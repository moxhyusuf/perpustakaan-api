<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>{{ $title }} - Monitoring Perpustakaan Kabupaten Probolinggo</title>
<meta name="description" content="Data kunjungan perpustakaan Kabupaten Probolinggo berbasis data API Perpusnas." />
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    theme: {
                        green: "#647d68",
                        light: "#e4ebe5",
                        dark: "#415143",
                    },
                },
                boxShadow: {
                    soft: "0 10px 30px rgba(15, 23, 42, 0.08)",
                },
            },
        },
    };
</script>
<link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">

<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- jQuery & DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" />
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

@include('css.style')
