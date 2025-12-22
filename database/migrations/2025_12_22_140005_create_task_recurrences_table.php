<?php

use Domain\Tasks\Models\Task;
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

            $table->string('type');
            $table->json('rule');
            $table->timestamp('start_at')
                ->nullable();
            $table->timestamp('end_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_recurrences');
    }
};
