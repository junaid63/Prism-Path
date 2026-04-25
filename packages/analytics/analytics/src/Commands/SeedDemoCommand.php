<?php

namespace UltraClarity\Analytics\Commands;

use Illuminate\Console\Command;
use UltraClarity\Analytics\Database\Seeders\UltraClarityDemoSeeder;

class SeedDemoCommand extends Command
{
    protected $signature = 'ultraclarity:seed {--fresh : Clear existing UltraClarity rows first}';

    protected $description = 'Seed demo analytics data for the PrismPath dashboard.';

    public function handle(): int
    {
        app(UltraClarityDemoSeeder::class)->setCommand($this)->run((bool) $this->option('fresh'));
        $this->call('ultraclarity:heatmaps');

        return self::SUCCESS;
    }
}

