<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Destination;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
   
    user::factory(10)
        ->has(Destination::factory(5))
        ->create();

    }
}
