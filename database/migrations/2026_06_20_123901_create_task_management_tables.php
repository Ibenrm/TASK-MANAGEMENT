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
        // 1. status_nodes
        Schema::create('status_nodes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->foreignId('next_status_id')->nullable()->constrained('status_nodes')->nullOnDelete();
            $table->integer('sort_order')->default(0);
        });

        // 2. priorities
        Schema::create('priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->integer('level')->default(0);
        });

        // 3. tasks
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->text('note')->nullable();
            $table->foreignId('status_id')->constrained('status_nodes');
            $table->foreignId('priority_id')->constrained('priorities');
            $table->date('start_date')->nullable();
            $table->date('deadline_date')->nullable();
            $table->integer('position')->default(0);
            $table->timestamps();
        });

        // 4. task_todos
        Schema::create('task_todos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->string('todo_text', 255);
            $table->boolean('is_checked')->default(false);
            $table->integer('position')->default(0);
            $table->timestamps();
        });

        // 5. user_profiles
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('avatar_url', 255)->nullable();
            $table->text('bio')->nullable();
        });

        // 6. task_assignees
        Schema::create('task_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_assignees');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('task_todos');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('priorities');
        
        Schema::table('status_nodes', function (Blueprint $table) {
            $table->dropForeign(['next_status_id']);
        });
        Schema::dropIfExists('status_nodes');
    }
};
