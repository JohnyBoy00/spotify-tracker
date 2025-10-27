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
        Schema::table('users', function (Blueprint $table) {
            $table->string('spotify_id')->unique()->nullable()->after('id');
            $table->string('spotify_display_name')->nullable()->after('spotify_id');
            $table->string('spotify_email')->nullable()->after('spotify_display_name');
            $table->text('access_token')->nullable()->after('spotify_email');
            $table->text('refresh_token')->nullable()->after('access_token');
            $table->timestamp('token_expires_at')->nullable()->after('refresh_token');
            $table->unsignedInteger('total_listening_minutes')->default(0)->after('token_expires_at');
            $table->timestamp('last_tracked_at')->nullable()->after('total_listening_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'spotify_id',
                'spotify_display_name',
                'spotify_email',
                'access_token',
                'refresh_token',
                'token_expires_at',
                'total_listening_minutes',
                'last_tracked_at'
            ]);
        });
    }
};
