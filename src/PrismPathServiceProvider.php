<?php

namespace PrismPath\Analytics;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use PrismPath\Analytics\Commands\CleanupCommand;
use PrismPath\Analytics\Commands\RegenerateHeatmapsCommand;
use PrismPath\Analytics\Commands\ReportCommand;
use PrismPath\Analytics\Commands\SeedDemoCommand;
use PrismPath\Analytics\Services\PrismPathManager;

class PrismPathServiceProvider extends ServiceProvider
{
        public function register()
        {
            $this->mergeConfigFrom(__DIR__ . '/../config/prismpath.php', 'prismpath');

            $this->app->singleton('prismpath', function ($app) {
                return new PrismPathManager($app);
            });
        }

        public function boot()
        {
            $this->loadViewsFrom(__DIR__ . '/../resources/views', 'prismpath');
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

            Blade::directive('prismpath', function () {
                return "<?php echo app('prismpath')->script(); ?>";
            });
            Blade::directive('prismpathStats', function () {
                return "<?php echo view('prismpath::components.live-stats', ['stats' => app(\\PrismPath\\Analytics\\Repositories\\AnalyticsRepository::class)->stats()])->render(); ?>";
            });
            $this->publishes([__DIR__ . '/../config/prismpath.php' => config_path('prismpath.php')], 'prismpath-config');
            $this->publishes([__DIR__ . '/../database/migrations' => database_path('migrations')], 'prismpath-migrations');
            $this->publishes([__DIR__ . '/../resources/js' => resource_path('js/vendor/prismpath')], 'prismpath-assets');
            $this->commands([CleanupCommand::class, SeedDemoCommand::class, RegenerateHeatmapsCommand::class, ReportCommand::class]);
        }
    }
}

