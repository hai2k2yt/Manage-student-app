<?php

namespace Database\Seeders;

use App\Models\ClubSession;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClubSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClubSession::factory(40)->create();
    }
}
