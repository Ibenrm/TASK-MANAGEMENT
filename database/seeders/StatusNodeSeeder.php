<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StatusNode;

class StatusNodeSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Belum Dikerjakan',
                'slug' => 'to_do',
                'sort_order' => 1,
            ],
            [
                'name' => 'Sedang Proses',
                'slug' => 'in_progress',
                'sort_order' => 2,
            ],
            [
                'name' => 'Sudah Selesai',
                'slug' => 'done',
                'sort_order' => 3,
            ]
        ];

        foreach ($statuses as $status) {
            StatusNode::firstOrCreate(
                ['slug' => $status['slug']],
                $status
            );
        }

        // Link next_status_id if needed
        $todo = StatusNode::where('slug', 'to_do')->first();
        $inProgress = StatusNode::where('slug', 'in_progress')->first();
        $done = StatusNode::where('slug', 'done')->first();

        if ($todo && $inProgress) {
            $todo->update(['next_status_id' => $inProgress->id]);
        }
        if ($inProgress && $done) {
            $inProgress->update(['next_status_id' => $done->id]);
        }
    }
}
