<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UltraClarity Demo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-950 text-white antialiased">
    <main class="min-h-screen">
        <section class="mx-auto grid min-h-screen max-w-7xl content-center gap-10 px-6 py-12 lg:grid-cols-[1fr_.9fr]">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-cyan-300">UltraClarity for Laravel</p>
                <h1 class="mt-5 text-5xl font-extrabold leading-tight sm:text-6xl">Session replay, heatmaps, and AI analytics in one installable package.</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-neutral-300">This demo page already includes the Blade directive, so clicks, scroll depth, movement traces, and custom events flow into the dashboard.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('ultraclarity.dashboard') }}" class="rounded-md bg-cyan-300 px-5 py-3 text-sm font-bold text-neutral-950">Open dashboard</a>
                    <button onclick="window.UltraClarity && window.UltraClarity.event('demo_cta_clicked', {label: 'Hero CTA'})" class="rounded-md border border-white/20 px-5 py-3 text-sm font-bold">Track custom event</button>
                </div>
                <p class="mt-5 text-sm text-neutral-400">Dashboard basic-auth demo user: admin@ultraclarity.test / password</p>
            </div>

            <div class="rounded-lg border border-white/10 bg-white/[0.04] p-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach(['Click heatmaps', 'Scroll depth', 'Session replay', 'AI hotspots', 'CSV exports', 'GDPR opt-out'] as $feature)
                        <div class="rounded-md bg-neutral-900 p-4">
                            <p class="font-semibold">{{ $feature }}</p>
                            <p class="mt-2 text-sm text-neutral-400">Production-ready package surface with Laravel-native routes, config, queues, cache, and migrations.</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>
    @ultraclarity
</body>
</html>
