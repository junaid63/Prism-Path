<div class="ultraclarity-live-stats" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;font-family:Poppins,ui-sans-serif,system-ui,sans-serif">
    <div style="border:1px solid rgba(148,163,184,.25);border-radius:8px;padding:10px;background:#0f172a;color:#fff">
        <div style="font-size:11px;color:#94a3b8">Live</div>
        <strong>{{ $stats['live'] ?? 0 }}</strong>
    </div>
    <div style="border:1px solid rgba(148,163,184,.25);border-radius:8px;padding:10px;background:#0f172a;color:#fff">
        <div style="font-size:11px;color:#94a3b8">Visitors</div>
        <strong>{{ $stats['visitors'] ?? 0 }}</strong>
    </div>
    <div style="border:1px solid rgba(148,163,184,.25);border-radius:8px;padding:10px;background:#0f172a;color:#fff">
        <div style="font-size:11px;color:#94a3b8">Views</div>
        <strong>{{ $stats['pageViews'] ?? 0 }}</strong>
    </div>
</div>

