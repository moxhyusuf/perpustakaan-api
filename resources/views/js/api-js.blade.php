<script>
    const API_BASE_URL = "{{ asset('json') }}/";

    const ApiService = {
        async fetchData(filename) {
            try {
                const response = await fetch(`${API_BASE_URL}${filename}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();

                return result.data;
            } catch (error) {
                console.error(`Error fetching ${filename}:`, error);
                return null;
            }
        },

        getPengunjung: () => ApiService.fetchData('pengunjung3513.json'),
        getPelibatan: () => ApiService.fetchData('pelibatan3513.json'),
        getPublikasi: () => ApiService.fetchData('publikasi3513.json'),
        getReplikasi: () => ApiService.fetchData('replikasi3513.json'),
        getKPI: () => ApiService.fetchData('iku3513.json'),
        getFasilitas: () => ApiService.fetchData('peningkatan3513.json')
    };

    window.ApiService = ApiService;
</script>
