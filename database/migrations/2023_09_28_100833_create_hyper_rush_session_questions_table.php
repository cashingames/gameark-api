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
        Schema::create('hyper_rush_session_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->references('id')->on('questions')->onDelete('cascade')->nullable();
            $table->foreignId('hyper_rush_game_session_id')->references('id')->on('hyper_rush_game_sessions')->onDelete('cascade')->nullable();
            $table->integer('option_id')->nullable();
            $table->timestamps();
        });
    }
   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hyper_rush_session_questions');
    }
};
