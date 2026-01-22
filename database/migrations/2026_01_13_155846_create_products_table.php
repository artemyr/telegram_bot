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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('telegram_user_id')
                ->constrained('telegram_users','telegram_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('title');
            $table->boolean('exist')->default(false);
            $table->unsignedInteger('expire_days')->nullable();
            $table->timestamp('buy_at')->nullable();

            $table->enum('store', [
                'fridge',
                'grocery',
                'other',
            ])->default('other');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
