<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use PrismPath\Analytics\Database\Seeders\DemoSeeder as PackageSeeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        app(PackageSeeder::class)->run();
    }
}
