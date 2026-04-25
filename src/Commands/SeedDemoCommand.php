<?php

namespace PrismPath\Analytics\Commands;

use Illuminate\Console\Command;
use PrismPath\Analytics\Database\Seeders\PrismPathDemoSeeder;

class SeedDemoCommand extends Command
{
    protected $signature = 'ultraclarity:seed {--fresh : Clear existing PrismPath rows first}';

    protected $description = 'Seed demo analytics data for the PrismPath dashboard.';

    public function handle(): int
    {
        app(PrismPathDemoSeeder::class)->setCommand($this)->run((bool) $this->option('fresh'));
        $this->call('ultraclarity:heatmaps');

        return self::SUCCESS;
    }
}

