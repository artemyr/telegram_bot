<?php

use Domain\TelegramBot\Models\TelegramUser;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->string('title');

            $table->foreignId('telegram_user_id')
                ->constrained('telegram_users','telegram_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamp('deadline')
                ->nullable();
            $table->unsignedInteger('priority')
                ->default(0);

            $table->boolean('repeat')
                ->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
