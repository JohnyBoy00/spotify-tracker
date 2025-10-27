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
        Schema::create('listening_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('track_id')->comment('Spotify track ID');
            $table->string('track_name', 500);
            $table->string('artist_name', 500);
            $table->string('album_name', 500)->nullable();
            $table->unsignedInteger('duration_ms')->comment('Total track duration in milliseconds');
            $table->timestamp('played_at')->comment('When the track was played');
            $table->unsignedInteger('listened_ms')->comment('Milliseconds actually listened');
            $table->boolean('completed')->default(false)->comment('Whether the full track was played');
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('played_at');
            $table->index(['user_id', 'played_at']);
            
            // Prevent duplicate entries
            $table->unique(['user_id', 'track_id', 'played_at'], 'unique_play');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listening_history');
    }
};
