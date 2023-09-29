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
        Schema::create('hyper_rush_game_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('session_token');
            $table->integer('correct_count')->default(0)->nullable();
            $table->integer('wrong_count')->default(0)->nullable();
            $table->integer('total_count')->default(0)->nullable();
            $table->integer('high_score')->default(0)->nullable();
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->string('state');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hyper_rush_game_sessions');
    }
};
