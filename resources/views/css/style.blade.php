<style>
    @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap");

    :root {
        --theme-green: #647d68;
        --theme-green-dark: #415143;
        --theme-green-light: #e4ebe5;
        --theme-bg: #f4f6f4;
        --theme-border: #ccd6cd;
    }

    * {
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        margin: 0;
        font-family: "Inter", sans-serif;
        background:
            radial-gradient(circle at top left,
                rgba(100, 125, 104, 0.12),
                transparent 28%),
            radial-gradient(circle at bottom right,
                rgba(170, 184, 171, 0.35),
                transparent 24%),
            var(--theme-bg);
        color: #334155;
    }

    .text-theme-green {
        color: var(--theme-green);
    }

    .bg-theme-green {
        background-color: var(--theme-green);
    }

    .border-theme-green {
        border-color: var(--theme-green);
    }

    .bg-theme-light {
        background-color: var(--theme-green-light);
    }

    .text-theme-dark {
        color: var(--theme-green-dark);
    }

    .dashboard-shell {
        width: 100%;
        min-height: 100vh;
    }

    .hero-panel {
        background:
            linear-gradient(135deg,
                rgba(255, 255, 255, 0.96) 0%,
                rgba(255, 255, 255, 0.92) 34%,
                rgba(255, 255, 255, 0.55) 58%,
                rgba(228, 235, 229, 0.95) 100%),
            radial-gradient(circle at 78% 26%,
                rgba(100, 125, 104, 0.2),
                transparent 22%),
            radial-gradient(circle at 70% 82%,
                rgba(100, 125, 104, 0.1),
                transparent 18%),
            linear-gradient(120deg, #fdfefd 0%, #e4ebe5 45%, #ccd6cd 100%);
    }

    .hero-ornament::before,
    .hero-ornament::after {
        content: "";
        position: absolute;
        border-radius: 999px;
        background: rgba(100, 125, 104, 0.08);
        filter: blur(1px);
    }

    .hero-ornament::before {
        width: 220px;
        height: 220px;
        right: 6%;
        top: 12%;
    }

    .hero-ornament::after {
        width: 140px;
        height: 140px;
        right: 20%;
        bottom: 10%;
    }

    .hero-building {
        position: absolute;
        right: 3.5rem;
        bottom: 2.5rem;
        width: min(34vw, 360px);
        min-width: 240px;
        border: 1px solid rgba(100, 125, 104, 0.14);
        border-radius: 28px 28px 20px 20px;
        background: linear-gradient(180deg, #fffefc 0%, #f5f0e8 100%);
        box-shadow: 0 18px 40px rgba(65, 81, 67, 0.14);
        overflow: hidden;
    }

    .hero-building-roof {
        height: 22px;
        background: linear-gradient(90deg, #85715a, #9f8a72);
    }

    .hero-building-sign {
        padding: 0.7rem 1rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
        text-align: center;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.26em;
        color: #705f4f;
    }

    .hero-building-windows {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.75rem;
        padding: 1rem 1rem 1.1rem;
    }

    .hero-building-windows span {
        height: 2.7rem;
        border-radius: 0.9rem;
        background: linear-gradient(180deg, #8e785f 0%, #c7b199 100%);
        opacity: 0.85;
    }

    .hero-ground {
        position: absolute;
        right: 2.25rem;
        bottom: 1rem;
        height: 18px;
        width: min(38vw, 420px);
        border-radius: 999px;
        background: rgba(100, 125, 104, 0.18);
        filter: blur(10px);
    }

    .activity-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 4rem;
        height: 4rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, #e4ebe5, #ccd6cd);
        color: var(--theme-green);
        flex-shrink: 0;
    }

    .metric-card h3 {
        line-height: 1;
    }

    .dashboard-chart {
        height: 250px;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 999px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .hover-card {
        transition:
            transform 0.25s ease,
            box-shadow 0.25s ease,
            border-color 0.25s ease;
    }

    .hover-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.08);
        border-color: rgba(100, 125, 104, 0.16);
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    @media (max-width: 1280px) {
        .hero-building {
            width: 280px;
            right: 1.75rem;
            bottom: 1.75rem;
        }

        .hero-ground {
            width: 320px;
            right: 1.2rem;
        }

        .dashboard-chart {
            height: 220px;
        }
    }

    @media (max-width: 1024px) {

        .hero-building,
        .hero-ground {
            display: none;
        }
    }

    @media (max-width: 768px) {
        body {
            background: var(--theme-bg);
        }

        .dashboard-chart {
            height: 205px;
        }

        .activity-placeholder {
            width: 3.25rem;
            height: 3.25rem;
            border-radius: 0.85rem;
        }
    }

    @media (max-width: 640px) {
        .dashboard-chart {
            height: 190px;
        }
    }

    /* Custom DataTables Styling (Minimalist & Modern) */
    div.dt-container {
        font-family: inherit;
        font-size: 0.875rem;
        color: #475569;
    }

    table.dataTable.no-footer {
        border-bottom: none !important;
    }

    table.dataTable.display>tbody>tr>td,
    table.dataTable.display>tbody>tr>th,
    table.dataTable.row-border>tbody>tr>td,
    table.dataTable.row-border>tbody>tr>th,
    table.dataTable>tbody>tr>td,
    table.dataTable>tbody>tr>th {
        border-top: none;
        border-bottom: 1px solid #f1f5f9;
        /* border-slate-100 */
    }

    table.dataTable>thead>tr>th,
    table.dataTable>thead>tr>td {
        border-bottom: 1px solid #e2e8f0 !important;
        /* border-slate-200 */
        padding-bottom: 0.75rem;
    }

    /* Select & Search Input */
    .dt-length select,
    .dt-search input {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.5rem !important;
        padding: 0.375rem 0.75rem !important;
        outline: none;
        background-color: transparent !important;
        transition: all 0.2s;
        color: #334155 !important;
        font-size: 0.875rem;
    }

    .dt-length select:focus,
    .dt-search input:focus {
        border-color: var(--theme-green) !important;
        box-shadow: 0 0 0 1px var(--theme-green) !important;
    }

    .dt-search input {
        margin-left: 0.5rem;
    }

    /* Pagination */
    .dt-paging {
        margin-top: 1rem;
    }

    .dt-paging-button {
        border: 1px solid transparent !important;
        border-radius: 0.375rem !important;
        background: transparent !important;
        padding: 0.375rem 0.75rem !important;
        color: #64748b !important;
        transition: all 0.2s !important;
        font-size: 0.875rem;
        margin: 0 2px !important;
    }

    .dt-paging-button:hover:not(.disabled):not(.current) {
        background: #f1f5f9 !important;
        /* bg-slate-100 */
        color: #0f172a !important;
        /* text-slate-900 */
        border-color: transparent !important;
    }

    .dt-paging-button.current,
    .dt-paging-button.current:hover {
        background: var(--theme-green-light) !important;
        color: var(--theme-green-dark) !important;
        border-color: transparent !important;
        font-weight: 600 !important;
    }

    .dt-paging-button.disabled {
        color: #cbd5e1 !important;
        /* text-slate-300 */
    }

    /* Info text */
    .dt-info {
        color: #64748b !important;
        /* text-slate-500 */
        font-size: 0.8125rem !important;
        padding-top: 1.2rem !important;
    }

    /* DataTables Sorting Icons Adjustments */
    table.dataTable thead th.dt-orderable-asc,
    table.dataTable thead th.dt-orderable-desc,
    table.dataTable thead td.dt-orderable-asc,
    table.dataTable thead td.dt-orderable-desc {
        padding-right: 2rem !important;
    }
</style>
