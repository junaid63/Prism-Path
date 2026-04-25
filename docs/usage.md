# Usage Guide

PrismPath is organized around dashboard panels that match the way product, growth, and support teams investigate behavior.

## Access The Dashboard

Open either URL:

```text
/ultraclarity
/ultraclarity/dashboard
```

Use the sidebar to reach every panel in two or three clicks. Use the top search bar to jump to live users, sessions, pages, heatmaps, clicks, events, or exports.

## Live Users

Open **Live Users** to see active sessions.

Each live user card shows:

- Current page
- Activity label
- Device, browser, OS
- City and country when available
- Click count
- Scroll depth
- Session duration

Use the search input to filter by page, browser, device, or location. Click a user to load their timeline. Click **Replay** to open the recorded session.

## Sessions And Timelines

Open **Sessions & Timelines**.

The table supports:

- Search
- Sorting by visitor, device, duration, and scroll
- Pagination
- Expandable row details
- One-click replay

The horizontal timeline shows pages in visit order. Each bar is sized by duration and shows clicks, scroll depth, movement samples, source, and event counts on hover.

## Session Replay

Replay controls:

- **Play / Pause** starts and stops playback.
- **Slow** plays at half speed.
- **1x** plays normal speed.
- **Fast** plays at 3x.
- **Skip idle** jumps to the next recorded action.
- Page buttons jump to specific page views.

The replay surface highlights recorded clicks and movement points. AI summaries call out engagement patterns and unusual behavior.

## Page Views And Clicks

Open **Page Views** for traffic and duration by URL. Open **Clicks & Scrolls** for element-level behavior.

Useful questions:

- Which pages get the most traffic?
- Where do users click most often?
- Which pages have high exit rates?
- Are users scrolling deeply enough to see important content?

## Heatmaps

Open **Heatmaps**.

Choose a page/type from the dropdown:

- Click heatmaps
- Scroll heatmaps
- Movement density heatmaps

Use filters for date range, device, browser, OS, country, city, logged-in users, or guests. Hotspots show intensity and AI analysis explains where users engage most.

## Events And Conversions

Open **Events & Conversions** to review custom events and funnels.

Track custom events from your frontend:

```js
window.PrismPath.event('checkout_started', {
    plan: 'team',
    value: 199
});
```

Default funnel steps are configured in `config/ultraclarity.php`.

## Filters

Click **Filters** in the top bar. Filters update charts, tables, heatmaps, exports, and live data.

Available filters:

- Date range
- Hour of day
- Page
- Device
- Browser
- OS
- Country
- City
- Logged-in vs guest

Active filters appear as chips above the filter controls.

## Exports And Reports

Open **Exports & Reports**.

Available exports:

- Full JSON
- Page views CSV
- Clicks CSV
- Events CSV
- Heatmaps CSV
- Timelines CSV
- Sessions CSV
- Live users CSV
- Print/PDF report

Scheduled reports are generated with:

```bash
php artisan ultraclarity:report daily
php artisan ultraclarity:report weekly
php artisan ultraclarity:report monthly --email
```

## Demo Data

Load sample analytics:

```bash
php artisan ultraclarity:seed
```

Reset and reseed:

```bash
php artisan ultraclarity:seed --fresh
```

The demo data includes visitors, sessions, page views, clicks, custom events, movement samples, heatmaps, and AI summaries so the dashboard looks alive immediately.

