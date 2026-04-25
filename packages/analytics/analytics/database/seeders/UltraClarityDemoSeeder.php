<?php

namespace UltraClarity\Analytics\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use UltraClarity\Analytics\Models\BehaviorEvent;
use UltraClarity\Analytics\Models\ClickEvent;
use UltraClarity\Analytics\Models\CustomEvent;
use UltraClarity\Analytics\Models\HeatmapData;
use UltraClarity\Analytics\Models\PageView;
use UltraClarity\Analytics\Models\Session;
use UltraClarity\Analytics\Models\Visitor;
use UltraClarity\Analytics\Services\RecordingCompressor;

class UltraClarityDemoSeeder extends Seeder
{
    public function run(bool $fresh = false): void
    {
        if ($fresh) {
            HeatmapData::truncate();
            BehaviorEvent::truncate();
            CustomEvent::truncate();
            ClickEvent::truncate();
            PageView::truncate();
            Session::truncate();
            Visitor::truncate();
        }

        $paths = ['/', '/pricing', '/docs', '/checkout', '/demo'];
        $devices = ['desktop', 'mobile', 'tablet'];
        $compressor = app(RecordingCompressor::class);

        for ($i = 0; $i < 36; $i++) {
            $visitor = Visitor::create([
                'visitor_uuid' => (string) Str::uuid(),
                'site_id' => config('ultraclarity.site_id'),
                'ip_hash' => hash('sha256', 'demo-' . $i),
                'ip_address' => '203.0.113.' . ($i + 10),
                'country' => fake()->countryCode(),
                'city' => fake()->city(),
                'device' => fake()->randomElement($devices),
                'browser' => fake()->randomElement(['Chrome', 'Safari', 'Edge', 'Firefox']),
                'os' => fake()->randomElement(['Windows', 'macOS', 'iOS', 'Android', 'Linux']),
                'is_authenticated' => fake()->boolean(38),
                'user_identifier' => fake()->optional(.38)->safeEmail(),
                'traits' => ['plan' => fake()->randomElement(['free', 'pro', 'business'])],
                'last_seen_at' => $i < 10 ? now()->subMinutes(fake()->numberBetween(1, 4)) : now()->subMinutes(fake()->numberBetween(8, 1400)),
            ]);

            $sessionPath = fake()->randomElement($paths);
            $session = Session::create([
                'visitor_id' => $visitor->id,
                'session_uuid' => (string) Str::uuid(),
                'landing_page' => $sessionPath,
                'exit_page' => fake()->randomElement($paths),
                'current_url' => url($sessionPath),
                'current_path' => $sessionPath,
                'source' => fake()->randomElement(['direct', 'google.com', 'linkedin.com', 'newsletter', 'twitter.com']),
                'duration_seconds' => fake()->numberBetween(42, 620),
                'max_scroll_depth' => fake()->numberBetween(35, 100),
                'event_count' => fake()->numberBetween(5, 45),
                'click_count' => fake()->numberBetween(2, 28),
                'movement_count' => fake()->numberBetween(40, 320),
                'last_scroll_depth' => fake()->numberBetween(12, 100),
                'recording_payload' => $compressor->compress($this->recording()),
                'ai_summary' => ['intent' => fake()->randomElement(['research', 'purchase', 'support'])],
                'started_at' => now()->subDays(fake()->numberBetween(0, 14))->subMinutes(fake()->numberBetween(1, 800)),
                'ended_at' => $i < 10 ? now()->subMinutes(fake()->numberBetween(1, 4)) : now()->subMinutes(fake()->numberBetween(8, 800)),
                'last_activity_at' => $i < 10 ? now()->subMinutes(fake()->numberBetween(1, 4)) : now()->subMinutes(fake()->numberBetween(8, 800)),
            ]);

            foreach (fake()->randomElements($paths, fake()->numberBetween(1, 3)) as $index => $path) {
                $pageView = PageView::create([
                    'session_id' => $session->id,
                    'sequence' => $index + 1,
                    'url' => url($path),
                    'path' => $path,
                    'title' => 'Demo ' . ucfirst(trim($path, '/') ?: 'Home'),
                    'referrer' => fake()->optional()->url(),
                    'viewport_width' => fake()->randomElement([390, 768, 1280, 1440]),
                    'viewport_height' => fake()->randomElement([740, 900, 1024]),
                    'time_on_page' => fake()->numberBetween(8, 180),
                    'scroll_depth' => fake()->numberBetween(22, 100),
                    'viewed_at' => now()->subDays(fake()->numberBetween(0, 14)),
                ]);

                for ($c = 0; $c < fake()->numberBetween(2, 8); $c++) {
                    ClickEvent::create([
                        'session_id' => $session->id,
                        'page_view_id' => $pageView->id,
                        'path' => $path,
                        'x' => fake()->numberBetween(80, 1180),
                        'y' => fake()->numberBetween(120, 1400),
                        'viewport_width' => $pageView->viewport_width,
                        'viewport_height' => $pageView->viewport_height,
                        'selector' => fake()->randomElement(['button.cta', 'a.nav-link', '[data-track=checkout]', '.pricing-card']),
                        'text' => fake()->randomElement(['Start trial', 'View docs', 'Upgrade', 'Book demo']),
                        'clicked_at' => now()->subDays(fake()->numberBetween(0, 14)),
                    ]);
                    BehaviorEvent::create([
                        'session_id' => $session->id,
                        'page_view_id' => $pageView->id,
                        'type' => 'click',
                        'path' => $path,
                        'x' => fake()->numberBetween(80, 1180),
                        'y' => fake()->numberBetween(120, 1400),
                        'scroll_depth' => fake()->numberBetween(0, 100),
                        'viewport_width' => $pageView->viewport_width,
                        'viewport_height' => $pageView->viewport_height,
                        'metadata' => ['selector' => 'button.cta'],
                        'occurred_ms' => fake()->numberBetween(500, 90000),
                        'occurred_at' => now()->subDays(fake()->numberBetween(0, 14)),
                    ]);
                    BehaviorEvent::create([
                        'session_id' => $session->id,
                        'page_view_id' => $pageView->id,
                        'type' => 'move',
                        'path' => $path,
                        'x' => fake()->numberBetween(80, 1180),
                        'y' => fake()->numberBetween(120, 1400),
                        'viewport_width' => $pageView->viewport_width,
                        'viewport_height' => $pageView->viewport_height,
                        'metadata' => [],
                        'occurred_ms' => fake()->numberBetween(500, 90000),
                        'occurred_at' => now()->subDays(fake()->numberBetween(0, 14)),
                    ]);
                }
            }

            CustomEvent::create([
                'session_id' => $session->id,
                'name' => fake()->randomElement(['signup_started', 'plan_selected', 'demo_requested', 'export_clicked']),
                'path' => fake()->randomElement($paths),
                'properties' => ['value' => fake()->numberBetween(10, 900), 'source' => 'demo'],
                'occurred_at' => now()->subDays(fake()->numberBetween(0, 14)),
            ]);
        }
    }

    private function recording(): array
    {
        return collect(range(1, 24))->map(fn ($tick) => [
            't' => $tick * 350,
            'type' => fake()->randomElement(['move', 'scroll', 'click']),
            'x' => fake()->numberBetween(80, 1100),
            'y' => fake()->numberBetween(100, 1200),
            'scroll' => fake()->numberBetween(0, 90),
        ])->all();
    }
}

