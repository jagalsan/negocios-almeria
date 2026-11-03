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
        Schema::create('ranking_position_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ranking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('position');
            $table->date('recorded_on');
            $table->timestamps();
            $table->unique(['ranking_id', 'company_id', 'recorded_on'], 'ranking_company_history_date_unique');
            $table->index(['ranking_id', 'recorded_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranking_position_histories');
    }
};
