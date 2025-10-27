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
        Schema::create('daily_listening_summary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('date')->comment('Date of the summary');
            $table->unsignedInteger('total_minutes')->default(0)->comment('Total minutes listened on this date');
            $table->unsignedInteger('total_tracks')->default(0)->comment('Total tracks played on this date');
            $table->unsignedInteger('unique_artists')->default(0)->comment('Number of unique artists listened to');
            $table->unsignedInteger('unique_albums')->default(0)->comment('Number of unique albums listened to');
            $table->string('top_genre')->nullable()->comment('Most listened genre for this date');
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('date');
            $table->index(['user_id', 'date']);
            
            // One summary per user per day
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_listening_summary');
    }
};
