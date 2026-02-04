<?php

use Domain\Travel\Models\TravelClaim;
use Domain\Travel\Models\TravelFormat;
use Domain\Travel\Models\TravelQuestionnaire;
use Domain\Travel\Models\TravelResort;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('travel_claims', function (Blueprint $table) {
            $table->id();

            $table->foreignId('telegram_user_id')
                ->constrained('telegram_users', 'telegram_id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignIdFor(TravelQuestionnaire::class)
                ->nullable()
                ->constrained();

            $table->foreignIdFor(TravelFormat::class)
                ->nullable()
                ->constrained();

            $table->foreignIdFor(TravelResort::class)
                ->nullable()
                ->constrained();

            $table->timestamp('date_from')
                ->nullable();
            $table->timestamp('date_to')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_claims');
    }
};
