<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for daily aggregated listening statistics.
 */
class DailyListeningSummary extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'daily_listening_summary';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'date',
        'total_minutes',
        'total_tracks',
        'unique_artists',
        'unique_albums',
        'top_genre',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'total_minutes' => 'integer',
        'total_tracks' => 'integer',
        'unique_artists' => 'integer',
        'unique_albums' => 'integer',
    ];

    /**
     * Get the user that owns this summary.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
