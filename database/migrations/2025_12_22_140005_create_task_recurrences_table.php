<?php

use Domain\Schedule\Tasks\Models\Task;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('task_recurrences', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Task::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->enum('type', [
                'daily',
                'weekly',
                'monthly',
                'custom'
            ]);

            $table->json('days_of_week')->nullable(); // [1,3] → Пн, Ср
            $table->json('days_of_month')->nullable(); // [1,5,10]

            $table->time('time'); // 10:00

            $table->boolean('is_active')->default(true);

            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_recurrences');
    }
};
