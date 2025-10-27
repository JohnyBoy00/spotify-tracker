<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for tracking individual listening history records.
 */
class ListeningHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'listening_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'track_id',
        'track_name',
        'artist_name',
        'album_name',
        'duration_ms',
        'played_at',
        'listened_ms',
        'completed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'played_at' => 'datetime',
        'completed' => 'boolean',
        'duration_ms' => 'integer',
        'listened_ms' => 'integer',
    ];

    /**
     * Get the user that owns this listening history record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
