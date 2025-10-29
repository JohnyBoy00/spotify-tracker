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
        Schema::create('imported_streaming_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('played_at'); // ts from JSON
            $table->string('platform')->nullable();
            $table->integer('ms_played'); // milliseconds played
            $table->string('track_name')->nullable();
            $table->string('artist_name')->nullable();
            $table->string('album_name')->nullable();
            $table->string('spotify_track_uri')->nullable();
            $table->boolean('skipped')->default(false);
            $table->string('reason_start')->nullable();
            $table->string('reason_end')->nullable();
            $table->boolean('shuffle')->default(false);
            $table->boolean('offline')->default(false);
            $table->boolean('incognito_mode')->default(false);
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['user_id', 'played_at']);
            $table->index('played_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imported_streaming_history');
    }
};
