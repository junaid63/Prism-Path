<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use PrismPath\Analytics\Database\Seeders\PrismPathDemoSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@prismpath.test'],
            ['name' => 'PrismPath Admin', 'password' => Hash::make('password')]
        );

        $this->call(PrismPathDemoSeeder::class);
    }
}
