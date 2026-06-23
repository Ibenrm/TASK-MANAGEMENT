<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('previous_task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->foreignId('next_task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->dropColumn('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['previous_task_id']);
            $table->dropForeign(['next_task_id']);
            $table->dropColumn('previous_task_id');
            $table->dropColumn('next_task_id');
            $table->integer('position')->default(0);
        });
    }
};
