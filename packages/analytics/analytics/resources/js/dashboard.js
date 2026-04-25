import { createApp } from 'vue';

createApp({
    data() {
        return {
            payload: window.UltraClarityDashboard || {},
            selectedHeatmap: null,
        };
    },
    computed: {
        stats() {
            return this.payload.stats || {};
        },
    },
    mounted() {
        this.selectedHeatmap = (this.payload.heatmaps || [])[0] || null;
        setInterval(this.refresh, 30000);
    },
    methods: {
        async refresh() {
            const response = await fetch('/ultraclarity/data');
            this.payload = await response.json();
        },
    },
}).mount('#ultraclarity-dashboard');

