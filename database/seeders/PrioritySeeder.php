<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Priority;

class PrioritySeeder extends Seeder
{
    public function run(): void
    {
        $priorities = [
            ['name' => 'Tinggi', 'slug' => 'high', 'level' => 3],
            ['name' => 'Sedang', 'slug' => 'medium', 'level' => 2],
            ['name' => 'Rendah', 'slug' => 'low', 'level' => 1],
        ];

        foreach ($priorities as $priority) {
            Priority::firstOrCreate(['name' => $priority['name']], $priority);
        }
    }
}
