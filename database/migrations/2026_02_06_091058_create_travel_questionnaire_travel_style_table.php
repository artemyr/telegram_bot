<?php

use Domain\Travel\Models\TravelQuestionnaire;
use Domain\Travel\Models\TravelStyle;
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
        Schema::create('travel_questionnaire_travel_style', function (Blueprint $table) {
            $table->foreignIdFor(TravelQuestionnaire::class)
                ->constrained('travel_questionnaires','id','travel_questionnaire_id_foreign')
                ->cascadeOnDelete();
            $table->foreignIdFor(TravelStyle::class)
                ->constrained('travel_styles','id','travel_style_id_foreign')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_questionnaire_travel_style');
    }
};
