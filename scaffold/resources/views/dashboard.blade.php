<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Laravel + Vue Deployment Demo</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 0 16px; }
        .card { border: 1px solid #ddd; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        button { padding: 10px 14px; border: 0; border-radius: 8px; background: #2563eb; color: white; cursor: pointer; }
        .muted { color: #666; }
    </style>
</head>
<body>
<div id="app" class="card">
    <h1>Laravel + Vue Dynamic Demo</h1>
    <p class="muted">Use this page to verify dynamic behavior after deploying to Render.</p>

    <p><strong>Total tracked visits:</strong> @{{ stats.visits }}</p>
    <p><strong>Last action:</strong> @{{ stats.last_action }}</p>
    <p><strong>Updated at:</strong> @{{ stats.updated_at }}</p>

    <button @click="track('button-click')">Track Button Click</button>
</div>

<script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                stats: { visits: 0, last_action: 'loading', updated_at: '-' }
            }
        },
        async mounted() {
            await this.refresh();
            await this.track('page-load');
        },
        methods: {
            async refresh() {
                const response = await fetch('/api/stats');
                this.stats = await response.json();
            },
            async track(action) {
                const response = await fetch('/api/track-visit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ action })
                });

                this.stats = await response.json();
            }
        }
    }).mount('#app');
</script>
</body>
</html>
