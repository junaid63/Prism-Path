<?php

namespace UltraClarity\Analytics;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use UltraClarity\Analytics\Commands\CleanupCommand;
use UltraClarity\Analytics\Commands\RegenerateHeatmapsCommand;
use UltraClarity\Analytics\Commands\ReportCommand;
use UltraClarity\Analytics\Commands\SeedDemoCommand;
use UltraClarity\Analytics\Services\UltraClarityManager;

class UltraClarityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ultraclarity.php', 'ultraclarity');

        $this->app->singleton('ultraclarity', fn ($app) => new UltraClarityManager($app));
        $this->app->alias('ultraclarity', 'prismpath');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ultraclarity');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        Blade::directive('ultraclarity', fn () => "<?php echo app('ultraclarity')->script(); ?>");
        Blade::directive('prismpath', fn () => "<?php echo app('prismpath')->script(); ?>");
        Blade::directive('ultraclarityStats', fn () => "<?php echo view('ultraclarity::components.live-stats', ['stats' => app(\\UltraClarity\\Analytics\\Repositories\\AnalyticsRepository::class)->stats()])->render(); ?>");
        Blade::directive('prismpathStats', fn () => "<?php echo view('ultraclarity::components.live-stats', ['stats' => app(\\UltraClarity\\Analytics\\Repositories\\AnalyticsRepository::class)->stats()])->render(); ?>");

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/ultraclarity.php' => config_path('ultraclarity.php')], 'ultraclarity-config');
            $this->publishes([__DIR__ . '/../database/migrations' => database_path('migrations')], 'ultraclarity-migrations');
            $this->publishes([__DIR__ . '/../resources/js' => resource_path('js/vendor/ultraclarity')], 'ultraclarity-assets');
            $this->commands([CleanupCommand::class, SeedDemoCommand::class, RegenerateHeatmapsCommand::class, ReportCommand::class]);
        }
    }
}

