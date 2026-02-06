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
        Schema::create('travel_questionnaires', function (Blueprint $table) {
            $table->id();

            $table->foreignId('telegram_user_id')
                ->constrained('telegram_users','telegram_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('name')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->enum('level', ['beginner', 'intermediate','confident','expert'])->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_questionnaires');
    }
};
