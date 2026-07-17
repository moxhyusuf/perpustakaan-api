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
        color: #0f172a !important;
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

    .hero-title {
        margin-bottom: 5px;
    }

   .hero-tagline{
    display: flex;
    align-items: flex-start;
    gap: 10px;

    margin-top: 2px;
    margin-bottom: 24px;

    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(13px, 1.5vw, 17px);
    font-style: italic;
    font-weight: 600;
    line-height: 1.6;

    color: #4f6b54;
    letter-spacing: .3px;
    }

    .hero-tagline span{
        display:block;
        flex:1;
    }

    .hero-tagline i{
        color:#6d8f73;
        font-size:17px;
        margin-top:4px;
        flex-shrink:0;
    }
    
    .metric-card {
        height: 165px;
    }

    .metric-card>div {
        height: 100%;
    }

    .metric-card .min-w-0 {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .metric-card .min-w-0 p:first-child {
        min-height: 40px;
        line-height: 1.35;
    }

    .metric-card .min-w-0 p:last-child {
        margin-top: auto;
    }

    /* ===========================
   PROFESSIONAL MAP
=========================== */

    #map-perpustakaan{
        height:520px;
        width:100%;
        border-radius:20px;
        overflow:hidden;

        border:1px solid #e5e7eb;

        box-shadow:
            0 8px 25px rgba(0,0,0,.08);
    }

    /* zoom button */

    .leaflet-control-zoom{

        border:none !important;

        box-shadow:
            0 6px 18px rgba(0,0,0,.15)!important;

        border-radius:14px!important;

        overflow:hidden;
    }

    .leaflet-control-zoom a{

        width:40px;
        height:40px;

        line-height:40px;

        font-size:18px;

        background:#fff!important;
    }

    .leaflet-control-zoom a:hover{

        background:#f3f4f6!important;
    }
    .map-wrapper{
        position:relative;
        overflow:hidden;
    }

    /* ===========================
    MAP LEGEND
    =========================== */

    .map-legend{

        position:absolute;

        right:20px;

        bottom:20px;

        background:#ffffff;

        padding:14px 16px;

        border-radius:14px;

        border:1px solid #e5e7eb;

        box-shadow:0 8px 25px rgba(0,0,0,.12);

        z-index:10;

        min-width:180px;

    }

    .legend-title{

        font-size:14px;

        font-weight:700;

        color:#1e293b;

        margin-bottom:10px;

    }

    .legend-item{

        display:flex;

        align-items:center;

        gap:10px;

        margin:8px 0;

        font-size:13px;

        color:#475569;

    }

    .legend-dot{

        width:14px;

        height:14px;

        border-radius:50%;

    }

    .legend-green{

        background:#22c55e;
    }

    .legend-orange{

        background:#f97316;
    }

    .legend-red{

        background:#ef4444;
    }

    .legend-blue{

        background:#3b82f6;
    }

    /* popup */

    .leaflet-popup-content-wrapper{

        border-radius:18px;

        box-shadow:
            0 12px 30px rgba(0,0,0,.18);

        border:none;
    }

    .leaflet-popup-tip{

        box-shadow:none;
    }
    .map-popup{
    width:240px;
    font-family:'Poppins',sans-serif;
    }

    .popup-title{
        font-size:15px;
        font-weight:700;
        color:#1e293b;
        margin-bottom:10px;
    }

    .popup-divider{
        height:1px;
        background:#e2e8f0;
        margin:10px 0;
    }

    .popup-item{
        display:flex;
        justify-content:space-between;
        margin:8px 0;
        font-size:13px;
        color:#475569;
    }

    .popup-btn{
        width:100%;
        border:none;
        background:#16a34a;
        color:white;
        border-radius:8px;
        padding:8px;
        cursor:pointer;
        font-weight:600;
    }

    .popup-btn:hover{
        background:#15803d;
    }
    .popup-btn-map,
    .popup-btn-detail{
        display:block;
        width:100%;
        text-align:center;
        padding:10px;
        margin-top:8px;
        border-radius:8px;
        text-decoration:none;
        font-size:13px;
        font-weight:600;
        transition:.2s;
    }

    .popup-btn-map{
        background:#2563eb;
        color:black;
    }

    .popup-btn-map:hover{
        background:#1d4ed8;
    }

    .popup-btn-detail{
        background:#16a34a;
        color:white;
        border:none;
        cursor:pointer;
    }

    .popup-btn-detail:hover{
        background:#15803d;
    }

    .popup-btn-detail:disabled{
        background:#94a3b8;
        cursor:not-allowed;
    }
    /* ===========================
   RESPONSIVE MAP LEGEND
    =========================== */

    @media (max-width: 768px){

        .map-legend{

            right:10px;

            bottom:10px;

            min-width:120px;

            padding:8px 10px;

            border-radius:10px;

        }

        .legend-title{

            font-size:12px;

            margin-bottom:6px;

        }

        .legend-item{

            font-size:11px;

            gap:6px;

            margin:5px 0;

        }

        .legend-dot{

            width:10px;

            height:10px;

        }

    }
</style>
