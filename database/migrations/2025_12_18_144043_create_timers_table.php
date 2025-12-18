<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('telegram_user_id')
                ->constrained('telegram_users','telegram_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('class');
            $table->timestamp('startDate');
            $table->string('code');
            $table->string('title');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timers');
    }
};
