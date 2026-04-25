<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PrismPath - Analytics Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
@php($ultraClarityViteManifest = public_path('build/manifest.json'))
@if (file_exists($ultraClarityViteManifest) && str_contains(file_get_contents($ultraClarityViteManifest), 'resources/css/app.css'))
@vite(['resources/css/app.css'])
@endif
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={theme:{extend:{fontFamily:{sans:['Poppins','ui-sans-serif']}}}};</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<style>
*,*::before,*::after{box-sizing:border-box}
html{background:#f8fafc;scroll-behavior:smooth}
body{color:#333;font-family:'Poppins',sans-serif}
[v-cloak]{display:none}
/* Scrollbar */
.uc-s{overflow:auto;overscroll-behavior:contain}
.uc-s::-webkit-scrollbar{width:5px;height:5px}
.uc-s::-webkit-scrollbar-track{background:transparent}
.uc-s::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:999px}
.uc-s::-webkit-scrollbar-thumb:hover{background:#94a3b8}
/* Chart containers */
.uc-chart{height:272px;width:100%;min-width:0;position:relative;overflow:hidden;contain:layout paint}
.uc-chart canvas{width:100%!important;height:272px!important;display:block}
.uc-chart-sm{height:200px}
.uc-chart-sm canvas{height:200px!important}
/* Inputs */
.uf{border:1px solid #e2e8f0;background:#fff;color:#334155;border-radius:.5rem;font-size:.8rem;padding:.45rem .7rem;min-height:2.25rem;font-family:'Poppins',sans-serif;transition:border-color .15s,box-shadow .15s;outline:none;width:100%}
.uf:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.12)}
/* Buttons */
.ub{display:inline-flex;align-items:center;gap:.3rem;border:1px solid #e2e8f0;background:#fff;color:#475569;border-radius:.5rem;padding:.4rem .8rem;font-size:.75rem;font-weight:600;font-family:'Poppins',sans-serif;cursor:pointer;transition:all .15s ease;white-space:nowrap;text-decoration:none}
.ub:hover{border-color:#3b82f6;color:#2563eb;box-shadow:0 4px 12px rgba(59,130,246,.1)}
.ub:disabled{opacity:.5;cursor:default}
.up{border-color:#3b82f6;background:#3b82f6;color:#fff}
.up:hover{background:#2563eb;border-color:#2563eb;color:#fff;box-shadow:0 4px 14px rgba(59,130,246,.3)}
.ut{border-color:#14b8a6;background:#14b8a6;color:#fff}
.ut:hover{background:#0d9488;border-color:#0d9488;color:#fff}
.us{padding:.3rem .6rem;font-size:.7rem}
/* Cards */
.uc{background:#fff;border:1px solid #e8edf5;border-radius:.875rem;padding:1.25rem;box-shadow:0 1px 2px rgba(15,23,42,.04),0 10px 28px rgba(15,23,42,.045);transition:border-color .18s,box-shadow .18s,transform .18s}
.uc:hover{border-color:#bfdbfe;box-shadow:0 14px 34px rgba(15,23,42,.075);transform:translateY(-1px)}
.uc-panel-head{display:flex;align-items:center;justify-content:space-between;gap:.75rem;margin-bottom:.75rem}
.uc-panel-actions{display:flex;align-items:center;gap:.35rem;flex-wrap:wrap}
.uc-topbar{backdrop-filter:saturate(160%) blur(12px)}
.uc-page-bg{background:linear-gradient(180deg,#fff 0,#f8fafc 190px,#f9fafb 100%)}
/* Metric accent bars */
.ma-b{border-left:3px solid #3b82f6}
.ma-t{border-left:3px solid #14b8a6}
.ma-v{border-left:3px solid #8b5cf6}
.ma-a{border-left:3px solid #f59e0b}
.ma-r{border-left:3px solid #f43f5e}
.ma-g{border-left:3px solid #10b981}
.ma-o{border-left:3px solid #f97316}
.ma-p{border-left:3px solid #a855f7}
/* Badges */
.bd{display:inline-flex;align-items:center;font-size:.65rem;font-weight:700;padding:.12rem .42rem;border-radius:999px}
.bd-b{background:#eff6ff;color:#2563eb}
.bd-t{background:#f0fdfa;color:#0d9488}
.bd-g{background:#f0fdf4;color:#16a34a}
.bd-a{background:#fffbeb;color:#d97706}
.bd-r{background:#fff1f2;color:#e11d48}
.bd-s{background:#f8fafc;color:#475569;border:1px solid #e2e8f0}
/* Live pulse */
.pulse{display:inline-block;width:8px;height:8px;border-radius:50%;background:#10b981;box-shadow:0 0 0 0 rgba(16,185,129,.4);animation:pl 2s infinite;flex-shrink:0}
@keyframes pl{0%{box-shadow:0 0 0 0 rgba(16,185,129,.4)}70%{box-shadow:0 0 0 8px rgba(16,185,129,0)}100%{box-shadow:0 0 0 0 rgba(16,185,129,0)}}
/* Progress */
.pr{height:5px;border-radius:999px;background:#f1f5f9;overflow:hidden}
.prb{height:100%;border-radius:999px;background:linear-gradient(90deg,#3b82f6,#14b8a6);transition:width .5s ease}
/* Table */
.tb{width:100%;border-collapse:collapse;font-size:.78rem}
.tb th{background:#f8fafc;color:#64748b;font-weight:700;font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;padding:.6rem .75rem;text-align:left;position:sticky;top:0;z-index:1}
.tb td{padding:.6rem .75rem;border-bottom:1px solid #f1f5f9;color:#334155}
.tb tr:hover td{background:#f8fafc}
.tb tr:last-child td{border-bottom:none}
.tb-wrap{max-height:420px;overflow:auto;border:1px solid #e2e8f0;border-radius:.875rem;background:#fff}
/* Code */
.co{background:#0f172a;color:#e2e8f0;padding:.9rem 1.1rem;border-radius:.75rem;font-family:'Courier New',monospace;font-size:.75rem;line-height:1.75;overflow-x:auto;white-space:pre}
.ci{background:#f1f5f9;color:#1e293b;padding:.1rem .35rem;border-radius:.3rem;font-family:monospace;font-size:.8em}
/* Nav active */
.na{background:#eff6ff;color:#2563eb}
.ni{background:#dbeafe;color:#2563eb}
/* Fade in */
.fd{animation:fa .2s ease}
@keyframes fa{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}
/* Sidebar mobile */
@media(max-width:1023px){
  .sb-hide{position:fixed;z-index:50;height:100vh;top:0;left:0;transform:translateX(-100%);transition:transform .25s ease}
  .sb-show{transform:translateX(0)}
}
@media(min-width:1024px){
  .sb-hide,.sb-show{position:sticky;top:0;transform:none;height:100vh}
}
</style>
</head>
<body class="uc-page-bg antialiased">

<div id="app" v-cloak>
  <!-- Mobile overlay -->
  <div v-if="sidebarOpen" @click="sidebarOpen=false" class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden"></div>

  <div class="min-h-screen lg:flex">

    <!-- Sidebar -->
    <aside :class="['bg-white border-r border-slate-200 w-64 flex-shrink-0 flex flex-col sb-hide', sidebarOpen ? 'sb-show' : '']" style="min-height:100vh">
      <!-- Brand -->
      <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-teal-400 flex items-center justify-center text-white font-extrabold text-xs shadow-sm flex-shrink-0">UC</div>
        <div>
          <p class="font-extrabold text-slate-900 text-sm leading-tight">PrismPath</p>
          <p class="text-[11px] text-slate-400 font-medium leading-tight">Analytics Dashboard</p>
        </div>
        <button @click="sidebarOpen=false" class="ml-auto text-slate-400 hover:text-slate-700 lg:hidden">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <!-- Live status -->
      <div class="px-5 py-2.5 border-b border-slate-100 flex items-center gap-2">
        <span class="pulse"></span>
        <span class="text-xs font-semibold text-slate-600"><span v-text="payload.stats?.live||0"></span> live now</span>
        <span class="ml-auto text-[11px] font-medium" :class="autoRefresh?'text-emerald-500':'text-slate-400'" v-text="autoRefresh?'Live':'Paused'"></span>
      </div>
      <!-- Nav -->
      <nav class="flex-1 uc-s px-3 py-3 space-y-0.5">
        <button v-for="item in nav" :key="item.key" @click="navigate(item.key)"
          :class="['w-full flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-semibold transition text-left', active===item.key?'na':'text-slate-600 hover:bg-slate-50 hover:text-slate-900']">
          <span :class="['w-7 h-7 flex items-center justify-center rounded-lg transition', active===item.key?'ni':'bg-slate-100 text-slate-400']">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path :d="item.icon"/></svg>
          </span>
          <span class="flex-1" v-text="item.label"></span>
          <span v-if="item.key==='live'&&(payload.stats?.live||0)>0" class="bd bd-g" v-text="payload.stats.live"></span>
          <span v-if="item.key==='docs'" class="bd bd-b">New</span>
        </button>
      </nav>
      <!-- Footer -->
      <div class="px-5 py-4 border-t border-slate-100">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-teal-400 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">A</div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-bold text-slate-800 truncate">Admin</p>
            <p class="text-[11px] text-slate-400">Analytics Owner</p>
          </div>
          <button @click="autoRefresh=!autoRefresh" :title="autoRefresh?'Pause live updates':'Resume live updates'"
            :class="['w-7 h-7 rounded-lg flex items-center justify-center border transition',autoRefresh?'bg-emerald-50 border-emerald-200 text-emerald-600':'bg-slate-50 border-slate-200 text-slate-400']">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main area -->
    <div class="flex-1 min-w-0 flex flex-col">

      <!-- Top nav bar -->
      <header class="uc-topbar sticky top-0 z-30 bg-white/92 border-b border-slate-200 shadow-sm">
        <div class="flex items-center gap-3 px-4 py-3">
          <button @click="sidebarOpen=true" class="ub us lg:hidden">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          </button>
          <div class="hidden sm:flex items-center gap-1.5 text-xs text-slate-400 font-medium">
            <span>Dashboard</span>
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span class="text-slate-700 font-semibold" v-text="activeLabel"></span>
          </div>
          <!-- Search -->
          <div class="flex-1 max-w-md mx-4 relative">
            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input v-model="globalSearch" @keyup.enter="runGlobalSearch" type="search" placeholder="Search sessions, pages, events..." class="uf pl-9">
          </div>
          <!-- Date quick picks -->
          <div class="hidden md:flex items-center gap-1">
            <button v-for="r in dateRanges" :key="r.d" @click="applyRange(r.d)"
              :class="['ub us', activeDays===r.d?'up':'']" v-text="r.l"></button>
          </div>
          <!-- Filter toggle -->
          <button @click="filtersOpen=!filtersOpen" :class="['ub', filtersOpen?'border-blue-200 bg-blue-50 text-blue-700':'']">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            Filters
            <span v-if="activeFilterCount>0" class="bd bd-b" v-text="activeFilterCount"></span>
          </button>
          <!-- Export -->
          <a :href="exportUrl('json')" class="ub up hidden sm:inline-flex">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export
          </a>
          <!-- Bell -->
          <button class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:border-blue-200 hover:text-blue-600 transition relative flex-shrink-0">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">3</span>
          </button>
          <!-- Avatar -->
          <div class="relative flex-shrink-0">
          <button @click="profileOpen=!profileOpen" class="flex items-center gap-2 border border-slate-200 rounded-xl px-2.5 py-1.5 bg-white cursor-pointer hover:border-blue-200 transition">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-teal-400 flex items-center justify-center text-white text-[11px] font-extrabold">UC</div>
            <div class="hidden sm:block">
              <p class="text-xs font-bold text-slate-800 leading-tight">Admin</p>
              <p class="text-[10px] text-slate-400">Owner</p>
            </div>
            <svg class="hidden h-3.5 w-3.5 text-slate-400 sm:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div v-if="profileOpen" class="absolute right-0 mt-2 w-64 rounded-xl border border-slate-200 bg-white p-3 shadow-lg">
            <p class="text-xs font-extrabold text-slate-900">PrismPath Admin</p>
            <p class="mt-1 text-[11px] text-slate-500">Dashboard protected by PrismPath middleware.</p>
            <div class="mt-3 grid gap-2">
              <button @click="active='docs'; activeDoc='config'; profileOpen=false" class="ub us justify-start">Configuration</button>
              <button @click="active='exports'; profileOpen=false" class="ub us justify-start">Exports & Reports</button>
              <button @click="autoRefresh=!autoRefresh" class="ub us justify-start" v-text="autoRefresh?'Pause live updates':'Resume live updates'"></button>
            </div>
          </div>
          </div>
        </div>
      </header>

      <!-- Filter panel -->
      <div v-show="filtersOpen" class="bg-white border-b border-slate-200 px-4 py-3 shadow-sm">
        <div class="max-w-7xl mx-auto">
          <div class="flex items-center justify-between mb-2.5">
            <div class="flex items-center gap-2">
              <p class="text-xs font-bold text-slate-900">Segmentation Filters</p>
              <span v-if="activeFilterCount>0" class="bd bd-b" v-text="activeFilterCount+' active'"></span>
            </div>
            <div class="flex items-center gap-2">
              <span v-for="l in activeFilterLabels" :key="l" class="bd bd-s" v-text="l"></span>
              <button v-if="activeFilterCount>0" @click="clearFilters" class="ub us text-rose-600 border-rose-200 hover:bg-rose-50">Clear all</button>
            </div>
          </div>
          <div class="grid grid-cols-2 sm:grid-cols-5 xl:grid-cols-10 gap-2">
            <input v-model="filters.from" @change="refresh" type="date" class="uf text-xs">
            <input v-model="filters.to" @change="refresh" type="date" class="uf text-xs">
            <select v-model="filters.hour" @change="refresh" class="uf text-xs"><option value="">Any hour</option><option v-for="h in 24" :key="h-1" :value="h-1" v-text="String(h-1).padStart(2,'0')+':00'"></option></select>
            <select v-model="filters.page" @change="refresh" class="uf text-xs"><option value="">All pages</option><option v-for="p in options.pages" :key="p" :value="p" v-text="p"></option></select>
            <select v-model="filters.device" @change="refresh" class="uf text-xs"><option value="">All devices</option><option v-for="d in options.devices" :key="d" :value="d" v-text="d"></option></select>
            <select v-model="filters.browser" @change="refresh" class="uf text-xs"><option value="">All browsers</option><option v-for="b in options.browsers" :key="b" :value="b" v-text="b"></option></select>
            <select v-model="filters.os" @change="refresh" class="uf text-xs"><option value="">All OS</option><option v-for="o in options.systems" :key="o" :value="o" v-text="o"></option></select>
            <select v-model="filters.country" @change="refresh" class="uf text-xs"><option value="">All countries</option><option v-for="c in options.countries" :key="c" :value="c" v-text="c"></option></select>
            <select v-model="filters.city" @change="refresh" class="uf text-xs"><option value="">All cities</option><option v-for="c in options.cities" :key="c" :value="c" v-text="c"></option></select>
            <select v-model="filters.authenticated" @change="refresh" class="uf text-xs"><option value="">All visitors</option><option value="1">Logged in</option><option value="0">Guests</option></select>
          </div>
        </div>
      </div>

      <!-- Metric cards strip -->
      <div class="bg-white border-b border-slate-100 px-4 py-3">
        <div class="max-w-7xl mx-auto">
          <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2.5">
            <div v-for="c in metricCards" :key="c.label" :class="['uc p-3', c.acc]" style="transition:none">
              <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400" v-text="c.label"></p>
              <p class="text-xl font-extrabold mt-1 leading-tight" :class="c.col" v-text="c.val"></p>
              <p v-if="c.sub" class="text-[10px] text-slate-400 mt-0.5" v-text="c.sub"></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Content -->
      <main class="flex-1 px-4 py-5">
        <div class="max-w-7xl mx-auto space-y-5">

          <!-- OVERVIEW -->
          <div v-show="active==='overview'" class="fd">
            <div class="grid gap-5 xl:grid-cols-2">
              <div class="uc">
                <div class="uc-panel-head">
                  <h3 class="font-bold text-sm text-slate-900">Traffic Overview</h3>
                  <div class="uc-panel-actions">
                    <span class="bd bd-b">Views</span><span class="bd bd-t">Sessions</span><span class="bd bd-a">Dur.</span>
                    <a :href="exportUrl('json')" class="ub us">JSON</a>
                  </div>
                </div>
                <div class="uc-chart"><canvas id="trendChart"></canvas></div>
              </div>
              <div class="uc">
                <div class="uc-panel-head">
                  <h3 class="font-bold text-sm text-slate-900">Pages, Clicks & Scroll</h3>
                  <a :href="exportUrl('pageviews')" class="ub us">CSV</a>
                </div>
                <div class="uc-chart"><canvas id="pagesChart"></canvas></div>
              </div>
              <div class="uc">
                <h3 class="font-bold text-sm text-slate-900 mb-3">Device Distribution</h3>
                <div class="grid grid-cols-[1fr_auto] gap-4 items-center">
                  <div class="uc-chart uc-chart-sm"><canvas id="devicesChart"></canvas></div>
                  <div class="space-y-2">
                    <div v-for="(seg,i) in payload.segments?.devices||[]" :key="seg.device" class="flex items-center gap-2 text-xs">
                      <span class="w-2.5 h-2.5 rounded-sm flex-shrink-0" :style="{background:dColors[i%dColors.length]}"></span>
                      <span class="text-slate-600" v-text="seg.device"></span>
                      <span class="ml-2 font-bold text-slate-800" v-text="seg.total"></span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="uc">
                <div class="uc-panel-head">
                  <h3 class="font-bold text-sm text-slate-900">Browser & OS</h3>
                  <span class="bd bd-s">Segment</span>
                </div>
                <div class="uc-chart uc-chart-sm"><canvas id="systemsChart"></canvas></div>
              </div>
            </div>
            <div class="uc mt-5">
              <div class="uc-panel-head">
                <h3 class="font-bold text-sm text-slate-900">Top Pages</h3>
                <div class="uc-panel-actions">
                  <a :href="exportUrl('pageviews')" class="ub us">Export CSV</a>
                  <button @click="active='pages'" class="ub us">View all</button>
                </div>
              </div>
              <div class="tb-wrap">
                <table class="tb">
                  <thead><tr><th>Page</th><th>Views</th><th>Avg Time</th><th>Scroll</th><th>Exit %</th><th>Clicks</th></tr></thead>
                  <tbody>
                    <tr v-for="p in (payload.topPages||[]).slice(0,6)" :key="p.path">
                      <td class="font-semibold text-slate-800 max-w-xs truncate" v-text="p.path"></td>
                      <td><span class="bd bd-b" v-text="p.views"></span></td>
                      <td v-text="p.duration+'s'"></td>
                      <td>
                        <div class="flex items-center gap-2">
                          <div class="pr w-14"><div class="prb" :style="{width:p.scroll+'%'}"></div></div>
                          <span v-text="p.scroll+'%'"></span>
                        </div>
                      </td>
                      <td v-text="p.exit_rate+'%'"></td>
                      <td v-text="p.clicks"></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- LIVE USERS -->
          <div v-show="active==='live'" class="fd">
            <div class="grid gap-5 xl:grid-cols-[1fr_1.25fr]">
              <div class="uc">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <span class="pulse"></span>
                    <h3 class="font-bold text-sm text-slate-900">Active Users</h3>
                    <span class="bd bd-g" v-text="liveUsers.length"></span>
                  </div>
                  <input v-model="liveSearch" placeholder="Search..." class="uf text-xs w-36">
                </div>
                <div class="uc-s space-y-2.5 max-h-[580px] pr-1">
                  <article v-for="u in filteredLive" :key="u.id" @click="selectLive(u)"
                    :class="['rounded-xl border p-3 cursor-pointer transition',selectedUser?.id===u.id?'border-blue-300 bg-blue-50':'border-slate-200 bg-white hover:border-blue-200 hover:bg-slate-50']">
                    <div class="flex items-start gap-2.5">
                      <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-400 to-teal-400 flex items-center justify-center text-white font-bold text-[11px] flex-shrink-0"
                        v-text="(u.visitor_uuid||'U').slice(0,2).toUpperCase()"></div>
                      <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 justify-between">
                          <p class="font-semibold text-slate-900 text-xs truncate" v-text="u.current_path||'/'"></p>
                          <span :class="['bd flex-shrink-0',aBadge(u).c]" v-text="aBadge(u).l"></span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5 truncate" v-text="[u.city,u.country].filter(Boolean).join(', ')"></p>
                        <p class="text-[11px] text-slate-400 truncate" v-text="[u.device,u.browser,u.os].filter(Boolean).join(' / ')"></p>
                      </div>
                    </div>
                    <div class="grid grid-cols-4 gap-1.5 mt-2.5">
                      <div class="text-center rounded-lg bg-slate-50 py-1.5"><p class="font-bold text-slate-800 text-sm" v-text="u.clicks||0"></p><p class="text-[10px] text-slate-400">clicks</p></div>
                      <div class="text-center rounded-lg bg-slate-50 py-1.5"><p class="font-bold text-slate-800 text-sm" v-text="(u.scroll||0)+'%'"></p><p class="text-[10px] text-slate-400">scroll</p></div>
                      <div class="text-center rounded-lg bg-slate-50 py-1.5"><p class="font-bold text-slate-800 text-sm" v-text="(u.duration||0)+'s'"></p><p class="text-[10px] text-slate-400">dur</p></div>
                      <div class="text-center rounded-lg bg-slate-50 py-1.5"><p class="font-bold text-slate-800 text-sm" v-text="u.movements||0"></p><p class="text-[10px] text-slate-400">moves</p></div>
                    </div>
                    <div class="mt-2 flex gap-1.5">
                      <button @click.stop="loadReplay(u.id)" class="ub up us flex-1 justify-center">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>Replay
                      </button>
                      <button @click.stop="selectLive(u)" class="ub us flex-1 justify-center">Timeline</button>
                    </div>
                  </article>
                  <p v-if="!filteredLive.length" class="text-sm text-slate-500 text-center py-12">No active users in the last 30 minutes.</p>
                </div>
              </div>

              <!-- Timeline -->
              <div class="uc">
                <h3 class="font-bold text-sm text-slate-900 mb-1">
                  Session Timeline
                  <span v-if="selectedUser" class="ml-2 text-xs font-normal text-slate-400" v-text="'- '+(selectedUser.visitor_uuid||'').slice(0,8)"></span>
                </h3>
                <p class="text-[11px] text-slate-400 mb-3">Horizontal scroll / hover for details</p>
                <div class="uc-s overflow-x-auto pb-3" style="max-height:560px">
                  <div v-if="selectedTimeline&&selectedTimeline.length" class="flex items-end gap-3 min-w-max p-2">
                    <div v-for="(pg,idx) in selectedTimeline" :key="pg.id||idx"
                      class="group relative flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition hover:border-blue-300 hover:shadow-md cursor-pointer"
                      :style="{height:Math.max(140,Math.min(420,(pg.duration||10)*2.2))+'px',minWidth:'170px'}">
                      <div>
                        <p class="font-bold text-slate-900 text-xs truncate" v-text="pg.path"></p>
                        <p class="text-[11px] text-slate-400 mt-0.5" v-text="pg.viewed_at?pg.viewed_at.slice(0,16):'live'"></p>
                        <div class="mt-1.5 flex flex-wrap gap-1">
                          <span v-if="pg.clicks" class="bd bd-b" v-text="pg.clicks+' clicks'"></span>
                          <span v-if="pg.scroll" class="bd bd-t" v-text="pg.scroll+'% scroll'"></span>
                        </div>
                      </div>
                      <div class="text-[11px] text-slate-500 space-y-0.5">
                        <p v-text="(pg.duration||0)+'s on page'"></p>
                        <p v-text="(pg.moves||0)+' moves'"></p>
                      </div>
                      <!-- Tooltip -->
                      <div class="pointer-events-none absolute left-0 right-0 -top-2 -translate-y-full hidden group-hover:block z-10 px-1">
                        <div class="rounded-xl border border-slate-200 bg-white p-3 text-xs shadow-lg">
                          <p class="font-bold text-slate-900" v-text="pg.title||pg.path"></p>
                          <p class="mt-1 text-slate-500" v-text="'Events: '+(pg.events||0)"></p>
                          <p class="text-slate-500" v-text="'Source: '+(pg.source||'direct')"></p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="flex flex-col items-center justify-center py-16 text-center">
                    <svg class="w-10 h-10 text-slate-200 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="13" y2="12"/><line x1="7" y1="16" x2="17" y2="16"/></svg>
                    <p class="text-sm font-semibold text-slate-400">Select a user to see their timeline</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- SESSIONS & REPLAY -->
          <div v-show="active==='sessions'" class="fd">
            <div class="grid gap-5 xl:grid-cols-[1fr_1.1fr]">
              <!-- Replay -->
              <div class="uc">
                <div class="uc-panel-head">
                  <h3 class="font-bold text-sm text-slate-900">Session Replay</h3>
                  <a :href="exportUrl('sessions')" class="ub us">Export sessions</a>
                </div>
                <div v-if="replay">
                  <div class="relative rounded-xl border border-slate-200 bg-slate-50 overflow-hidden" style="height:320px">
                    <div class="absolute inset-0 bg-[linear-gradient(rgba(59,130,246,.05)_1px,transparent_1px),linear-gradient(90deg,rgba(59,130,246,.05)_1px,transparent_1px)] bg-[size:36px_36px]"></div>
                    <div v-for="(ev,i) in replayFrameEvents" :key="i"
                      :class="['absolute w-4 h-4 -translate-x-1/2 -translate-y-1/2 rounded-full transition-all duration-150',ev.type==='click'?'bg-blue-500 shadow-lg shadow-blue-300/60':'bg-teal-400/70']"
                      :style="evStyle(ev)"></div>
                    <div class="absolute bottom-3 left-3 right-3 bg-white/95 rounded-xl border border-slate-200 px-3 py-2 shadow-sm">
                      <div class="flex items-center gap-2 text-xs text-slate-600">
                        <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span v-text="replay.ai?.summary||'Analyzing...'"></span>
                      </div>
                      <div v-if="replay.ai?.signals" class="mt-1.5 flex gap-1.5 flex-wrap">
                        <span v-if="replay.ai.signals.rage_click_clusters>0" class="bd bd-r">Rage clicks</span>
                        <span v-if="replay.ai.signals.high_engagement" class="bd bd-g">High engagement</span>
                        <span v-if="replay.ai.signals.deep_scroll" class="bd bd-b">Deep scroll</span>
                      </div>
                    </div>
                  </div>
                  <div class="mt-3 flex flex-wrap items-center gap-2">
                    <button @click="playing=!playing" class="ub up">
                      <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                        <path v-if="playing" d="M6 4h4v16H6V4Zm8 0h4v16h-4V4Z"/>
                        <polygon v-else points="5 3 19 12 5 21 5 3"/>
                      </svg>
                      <span v-text="playing?'Pause':'Play'"></span>
                    </button>
                    <button v-for="sp in [0.5,1,2,4]" :key="sp" @click="speed=sp"
                      :class="['ub us',speed===sp?'border-teal-300 bg-teal-50 text-teal-700':'']"
                      v-text="sp+'x'"></button>
                    <button @click="speed=4" class="ub us">Fast-forward</button>
                    <button @click="speed=0.5" class="ub us">Slow-motion</button>
                    <button @click="skipIdle" class="ub us">Skip idle</button>
                    <input v-model.number="replayTime" type="range" min="0" :max="replayMax" class="flex-1 min-w-24 accent-blue-600">
                    <span class="text-xs text-slate-400 font-mono w-10 text-right" v-text="Math.round(replayTime/1000)+'s'"></span>
                  </div>
                  <div class="uc-s overflow-x-auto mt-3 flex gap-1.5 pb-1">
                    <button v-for="pg in replay.pageTimeline" :key="pg.id" @click="jumpToPage(pg)"
                      class="ub us min-w-max" v-text="pg.sequence+'. '+pg.path"></button>
                  </div>
                </div>
                <div v-else class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center py-16 text-center">
                  <svg class="w-10 h-10 text-slate-300 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                  <p class="text-sm font-semibold text-slate-500">Select a session to replay</p>
                  <p class="text-xs text-slate-400 mt-1">Click Play on any session in the table.</p>
                </div>
              </div>
              <!-- Sessions table -->
              <session-table :sessions="payload.sessions" @play="loadReplay"></session-table>
            </div>
          </div>

          <!-- PAGE VIEWS -->
          <div v-show="active==='pages'" class="fd">
            <div class="grid gap-5 xl:grid-cols-[1.2fr_.8fr]">
              <div class="uc">
                <div class="uc-panel-head">
                  <h3 class="font-bold text-sm text-slate-900">Page View Trends</h3>
                  <div class="uc-panel-actions">
                    <a :href="exportUrl('json')" class="ub us">JSON</a>
                    <a :href="exportUrl('pageviews')" class="ub us">CSV</a>
                  </div>
                </div>
                <div class="uc-chart"><canvas id="pagesPanelChart"></canvas></div>
              </div>
              <div class="uc">
                <div class="uc-panel-head">
                  <h3 class="font-bold text-sm text-slate-900">Top Pages</h3>
                  <a :href="exportUrl('pageviews')" class="ub us">Export</a>
                </div>
                <div class="uc-s space-y-2 max-h-80 pr-1">
                  <div v-for="p in payload.topPages" :key="p.path" class="rounded-xl border border-slate-200 bg-white p-3 hover:border-blue-200 transition">
                    <div class="flex items-center justify-between gap-2">
                      <span class="text-xs font-semibold text-slate-800 truncate" v-text="p.path"></span>
                      <span class="bd bd-b flex-shrink-0" v-text="p.views+' views'"></span>
                    </div>
                    <div class="mt-1.5 pr"><div class="prb" :style="{width:Math.min(100,p.views/(payload.topPages?.[0]?.views||1)*100)+'%'}"></div></div>
                    <div class="mt-1.5 flex gap-3 text-[11px] text-slate-500">
                      <span v-text="p.duration+'s avg'"></span>
                      <span v-text="p.exit_rate+'% exit'"></span>
                      <span v-text="p.scroll+'% scroll'"></span>
                      <span class="ml-auto" v-text="p.clicks+' clicks'"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- CLICKS & SCROLLS -->
          <div v-show="active==='behavior'" class="fd">
            <div class="grid gap-5 xl:grid-cols-2">
              <div class="uc">
                <div class="uc-panel-head">
                  <h3 class="font-bold text-sm text-slate-900">Top Clicked Elements</h3>
                  <a :href="exportUrl('clicks')" class="ub us">Export</a>
                </div>
                <div class="uc-s space-y-2 max-h-[420px] pr-1">
                  <div v-for="(el,i) in payload.elements" :key="el.selector"
                    class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2.5 hover:border-blue-200 transition">
                    <span class="text-xs font-bold text-slate-400 w-5 flex-shrink-0 text-center" v-text="i+1"></span>
                    <span class="flex-1 text-xs font-mono text-slate-700 truncate" v-text="el.selector"></span>
                    <div class="flex items-center gap-2">
                      <div class="pr w-20"><div class="prb" :style="{width:Math.min(100,el.clicks/(payload.elements?.[0]?.clicks||1)*100)+'%'}"></div></div>
                      <span class="font-bold text-teal-700 text-sm w-10 text-right" v-text="el.clicks"></span>
                    </div>
                  </div>
                  <p v-if="!payload.elements?.length" class="text-sm text-slate-500 text-center py-8">No click data yet.</p>
                </div>
              </div>
              <div class="uc">
                <div class="uc-panel-head">
                  <h3 class="font-bold text-sm text-slate-900">Clicks & Scroll by Page</h3>
                  <a :href="exportUrl('clicks')" class="ub us">CSV</a>
                </div>
                <div class="uc-chart"><canvas id="behaviorChart"></canvas></div>
              </div>
            </div>
          </div>

          <!-- HEATMAPS -->
          <div v-show="active==='heatmaps'" class="fd">
            <div class="uc">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                  <h3 class="font-bold text-sm text-slate-900">AI Heatmap Overlay</h3>
                  <p class="text-xs text-slate-400 mt-0.5">Click, scroll, and movement density with AI hotspot detection</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                  <div class="flex rounded-lg border border-slate-200 overflow-hidden">
                    <button v-for="t in heatTypes" :key="t.v" @click="heatFilter=t.v"
                      :class="['px-3 py-1.5 text-xs font-semibold transition border-r last:border-r-0 border-slate-200',heatFilter===t.v?'bg-blue-600 text-white':'bg-white text-slate-600 hover:bg-slate-50']"
                      v-text="t.l"></button>
                  </div>
                  <select v-model="selectedHeatId" class="uf text-xs" style="width:auto">
                    <option v-for="m in filteredHeats" :key="m.id" :value="m.id" v-text="m.path+' / '+m.type"></option>
                  </select>
                  <a :href="exportUrl('heatmaps')" class="ub us">Export CSV</a>
                </div>
              </div>
              <div class="relative rounded-xl border border-slate-200 bg-white overflow-hidden" style="height:500px">
                <iframe v-if="selHeat" :src="selHeat.path" class="absolute inset-0 w-full h-full border-0 opacity-25 pointer-events-none"></iframe>
                <div class="absolute inset-0 bg-[linear-gradient(rgba(15,23,42,.04)_1px,transparent_1px),linear-gradient(90deg,rgba(15,23,42,.04)_1px,transparent_1px)] bg-[size:36px_36px]"></div>
                <div v-for="(sp,i) in selHeat?.hotspots||[]" :key="i"
                  class="absolute -translate-x-1/2 -translate-y-1/2 rounded-full blur-md transition-transform hover:scale-110 cursor-pointer"
                  :title="'Intensity: '+(sp.intensity||0)" :style="hotStyle(sp)"></div>
                <div v-if="!selHeat" class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">
                  No heatmap data. Run <span class="ci mx-1">php artisan ultraclarity:heatmaps</span>
                </div>
                <div class="absolute bottom-3 left-3 right-3 bg-white/95 rounded-xl border border-slate-200 p-3 shadow-sm">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                      <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                    </div>
                    <div class="min-w-0">
                      <p class="font-bold text-slate-900 text-sm" v-text="selHeat?.ai_insights?.summary||'No heatmap selected'"></p>
                      <div class="flex gap-3 mt-1 text-[11px] text-slate-500">
                        <span v-text="'Sample: '+(selHeat?.sample_size||0)+' events'"></span>
                        <span v-text="(selHeat?.hotspots?.length||0)+' hotspots'"></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- EVENTS -->
          <div v-show="active==='events'" class="fd">
            <div class="grid gap-5 xl:grid-cols-[.9fr_1.1fr]">
              <div class="uc">
                <div class="uc-panel-head">
                  <h3 class="font-bold text-sm text-slate-900">Conversion Funnel</h3>
                  <a :href="exportUrl('events')" class="ub us">Export</a>
                </div>
                <div class="space-y-3">
                  <div v-for="(step,i) in payload.funnels" :key="step.step">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                      <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[10px]" v-text="i+1"></span>
                        <span class="font-semibold text-slate-800" v-text="step.step"></span>
                      </div>
                      <div class="flex items-center gap-2">
                        <span class="text-slate-500" v-text="step.sessions+' sessions'"></span>
                        <span :class="['bd',step.conversion>=70?'bd-g':step.conversion>=40?'bd-a':'bd-r']" v-text="step.conversion+'%'"></span>
                      </div>
                    </div>
                    <div class="pr h-3"><div class="prb" :style="{width:Math.max(2,step.conversion)+'%'}"></div></div>
                    <div v-if="i<payload.funnels.length-1" class="flex justify-center my-1">
                      <svg class="w-3 h-3 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                  </div>
                  <p v-if="!payload.funnels?.length" class="text-sm text-slate-500 text-center py-8">Configure funnels in ultraclarity.php.</p>
                </div>
              </div>
              <div class="space-y-4">
                <div class="uc">
                  <div class="uc-panel-head">
                    <h3 class="font-bold text-sm text-slate-900">Event Frequency</h3>
                    <a :href="exportUrl('events')" class="ub us">CSV</a>
                  </div>
                  <div class="uc-s space-y-2 max-h-52 pr-1">
                    <div v-for="ev in payload.events?.frequency||[]" :key="ev.name"
                      class="flex items-center gap-3 rounded-lg border border-slate-100 px-3 py-2 hover:bg-slate-50">
                      <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-700 truncate" v-text="ev.name"></p>
                        <div class="mt-1 pr"><div class="prb" :style="{width:Math.min(100,ev.total/(payload.events?.frequency?.[0]?.total||1)*100)+'%'}"></div></div>
                      </div>
                      <span class="font-bold text-teal-700 text-sm flex-shrink-0" v-text="ev.total"></span>
                    </div>
                    <p v-if="!payload.events?.frequency?.length" class="text-sm text-slate-500 text-center py-4">No custom events yet.</p>
                  </div>
                </div>
                <div class="uc">
                  <div class="uc-panel-head">
                    <h3 class="font-bold text-sm text-slate-900">Recent Events</h3>
                    <a :href="exportUrl('events')" class="ub us">Export</a>
                  </div>
                  <div class="uc-s space-y-1.5 max-h-52 pr-1">
                    <div v-for="ev in payload.events?.recent||[]" :key="ev.id"
                      class="flex items-center gap-2 text-xs py-1.5 border-b border-slate-50 last:border-b-0">
                      <span class="bd bd-b flex-shrink-0" v-text="ev.name"></span>
                      <span class="text-slate-500 truncate" v-text="ev.path"></span>
                      <span class="ml-auto text-slate-400 flex-shrink-0" v-text="(ev.occurred_at||'').slice(0,16)"></span>
                    </div>
                    <p v-if="!payload.events?.recent?.length" class="text-sm text-slate-500 text-center py-4">No recent events.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- EXPORTS -->
          <div v-show="active==='exports'" class="fd">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              <a v-for="item in exportItems" :key="item.type" :href="exportUrl(item.type)"
                class="uc group flex flex-col gap-3 cursor-pointer hover:border-blue-300 no-underline">
                <div class="flex items-start justify-between">
                  <div :class="['w-10 h-10 rounded-xl flex items-center justify-center',item.ibg]">
                    <svg class="w-5 h-5" :class="item.ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path :d="item.ip"/></svg>
                  </div>
                  <span :class="['bd',item.bc]" v-text="item.fmt"></span>
                </div>
                <div>
                  <p class="font-bold text-slate-900 text-sm" v-text="item.label"></p>
                  <p class="text-xs text-slate-500 mt-1" v-text="item.help"></p>
                </div>
                <div class="mt-auto flex items-center gap-1 text-xs font-semibold text-blue-600 group-hover:gap-2 transition-all">
                  <span>Download</span>
                  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
              </a>
            </div>
            <div class="uc mt-5">
              <h3 class="font-bold text-sm text-slate-900 mb-4">Scheduled Reports</h3>
              <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="r in payload.reports||[]" :key="r.name" class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                  <div class="flex items-center justify-between">
                    <p class="font-semibold text-slate-800 text-sm" v-text="r.name"></p>
                    <span class="bd bd-s" v-text="(r.format||'').toUpperCase()"></span>
                  </div>
                  <p class="text-xs text-slate-500 mt-1 capitalize" v-text="r.frequency"></p>
                  <code class="block mt-2 text-[11px] bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-slate-600 font-mono">php artisan ultraclarity:report</code>
                </div>
              </div>
            </div>
          </div>

          <!-- DOCUMENTATION -->
          <div v-show="active==='docs'" class="fd">
            <div class="grid gap-5 lg:grid-cols-[200px_1fr]">
              <!-- Docs nav -->
              <div class="uc h-fit lg:sticky lg:top-24">
                <h3 class="font-bold text-sm text-slate-900 mb-3">Contents</h3>
                <nav class="space-y-0.5">
                  <button v-for="s in docSections" :key="s.id" @click="activeDoc=s.id"
                    :class="['w-full text-left text-xs px-3 py-2 rounded-lg font-semibold transition',activeDoc===s.id?'bg-blue-50 text-blue-700':'text-slate-600 hover:bg-slate-50']"
                    v-text="s.title"></button>
                </nav>
              </div>
              <!-- Docs body -->
              <div class="uc min-w-0">
                <!-- Quick Start -->
                <div v-if="activeDoc==='quickstart'">
                  <h2 class="text-xl font-extrabold text-slate-900 mb-1">Quick Start</h2>
                  <p class="text-sm text-slate-500 mb-6">Get PrismPath running in under 5 minutes.</p>
                  <div class="space-y-6">
                    <div><h3 class="font-bold text-slate-800 text-sm mb-2 flex items-center gap-2"><span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">1</span>Install the package</h3><pre class="co">composer require aetherpulse/prismpath</pre></div>
                    <div><h3 class="font-bold text-slate-800 text-sm mb-2 flex items-center gap-2"><span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">2</span>Publish config &amp; run migrations</h3><pre class="co">php artisan vendor:publish --tag=ultraclarity-config
php artisan vendor:publish --tag=ultraclarity-migrations
php artisan migrate</pre></div>
                    <div><h3 class="font-bold text-slate-800 text-sm mb-2 flex items-center gap-2"><span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">3</span>Add the tracking snippet to your layout</h3><pre class="co">&lt;!-- resources/views/layouts/app.blade.php --&gt;
&lt;body&gt;
    @yield('content')
    @@prismpath {{-- before &lt;/body&gt; --}}
&lt;/body&gt;</pre></div>
                    <div><h3 class="font-bold text-slate-800 text-sm mb-2 flex items-center gap-2"><span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">4</span>Seed demo data and visit the dashboard</h3><pre class="co">php artisan ultraclarity:seed
php artisan serve
# Open: http://127.0.0.1:8000/ultraclarity</pre></div>
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                      <p class="text-xs font-bold text-amber-800 mb-1">Default Credentials</p>
                      <p class="text-xs text-amber-700">Email: <span class="ci">admin@prismpath.test</span> &nbsp; Password: <span class="ci">password</span></p>
                      <p class="text-xs text-amber-600 mt-1.5">Override in <span class="ci">.env</span> with <span class="ci">PRISMPATH_DASHBOARD_EMAIL</span> and <span class="ci">PRISMPATH_DASHBOARD_PASSWORD</span></p>
                    </div>
                  </div>
                </div>

                <!-- Installation -->
                <div v-if="activeDoc==='installation'">
                  <h2 class="text-xl font-extrabold text-slate-900 mb-1">Installation</h2>
                  <p class="text-sm text-slate-500 mb-6">Full guide for new and existing Laravel 10+ projects.</p>
                  <div class="space-y-5">
                    <div><h3 class="font-bold text-slate-800 mb-2">New Project</h3><pre class="co">composer create-project laravel/laravel:^10.0 myapp
cd myapp
composer require aetherpulse/prismpath
cp .env.example .env && php artisan key:generate</pre></div>
                    <div><h3 class="font-bold text-slate-800 mb-2">SQLite (development)</h3><pre class="co">touch database/database.sqlite
# .env:
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite</pre></div>
                    <div><h3 class="font-bold text-slate-800 mb-2">MySQL (production)</h3><pre class="co">DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ultraclarity
DB_USERNAME=root
DB_PASSWORD=your_password</pre></div>
                    <div><h3 class="font-bold text-slate-800 mb-2">Database tables created</h3>
                      <div class="grid gap-2 sm:grid-cols-2 text-xs">
                        <div v-for="t in dbTables" :key="t.n" class="rounded-lg border border-slate-200 p-2.5">
                          <p class="font-mono font-bold text-blue-700" v-text="t.n"></p>
                          <p class="text-slate-500 mt-0.5" v-text="t.d"></p>
                        </div>
                      </div>
                    </div>
                    <div><h3 class="font-bold text-slate-800 mb-2">Existing Application</h3><pre class="co">composer require aetherpulse/prismpath
php artisan vendor:publish --tag=ultraclarity-config
php artisan vendor:publish --tag=ultraclarity-migrations
php artisan migrate</pre></div>
                  </div>
                </div>

                <!-- JS Snippet -->
                <div v-if="activeDoc==='snippet'">
                  <h2 class="text-xl font-extrabold text-slate-900 mb-1">JS Tracking Snippet</h2>
                  <p class="text-sm text-slate-500 mb-6">How the client-side tracker captures behavior.</p>
                  <div class="space-y-5">
                    <div><h3 class="font-bold text-slate-800 mb-2">Blade Directive</h3>
                      <pre class="co">@@prismpath
// renders as:
&lt;script src="/ultraclarity.js" async defer
  data-endpoint="/api/ultraclarity/collect"
  data-site="default"&gt;&lt;/script&gt;</pre></div>
                    <div><h3 class="font-bold text-slate-800 mb-3">What Gets Tracked</h3>
                      <div class="grid gap-2 sm:grid-cols-2">
                        <div v-for="f in trackerFeats" :key="f.l" class="rounded-lg border border-slate-200 p-3">
                          <p class="font-semibold text-slate-800 text-xs" v-text="f.l"></p>
                          <p class="text-[11px] text-slate-500 mt-0.5" v-text="f.d"></p>
                        </div>
                      </div>
                    </div>
                    <div><h3 class="font-bold text-slate-800 mb-2">Snippet Config</h3>
                      <pre class="co">// config/ultraclarity.php
'snippet' => [
    'async'       => true,        // Load asynchronously
    'defer'       => true,        // Execute after DOM ready
    'gdpr'        => true,        // Respect opt-out cookie/localStorage
    'sample_rate' => 1.0,         // 0.0-1.0; 1.0 = 100% of visitors
    'mask_inputs' => true,        // Hide sensitive form values
    'endpoint'    => '/api/ultraclarity/collect',
],</pre></div>
                    <div><h3 class="font-bold text-slate-800 mb-2">GDPR Opt-Out</h3>
                      <pre class="co">// User opts out - add to your privacy/cookie banner:
localStorage.setItem('ultraclarity:optout', '1');

// To re-enable:
localStorage.removeItem('ultraclarity:optout');</pre></div>
                    <div><h3 class="font-bold text-slate-800 mb-2">Reduce Tracking Volume (Sampling)</h3>
                      <pre class="co"># .env - only track 50% of visitors
ULTRACLARITY_SAMPLE_RATE=0.5</pre></div>
                  </div>
                </div>

                <!-- Custom Events -->
                <div v-if="activeDoc==='events'">
                  <h2 class="text-xl font-extrabold text-slate-900 mb-1">Custom Event Tracking</h2>
                  <p class="text-sm text-slate-500 mb-6">Track conversions, signups, purchases, and any user action.</p>
                  <div class="space-y-5">
                    <div><h3 class="font-bold text-slate-800 mb-2">JavaScript API</h3>
                      <pre class="co">// Basic event
window.PrismPath?.event('signup_started');

// With properties
window.PrismPath?.event('plan_selected', {
    plan:    'pro',
    billing: 'annual',
    price:   49,
});

// Ecommerce
window.PrismPath?.event('purchase', {
    order_id: 'ORD-123',
    total:    99.00,
    currency: 'USD',
    items:    ['Pro Plan'],
});</pre></div>
                    <div><h3 class="font-bold text-slate-800 mb-2">REST API</h3>
                      <pre class="co">POST /api/ultraclarity/event
Content-Type: application/json

{
  "name":         "checkout_completed",
  "path":         "/checkout/success",
  "session_uuid": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
  "properties":   { "order_id": "ORD-456", "total": 149 }
}</pre></div>
                    <div><h3 class="font-bold text-slate-800 mb-2">Funnel Configuration</h3>
                      <pre class="co">// config/ultraclarity.php
'funnels' => [
    'default' => [
        '/',               // page view at /
        'signup_started',  // custom event name
        'plan_selected',   // custom event name
        '/checkout',       // page view at /checkout
    ],
],

// Paths (/) match page views.
// Non-path strings match custom event names.</pre></div>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-xs text-blue-700">
                      <p class="font-bold text-blue-900 mb-1">Tip: Identify Logged-in Users</p>
                      <p>Pass <span class="ci">user_id</span> in your backend so sessions are tied to real accounts. The tracker picks it up automatically if you add it to your layout.</p>
                    </div>
                  </div>
                </div>

                <!-- Replay & Heatmaps -->
                <div v-if="activeDoc==='replay'">
                  <h2 class="text-xl font-extrabold text-slate-900 mb-1">Session Replay &amp; Heatmaps</h2>
                  <p class="text-sm text-slate-500 mb-6">Watch real user journeys and spot UX friction.</p>
                  <div class="space-y-5">
                    <div><h3 class="font-bold text-slate-800 mb-2">Using the Replay Player</h3>
                      <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <ul class="text-xs text-slate-700 space-y-1.5">
                          <li>- <strong>Play / Pause</strong> - Start or stop the recording</li>
                          <li>- <strong>Speed controls</strong> - 0.5x, 1x, 2x, 4x playback</li>
                          <li>- <strong>Skip idle</strong> - Jump past inactive gaps (&gt;1.5s)</li>
                          <li>- <strong>Scrubber bar</strong> - Drag to any moment in the session</li>
                          <li>- <strong>Page buttons</strong> - Jump directly to page changes</li>
                          <li>- <strong>AI insights</strong> - Rage-click and engagement signals appear at the bottom</li>
                        </ul>
                      </div>
                    </div>
                    <div><h3 class="font-bold text-slate-800 mb-2">Heatmap Types</h3>
                      <div class="grid gap-2 sm:grid-cols-3 text-xs">
                        <div class="rounded-lg border border-slate-200 p-3"><p class="font-bold text-slate-800">Click</p><p class="text-slate-500 mt-0.5">Where users click most. Identifies UI confusion.</p></div>
                        <div class="rounded-lg border border-slate-200 p-3"><p class="font-bold text-slate-800">Scroll</p><p class="text-slate-500 mt-0.5">How far users scroll. Reveals content fold problems.</p></div>
                        <div class="rounded-lg border border-slate-200 p-3"><p class="font-bold text-slate-800">Movement</p><p class="text-slate-500 mt-0.5">Mouse path density. Predicts reading attention.</p></div>
                      </div>
                    </div>
                    <div><h3 class="font-bold text-slate-800 mb-2">Regenerate Heatmaps</h3>
                      <pre class="co">php artisan ultraclarity:heatmaps

# Heatmaps are also computed on-the-fly when you
# apply filters in the dashboard that have no cached data.</pre></div>
                    <div><h3 class="font-bold text-slate-800 mb-2">Retention Policies</h3>
                      <pre class="co"># config/ultraclarity.php
'retention' => [
    'raw_events_days'   => 90,   // Click/scroll/move events
    'recordings_days'   => 30,   // Session replay payloads
    'aggregates_days'   => 365,  // Heatmap aggregates
],

# Enforce retention (add to scheduler):
php artisan ultraclarity:cleanup</pre></div>
                  </div>
                </div>

                <!-- Filters -->
                <div v-if="activeDoc==='filters'">
                  <h2 class="text-xl font-extrabold text-slate-900 mb-1">Filters &amp; Segmentation</h2>
                  <p class="text-sm text-slate-500 mb-6">Every chart and table responds instantly to filter changes.</p>
                  <div class="space-y-5">
                    <div><h3 class="font-bold text-slate-800 mb-3">Available Filters</h3>
                      <div class="overflow-x-auto"><table class="tb">
                        <thead><tr><th>Parameter</th><th>Example</th><th>Description</th></tr></thead>
                        <tbody>
                          <tr v-for="f in filterDocs" :key="f.p"><td class="font-mono text-blue-700" v-text="f.p"></td><td class="ci text-slate-500" v-text="f.ex"></td><td v-text="f.desc"></td></tr>
                        </tbody>
                      </table></div>
                    </div>
                    <div><h3 class="font-bold text-slate-800 mb-2">API Filtering</h3>
                      <pre class="co">GET /ultraclarity/data?from=2026-01-01&amp;to=2026-01-31&amp;device=mobile

# Combined filters
GET /ultraclarity/data?device=mobile&amp;authenticated=1&amp;country=US</pre></div>
                    <div><h3 class="font-bold text-slate-800 mb-2">Quick Date Ranges</h3>
                      <p class="text-xs text-slate-600">Use the <strong>7d / 30d / 90d</strong> buttons in the top navigation bar. These automatically set the <span class="ci">from</span> and <span class="ci">to</span> filter fields and refresh all panels.</p>
                    </div>
                  </div>
                </div>

                <!-- Exports -->
                <div v-if="activeDoc==='exports'">
                  <h2 class="text-xl font-extrabold text-slate-900 mb-1">Exports &amp; Reports</h2>
                  <p class="text-sm text-slate-500 mb-6">Download data in CSV, JSON, or PDF. Schedule automated reports.</p>
                  <div class="space-y-5">
                    <div><h3 class="font-bold text-slate-800 mb-3">Export Endpoints</h3>
                      <div class="overflow-x-auto"><table class="tb">
                        <thead><tr><th>URL</th><th>Format</th><th>Contents</th></tr></thead>
                        <tbody>
                          <tr v-for="e in exportDocs" :key="e.url">
                            <td class="font-mono text-[11px] text-blue-700" v-text="e.url"></td>
                            <td><span class="bd bd-s" v-text="e.fmt"></span></td>
                            <td class="text-slate-600" v-text="e.desc"></td>
                          </tr>
                        </tbody>
                      </table></div>
                    </div>
                    <div><h3 class="font-bold text-slate-800 mb-2">Filters Apply to Exports</h3>
                      <pre class="co"># Export only mobile visitors from the US in January
GET /ultraclarity/export/sessions?device=mobile&amp;country=US&amp;from=2026-01-01&amp;to=2026-01-31</pre></div>
                    <div><h3 class="font-bold text-slate-800 mb-2">Scheduled Reports</h3>
                      <pre class="co">// app/Console/Kernel.php
$schedule->command('ultraclarity:report')->daily();
$schedule->command('ultraclarity:report')->weekly();

# Email recipients (.env)
ULTRACLARITY_REPORT_RECIPIENTS=ceo@company.com,growth@company.com</pre></div>
                  </div>
                </div>

                <!-- Config Reference -->
                <div v-if="activeDoc==='config'">
                  <h2 class="text-xl font-extrabold text-slate-900 mb-1">Configuration Reference</h2>
                  <p class="text-sm text-slate-500 mb-6">All options in <span class="ci">config/ultraclarity.php</span> and <span class="ci">.env</span>.</p>
                  <pre class="co">&lt;?php
return [
    // Master on/off switch
    'enabled'      => env('ULTRACLARITY_ENABLED', true),

    // URL prefix - dashboard lives at /ultraclarity
    'route_prefix' => env('ULTRACLARITY_ROUTE_PREFIX', 'ultraclarity'),

    // Basic-auth for the dashboard
    'dashboard_auth' => [
        'enabled'  => env('ULTRACLARITY_DASHBOARD_AUTH', true),
        'email'    => env('PRISMPATH_DASHBOARD_EMAIL', env('ULTRACLARITY_DASHBOARD_EMAIL', 'admin@prismpath.test')),
        'password' => env('ULTRACLARITY_DASHBOARD_PASSWORD', 'password'),
    ],

    // Feature toggles
    'features' => [
        'sessions'    => env('ULTRACLARITY_SESSIONS', true),
        'heatmaps'    => env('ULTRACLARITY_HEATMAPS', true),
        'clicks'      => env('ULTRACLARITY_CLICKS', true),
        'ai_insights' => env('ULTRACLARITY_AI_INSIGHTS', true),
        'echo'        => env('ULTRACLARITY_ECHO', false),   // Laravel Echo realtime
    ],

    'privacy' => [
        'store_ip' => env('ULTRACLARITY_STORE_IP', true),  // false to anonymise
    ],

    'retention' => [
        'raw_events_days'   => env('ULTRACLARITY_RAW_RETENTION_DAYS', 90),
        'recordings_days'   => env('ULTRACLARITY_RECORDING_RETENTION_DAYS', 30),
        'aggregates_days'   => env('ULTRACLARITY_AGGREGATE_RETENTION_DAYS', 365),
    ],

    'snippet' => [
        'async'       => true,
        'defer'       => true,
        'gdpr'        => true,
        'sample_rate' => (float) env('ULTRACLARITY_SAMPLE_RATE', 1.0),
        'mask_inputs' => true,
        'endpoint'    => '/api/ultraclarity/collect',
    ],

    'cache_ttl' => env('ULTRACLARITY_CACHE_TTL', 60),  // seconds

    'funnels' => [
        'default' => ['/', 'signup_started', 'plan_selected', '/checkout'],
    ],

    'reports' => [
        'recipients' => array_filter(explode(',', env('ULTRACLARITY_REPORT_RECIPIENTS', ''))),
        'schedules'  => [
            ['name' => 'Executive daily',  'frequency' => 'daily',   'format' => 'pdf'],
            ['name' => 'Growth weekly',    'frequency' => 'weekly',  'format' => 'csv'],
            ['name' => 'Product monthly',  'frequency' => 'monthly', 'format' => 'json'],
        ],
    ],
];</pre>
                </div>

                <!-- FAQ -->
                <div v-if="activeDoc==='faq'">
                  <h2 class="text-xl font-extrabold text-slate-900 mb-1">FAQ &amp; Troubleshooting</h2>
                  <p class="text-sm text-slate-500 mb-6">Answers to common questions.</p>
                  <div class="space-y-3">
                    <div v-for="f in faqs" :key="f.q" class="rounded-xl border border-slate-200 overflow-hidden">
                      <button @click="openFaq=openFaq===f.q?null:f.q"
                        class="w-full flex items-center justify-between p-4 text-left hover:bg-slate-50 transition">
                        <span class="font-semibold text-slate-800 text-sm" v-text="f.q"></span>
                        <svg :class="['w-4 h-4 text-slate-400 transition-transform flex-shrink-0',openFaq===f.q?'rotate-180':'']"
                          viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                      </button>
                      <div v-show="openFaq===f.q" class="px-4 pb-4 text-sm text-slate-600" v-html="f.a"></div>
                    </div>
                  </div>
                </div>

              </div><!-- /docs body -->
            </div>
          </div>

        </div><!-- /max-w-7xl -->
      </main>
    </div><!-- /main area -->
  </div><!-- /lg:flex -->
</div><!-- /#app -->

<script>
window.__UC = @json($payload);
window.__UCR = {
  data:       @json(route('ultraclarity.data')),
  live:       @json(route('ultraclarity.live')),
  docs:       @json(route('ultraclarity.docs')),
  replay:     @json(url(config('ultraclarity.route_prefix').'/replay')),
  exportBase: @json(url(config('ultraclarity.route_prefix').'/export')),
};
</script>

<script>
const {createApp} = Vue;

/* SessionTable component */
const SessionTable = {
  props: ['sessions'],
  emits: ['play'],
  data() {
    return {expanded:null, search:'', sortKey:'started_at', sortDir:'desc', page:1, perPage:10};
  },
  computed: {
    rows() {
      const t = this.search.trim().toLowerCase();
      let rows = t ? (this.sessions||[]).filter(s =>
        [s.visitor?.visitor_uuid, s.visitor?.device, s.visitor?.browser, s.visitor?.os, s.visitor?.city, s.visitor?.country,
          ...(s.timeline||[]).map(p=>p.path)].filter(Boolean).join(' ').toLowerCase().includes(t)
      ) : (this.sessions||[]);
      return [...rows].sort((a,b) => {
        const av = this.sv(a), bv = this.sv(b);
        if(av===bv) return 0;
        return this.sortDir==='asc' ? (av>bv?1:-1) : (av>bv?-1:1);
      });
    },
    totalPages() { return Math.max(1, Math.ceil(this.rows.length/this.perPage)); },
    page_rows() { return this.rows.slice((this.page-1)*this.perPage, this.page*this.perPage); },
  },
  watch: { search(){this.page=1;}, sessions(){this.page=1;} },
  methods: {
    sv(s) {
      if(this.sortKey==='visitor') return s.visitor?.visitor_uuid||'';
      if(this.sortKey==='device') return [s.visitor?.device,s.visitor?.browser].filter(Boolean).join(' ');
      if(this.sortKey==='duration') return Number(s.duration_seconds||0);
      if(this.sortKey==='scroll') return Number(s.max_scroll_depth||0);
      return s.started_at||s.id||'';
    },
    sb(k) { this.sortKey===k ? this.sortDir=(this.sortDir==='asc'?'desc':'asc') : (this.sortKey=k, this.sortDir=(k==='duration'||k==='scroll'?'desc':'asc')); },
    sm(k) { return this.sortKey===k ? (this.sortDir==='asc'?' up':' down') : ''; },
  },
  template: `<div class="uc">
    <div class="flex flex-col sm:flex-row sm:items-center gap-2 justify-between mb-3">
      <h3 class="font-bold text-sm text-slate-900">Sessions &amp; Timelines</h3>
      <div class="flex items-center gap-2">
        <input v-model="search" class="uf text-xs w-40" type="search" placeholder="Search...">
        <span class="text-xs text-slate-400 font-medium whitespace-nowrap" v-text="rows.length+' sessions'"></span>
        <select v-model.number="perPage" class="uf text-xs" style="width:auto"><option :value="8">8/pg</option><option :value="10">10/pg</option><option :value="20">20/pg</option></select>
      </div>
    </div>
    <div class="overflow-auto rounded-xl border border-slate-200" style="max-height:420px">
      <table class="tb">
        <thead><tr>
          <th><button @click="sb('visitor')" class="font-bold" v-text="'Visitor'+sm('visitor')"></button></th>
          <th><button @click="sb('device')" class="font-bold" v-text="'Device'+sm('device')"></button></th>
          <th><button @click="sb('duration')" class="font-bold" v-text="'Duration'+sm('duration')"></button></th>
          <th>Timeline</th>
          <th><button @click="sb('scroll')" class="font-bold" v-text="'Scroll'+sm('scroll')"></button></th>
          <th></th>
        </tr></thead>
        <tbody>
          <template v-for="s in page_rows" :key="s.id">
            <tr>
              <td class="font-semibold text-slate-900" v-text="(s.visitor?.visitor_uuid||'visitor').slice(0,8)"></td>
              <td class="text-slate-600 truncate max-w-32" v-text="[s.visitor?.device,s.visitor?.browser].filter(Boolean).join(' / ')"></td>
              <td class="text-slate-600" v-text="(s.duration_seconds||0)+'s'"></td>
              <td>
                <div class="flex gap-1 overflow-hidden">
                  <span v-for="p in s.timeline" :key="p.id"
                    class="h-3 rounded-full bg-gradient-to-r from-blue-400 to-teal-400 flex-shrink-0"
                    :title="p.path+' / '+(p.duration||0)+'s / '+(p.clicks||0)+' clicks'"
                    :style="{width:Math.max(24,p.duration||10)+'px'}">
                  </span>
                </div>
              </td>
              <td class="text-slate-600" v-text="(s.max_scroll_depth||0)+'%'"></td>
              <td class="flex gap-1">
                <button @click="$emit('play',s.id)" class="ub up us">Play</button>
                <button @click="expanded=expanded===s.id?null:s.id" class="ub us">View</button>
              </td>
            </tr>
            <tr v-if="expanded===s.id">
              <td colspan="6" class="bg-slate-50 px-3 py-3">
                <div class="grid gap-2 sm:grid-cols-2">
                  <div v-for="p in s.timeline" :key="p.id" class="rounded-lg border border-slate-200 bg-white p-3 text-xs text-slate-600">
                    <p class="font-bold text-slate-900 truncate" v-text="p.path"></p>
                    <p class="mt-1" v-text="(p.duration||0)+'s / '+(p.clicks||0)+' clicks / '+(p.scroll||0)+'% scroll / '+(p.moves||0)+' moves'"></p>
                  </div>
                </div>
              </td>
            </tr>
          </template>
          <tr v-if="!page_rows.length"><td colspan="6" class="px-3 py-8 text-center text-sm text-slate-500">No sessions match the current search.</td></tr>
        </tbody>
      </table>
    </div>
    <div class="mt-3 flex items-center justify-between text-xs font-semibold text-slate-500">
      <span v-text="'Page '+page+' of '+totalPages"></span>
      <div class="flex gap-2">
        <button @click="page=Math.max(1,page-1)" class="ub us" :disabled="page===1">Prev</button>
        <button @click="page=Math.min(totalPages,page+1)" class="ub us" :disabled="page===totalPages">Next</button>
      </div>
    </div>
  </div>`,
};

/* Main App */
createApp({
  components: {'session-table': SessionTable},
  data() {
    return {
      payload: window.__UC,
      filters: {from:'',to:'',hour:'',page:'',device:'',browser:'',os:'',country:'',city:'',authenticated:''},
      active: 'overview',
      sidebarOpen: false,
      filtersOpen: false,
      autoRefresh: true,
      profileOpen: false,
      activeDays: null,
      globalSearch: '',
      liveSearch: '',
      liveUsers: window.__UC.liveUsers||[],
      selectedUser: null,
      replay: null,
      replayTime: 0,
      playing: false,
      speed: 1,
      charts: {},
      selectedHeatId: null,
      heatFilter: 'click',
      activeDoc: 'quickstart',
      openFaq: null,
      dColors: ['#3b82f6','#14b8a6','#8b5cf6','#f59e0b','#f43f5e','#10b981'],
      dateRanges: [{d:7,l:'7d'},{d:30,l:'30d'},{d:90,l:'90d'}],
      heatTypes: [{v:'click',l:'Click'},{v:'scroll',l:'Scroll'},{v:'movement',l:'Movement'}],
      nav: [
        {key:'overview', label:'Overview',         icon:'M4 13h6V4H4v9Zm10 7h6V4h-6v16ZM4 20h6v-3H4v3Z'},
        {key:'live',     label:'Live Users',       icon:'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2 M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M23 21v-2a4 4 0 0 0-3-3.87 M16 3.13a4 4 0 0 1 0 7.75'},
        {key:'sessions', label:'Sessions',         icon:'M4 6h16 M4 12h10 M4 18h16 M18 10l4 2-4 2v-4Z'},
        {key:'pages',    label:'Page Views',       icon:'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6 M16 13H8 M16 17H8 M10 9H8'},
        {key:'behavior', label:'Clicks & Scrolls', icon:'M3 3l7.5 18 2.5-7 7-2.5L3 3Z M14 14l5 5'},
        {key:'heatmaps', label:'Heatmaps',         icon:'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20 M12 6a6 6 0 1 0 0 12 6 6 0 0 0 0-12 M12 10a2 2 0 1 0 0 4 2 2 0 0 0 0-4'},
        {key:'events',   label:'Events',           icon:'M4 19V5 M4 19h16 M8 15l3-4 3 2 5-7'},
        {key:'exports',  label:'Exports & Reports',icon:'M12 3v12 M7 10l5 5 5-5 M5 21h14'},
        {key:'docs',     label:'Documentation',    icon:'M12 2L2 7l10 5 10-5-10-5 M2 17l10 5 10-5 M2 12l10 5 10-5'},
      ],
      docSections: [
        {id:'quickstart',   title:'Quick Start'},
        {id:'installation', title:'Installation'},
        {id:'snippet',      title:'JS Tracking Snippet'},
        {id:'events',       title:'Custom Events'},
        {id:'replay',       title:'Session Replay & Heatmaps'},
        {id:'filters',      title:'Filters & Segmentation'},
        {id:'exports',      title:'Exports & Reports'},
        {id:'config',       title:'Configuration Reference'},
        {id:'faq',          title:'FAQ & Troubleshooting'},
      ],
      dbTables: [
        {n:'uc_visitors',       d:'Unique visitor profiles with device & geo'},
        {n:'uc_sessions',       d:'Sessions with replay payload and AI summary'},
        {n:'uc_page_views',     d:'Page views with duration and scroll depth'},
        {n:'uc_click_events',   d:'Click coordinates and element selectors'},
        {n:'uc_behavior_events',d:'Mouse move, scroll, and typing events'},
        {n:'uc_custom_events',  d:'User-defined events with properties JSON'},
        {n:'uc_heatmap_data',   d:'Aggregated heatmaps with AI hotspots'},
      ],
      trackerFeats: [
        {l:'Page Views',      d:'Every URL change, title, referrer, viewport'},
        {l:'Clicks',          d:'X/Y coordinates, CSS selector, element text'},
        {l:'Scroll Depth',    d:'Max scroll % per page, sampled at 700ms'},
        {l:'Mouse Movements', d:'Path sampled at 220ms intervals'},
        {l:'Session Recording',d:'Full replay stored as gzipped JSON'},
        {l:'Custom Events',   d:'Via window.PrismPath.event()'},
        {l:'Visitor Identity',d:'Persistent UUID in localStorage'},
        {l:'Device & Browser',d:'User-agent parsed client-side'},
      ],
      filterDocs: [
        {p:'from',          ex:'2026-01-01', desc:'Start date (inclusive)'},
        {p:'to',            ex:'2026-01-31', desc:'End date (inclusive)'},
        {p:'hour',          ex:'14',         desc:'Hour of day 0-23'},
        {p:'page',          ex:'/pricing',   desc:'Match landing, exit, or current page'},
        {p:'device',        ex:'mobile',     desc:'desktop, mobile, tablet'},
        {p:'browser',       ex:'Chrome',     desc:'Browser name (partial match)'},
        {p:'os',            ex:'Windows',    desc:'Operating system'},
        {p:'country',       ex:'US',         desc:'ISO country code'},
        {p:'city',          ex:'New York',   desc:'City name'},
        {p:'authenticated', ex:'1',          desc:'1 = logged in, 0 = guest'},
      ],
      exportDocs: [
        {url:'/ultraclarity/export/json',      fmt:'JSON', desc:'Full dashboard payload for BI tools'},
        {url:'/ultraclarity/export/pageviews', fmt:'CSV',  desc:'Page views with duration and referrer'},
        {url:'/ultraclarity/export/clicks',    fmt:'CSV',  desc:'Click coordinates and selectors'},
        {url:'/ultraclarity/export/events',    fmt:'CSV',  desc:'Custom events and properties'},
        {url:'/ultraclarity/export/sessions',  fmt:'CSV',  desc:'Session summaries'},
        {url:'/ultraclarity/export/timelines', fmt:'CSV',  desc:'Per-session page sequences'},
        {url:'/ultraclarity/export/heatmaps',  fmt:'CSV',  desc:'Hotspots and AI summaries'},
        {url:'/ultraclarity/export/live',      fmt:'CSV',  desc:'Current active user snapshot'},
        {url:'/ultraclarity/report/pdf',       fmt:'HTML', desc:'Print-friendly executive report'},
      ],
      faqs: [
        {q:'The tracker script is not firing - why?',
         a:'<p>Check that <code class="ci">&#64;prismpath</code> is in your layout and <code class="ci">ULTRACLARITY_ENABLED=true</code> in <code class="ci">.env</code>. Open the browser console and confirm <code class="ci">/ultraclarity.js</code> loads without 404 errors. Make sure the package routes are included - they register via the service provider automatically.</p>'},
        {q:'No data appears in the dashboard.',
         a:'<p>1. Run <code class="ci">php artisan ultraclarity:seed</code> for demo data.</p><p class="mt-1">2. Check that the CSRF token is excluded for the collect endpoint - add <code class="ci">/api/ultraclarity/*</code> to <code class="ci">VerifyCsrfToken.php</code> $except array if needed.</p><p class="mt-1">3. Check <code class="ci">storage/logs/laravel.log</code> for collector errors.</p>'},
        {q:'How do I disable tracking for specific users?',
         a:'<p>Set the localStorage opt-out flag: <code class="ci">localStorage.setItem(\'ultraclarity:optout\', \'1\')</code>. You can toggle this from a cookie consent banner. The tracker checks this flag before sending any data.</p>'},
        {q:'Heatmaps show no data.',
         a:'<p>Heatmaps require click events to be collected first. Enable clicks: <code class="ci">ULTRACLARITY_CLICKS=true</code>. Then run <code class="ci">php artisan ultraclarity:heatmaps</code> to build aggregates from existing click data.</p>'},
        {q:'Session replays are empty / not recording.',
         a:'<p>Enable session recording: <code class="ci">ULTRACLARITY_SESSIONS=true</code>. The tracker must load on the page. Recordings are stored compressed - run <code class="ci">php artisan ultraclarity:seed</code> to see demo recordings immediately.</p>'},
        {q:'How do I change the dashboard URL?',
         a:'<p>Set <code class="ci">ULTRACLARITY_ROUTE_PREFIX=analytics</code> in <code class="ci">.env</code> and run <code class="ci">php artisan route:clear</code>. The dashboard will then be at <code class="ci">/analytics</code>.</p>'},
        {q:'Can I use my own auth instead of basic auth?',
         a:'<p>Yes. Set <code class="ci">ULTRACLARITY_DASHBOARD_AUTH=false</code> in <code class="ci">.env</code>, then change <code class="ci">config(\'ultraclarity.middleware\')</code> to include your own middleware (e.g. <code class="ci">[\'web\', \'auth\', \'can:view-analytics\']</code>).</p>'},
        {q:'How do I track conversions / purchases?',
         a:'<p>Use the JavaScript API after a successful action: <code class="ci">window.PrismPath?.event(\'purchase\', { total: 99, plan: \'pro\' })</code>. Then configure a funnel in <code class="ci">ultraclarity.php</code> to see the step-by-step conversion rates in the Events panel.</p>'},
      ],
    };
  },
  computed: {
    options() { return this.payload.filters||{}; },
    activeLabel() { return this.nav.find(n=>n.key===this.active)?.label||'Overview'; },
    activeFilterCount() { return Object.values(this.filters).filter(v=>v!=='').length; },
    activeFilterLabels() { return Object.entries(this.filters).filter(([,v])=>v!=='').map(([k,v])=>k+': '+v); },
    metricCards() {
      const s = this.payload.stats||{};
      return [
        {label:'Live',        val:s.live||0,                      col:'text-emerald-600', acc:'ma-g'},
        {label:'Visitors',    val:s.visitors||0,                  col:'text-slate-900',   acc:'ma-b'},
        {label:'Sessions',    val:s.sessions||0,                  col:'text-blue-700',    acc:'ma-b'},
        {label:'Page Views',  val:s.pageViews||0,                 col:'text-teal-700',    acc:'ma-t'},
        {label:'Avg Session', val:(s.averageDuration||0)+'s',     col:'text-amber-600',   acc:'ma-a'},
        {label:'Bounce Rate', val:(s.bounceRate||0)+'%',          col:'text-rose-600',    acc:'ma-r'},
        {label:'Avg Scroll',  val:(s.averageScroll||0)+'%',       col:'text-violet-700',  acc:'ma-v'},
        {label:'Movements',   val:s.movements||0,                 col:'text-orange-600',  acc:'ma-o'},
      ];
    },
    filteredLive() {
      const t = this.liveSearch.trim().toLowerCase();
      if(!t) return this.liveUsers;
      return this.liveUsers.filter(u=>[u.current_path,u.city,u.country,u.device,u.browser,u.os].filter(Boolean).join(' ').toLowerCase().includes(t));
    },
    selectedTimeline() { return this.replay?.pageTimeline||this.selectedUser?.timeline||[]; },
    filteredHeats() {
      const h = this.payload.heatmaps||[];
      return this.heatFilter ? h.filter(m=>m.type===this.heatFilter) : h;
    },
    selHeat() {
      const h = this.payload.heatmaps||[];
      return h.find(m=>m.id===this.selectedHeatId) || this.filteredHeats[0] || h[0] || null;
    },
    replayMax() {
      return Math.max(...((this.replay?.events||[]).map(e=>e.t||e.occurred_ms||0)),1000);
    },
    replayFrameEvents() {
      return (this.replay?.events||[]).filter(e=>Math.abs((e.t||0)-this.replayTime)<900&&e.x&&e.y).slice(-20);
    },
    exportItems() {
      const ip = 'M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4 M7 10l5 5 5-5 M12 15V3';
      return [
        {type:'json',      label:'Full JSON Export',    help:'All dashboard data for BI tools.',        fmt:'JSON', ibg:'bg-blue-50',   ic:'text-blue-600',   ip, bc:'bd-b'},
        {type:'pageviews', label:'Page Views CSV',      help:'URL, duration, referrer, scroll depth.',  fmt:'CSV',  ibg:'bg-teal-50',   ic:'text-teal-600',   ip, bc:'bd-t'},
        {type:'clicks',    label:'Clicks CSV',          help:'Coordinates, selector, and page.',        fmt:'CSV',  ibg:'bg-violet-50', ic:'text-violet-600', ip, bc:'bd-s'},
        {type:'events',    label:'Events CSV',          help:'Custom events and conversion signals.',   fmt:'CSV',  ibg:'bg-amber-50',  ic:'text-amber-600',  ip, bc:'bd-a'},
        {type:'sessions',  label:'Sessions CSV',        help:'Session summary with visitor metadata.',  fmt:'CSV',  ibg:'bg-rose-50',   ic:'text-rose-600',   ip, bc:'bd-r'},
        {type:'timelines', label:'Timelines CSV',       help:'Per-session page sequence and duration.', fmt:'CSV',  ibg:'bg-slate-50',  ic:'text-slate-600',  ip, bc:'bd-s'},
        {type:'heatmaps',  label:'Heatmaps CSV',        help:'Hotspots and AI summaries.',              fmt:'CSV',  ibg:'bg-emerald-50',ic:'text-emerald-600',ip, bc:'bd-g'},
        {type:'live',      label:'Live Users Snapshot', help:'Current active users for CRM sync.',      fmt:'CSV',  ibg:'bg-sky-50',    ic:'text-sky-600',    ip, bc:'bd-b'},
        {type:'pdf',       label:'Executive Report',    help:'Printable PDF-ready HTML report.',         fmt:'HTML', ibg:'bg-orange-50', ic:'text-orange-600', ip, bc:'bd-a'},
      ];
    },
  },
  mounted() {
    this.selectedHeatId = (this.payload.heatmaps||[])[0]?.id||null;
    this.$nextTick(()=>this.drawCharts());
    this.refreshLive();
    setInterval(()=>{ if(this.autoRefresh) this.refreshLive(); }, 5000);
    setInterval(()=>{ if(this.autoRefresh) this.refresh(); }, 30000);
    setInterval(()=>{
      if(!this.playing) return;
      this.replayTime += 250*this.speed;
      if(this.replayTime>=this.replayMax){ this.replayTime=0; this.playing=false; }
    }, 250);
    if(window.Echo) {
      window.Echo.channel('ultraclarity.live').listen('.session.updated', s=>{
        this.liveUsers = [s,...this.liveUsers.filter(u=>u.id!==s.id)].slice(0,50);
      });
    }
  },
  methods: {
    navigate(key) { this.active=key; this.sidebarOpen=false; this.$nextTick(()=>this.drawCharts()); },
    params() {
      return new URLSearchParams(Object.fromEntries(Object.entries(this.filters).filter(([,v])=>v!=='')));
    },
    exportUrl(type) {
      const q = this.params().toString();
      return window.__UCR.exportBase+'/'+type+(q?'?'+q:'');
    },
    applyRange(days) {
      this.activeDays = days;
      const to = new Date();
      const from = new Date(to); from.setDate(from.getDate()-days);
      this.filters.from = from.toISOString().slice(0,10);
      this.filters.to   = to.toISOString().slice(0,10);
      this.refresh();
    },
    clearFilters() {
      this.filters={from:'',to:'',hour:'',page:'',device:'',browser:'',os:'',country:'',city:'',authenticated:''};
      this.activeDays=null;
      this.refresh();
    },
    async refresh() {
      try {
        const r = await fetch(window.__UCR.data+'?'+this.params());
        this.payload = await r.json();
        this.liveUsers = this.payload.liveUsers||[];
        this.selectedHeatId = (this.payload.heatmaps||[])[0]?.id||null;
        this.$nextTick(()=>this.drawCharts());
      } catch(e) { console.warn('PrismPath refresh failed', e); }
    },
    async refreshLive() {
      try {
        const r = await fetch(window.__UCR.live+'?'+this.params());
        const d = await r.json();
        this.liveUsers = d.users||[];
      } catch(e) {}
    },
    selectLive(user) { this.selectedUser=user; },
    runGlobalSearch() {
      const t = this.globalSearch.trim().toLowerCase();
      if(!t) return;
      if(t.includes('heat')) this.active='heatmaps';
      else if(t.includes('event')||t.includes('funnel')||t.includes('conv')) this.active='events';
      else if(t.includes('session')||t.includes('replay')) this.active='sessions';
      else if(t.includes('page')||t.includes('url')) this.active='pages';
      else if(t.includes('click')||t.includes('scroll')) this.active='behavior';
      else if(t.includes('export')||t.includes('report')) this.active='exports';
      else if(t.includes('doc')||t.includes('install')) this.active='docs';
      else this.active='live';
      this.liveSearch=t;
    },
    async loadReplay(id) {
      try {
        const r = await fetch(window.__UCR.replay+'/'+id);
        this.replay = await r.json();
        this.replayTime=0; this.playing=true; this.active='sessions';
      } catch(e) { console.warn('Replay load failed', e); }
    },
    jumpToPage(pg) {
      const ev = (this.replay?.timeline||[]).find(e=>e.page_view_id===pg.id);
      this.replayTime = ev ? ev.occurred_ms : 0;
    },
    skipIdle() {
      const next = (this.replay?.events||[]).find(e=>(e.t||0)>this.replayTime+1500);
      if(next) this.replayTime=next.t||0;
    },
    aBadge(u) {
      const clicks=u.clicks||0, scroll=u.scroll||0, moves=u.movements||0;
      if(clicks>20) return {l:'High clicks', c:'bd-r'};
      if(scroll>80)  return {l:'Deep scroll', c:'bd-t'};
      if(moves>250)  return {l:'Exploring',   c:'bd-b'};
      return {l:'Active', c:'bd-g'};
    },
    evStyle(ev) {
      return {
        left: Math.min(96,Math.max(4,(ev.x||0)/14))+'%',
        top:  Math.min(96,Math.max(4,(ev.y||0)/16))+'%',
      };
    },
    hotStyle(sp) {
      const sz = Math.min(120,30+(sp.intensity||1)*5);
      return {
        left:   Math.min(96,Math.max(4,(sp.x||0)/14))+'%',
        top:    Math.min(96,Math.max(4,(sp.y||0)/16))+'%',
        width:  sz+'px', height: sz+'px',
        background: 'radial-gradient(circle, rgba(239,68,68,.65), rgba(249,115,22,.40) 45%, rgba(59,130,246,.18))',
      };
    },
    /* Charts */
    drawCharts() {
      this.mkChart('trendChart','line',{
        labels:(this.payload.trend||[]).map(r=>r.label),
        datasets:[
          {label:'Views',    data:(this.payload.trend||[]).map(r=>r.views),    borderColor:'#3b82f6',backgroundColor:'rgba(59,130,246,.10)',tension:.35,fill:true,pointRadius:2},
          {label:'Sessions', data:(this.payload.trend||[]).map(r=>r.sessions), borderColor:'#14b8a6',backgroundColor:'rgba(20,184,166,.08)',tension:.35,fill:true,pointRadius:2},
          {label:'Dur (s)',  data:(this.payload.trend||[]).map(r=>r.duration), borderColor:'#f59e0b',backgroundColor:'rgba(245,158,11,.08)',tension:.35,fill:true,pointRadius:2},
        ],
      });
      this.mkChart('pagesChart','bar',this.pageData());
      this.mkChart('pagesPanelChart','bar',this.pageData());
      this.mkChart('behaviorChart','bar',this.pageData());
      this.mkChart('devicesChart','doughnut',{
        labels:(this.payload.segments?.devices||[]).map(r=>r.device),
        datasets:[{data:(this.payload.segments?.devices||[]).map(r=>r.total),backgroundColor:['#3b82f6','#14b8a6','#8b5cf6','#f59e0b','#f43f5e'],borderWidth:2,borderColor:'#fff'}],
      });
      this.mkChart('systemsChart','pie',{
        labels:[...(this.payload.segments?.browsers||[]).map(r=>r.browser),...(this.payload.segments?.systems||[]).map(r=>r.os)],
        datasets:[{data:[...(this.payload.segments?.browsers||[]).map(r=>r.total),...(this.payload.segments?.systems||[]).map(r=>r.total)],backgroundColor:['#3b82f6','#f59e0b','#14b8a6','#8b5cf6','#fb7185','#6366f1','#ec4899','#06b6d4'],borderWidth:2,borderColor:'#fff'}],
      });
    },
    pageData() {
      return {
        labels:(this.payload.topPages||[]).map(r=>r.path),
        datasets:[
          {label:'Views',   data:(this.payload.topPages||[]).map(r=>r.views),   backgroundColor:'#3b82f6',borderRadius:6},
          {label:'Clicks',  data:(this.payload.topPages||[]).map(r=>r.clicks),  backgroundColor:'#14b8a6',borderRadius:6},
          {label:'Scroll%', data:(this.payload.topPages||[]).map(r=>r.scroll),  backgroundColor:'#f59e0b',borderRadius:6},
        ],
      };
    },
    mkChart(id, type, data) {
      const el = document.getElementById(id);
      if(!el) return;
      if(this.charts[id]) this.charts[id].destroy();
      this.charts[id] = new Chart(el, {
        type, data,
        options: {
          responsive:true, maintainAspectRatio:false, resizeDelay:120,
          animation:{duration:300},
          interaction:{mode:'index',intersect:false},
          plugins:{
            legend:{labels:{color:'#64748b',boxWidth:10,usePointStyle:true,font:{family:'Poppins',size:10}}},
            tooltip:{backgroundColor:'#0f172a',titleColor:'#f8fafc',bodyColor:'#cbd5e1',borderColor:'#1e293b',borderWidth:1,padding:10,cornerRadius:8},
          },
          scales: type==='line'||type==='bar' ? {
            x:{ticks:{color:'#94a3b8',maxRotation:0,autoSkip:true,font:{family:'Poppins',size:10}},grid:{color:'rgba(148,163,184,.12)'}},
            y:{beginAtZero:true,ticks:{color:'#94a3b8',font:{family:'Poppins',size:10}},grid:{color:'rgba(148,163,184,.15)'}},
          } : {},
        },
      });
    },
  },
}).mount('#app');
</script>
</body>
</html>


