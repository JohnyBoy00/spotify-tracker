<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model for imported Spotify streaming history data.
 */
class ImportedStreamingHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'imported_streaming_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'played_at',
        'platform',
        'ms_played',
        'track_name',
        'artist_name',
        'album_name',
        'spotify_track_uri',
        'skipped',
        'reason_start',
        'reason_end',
        'shuffle',
        'offline',
        'incognito_mode',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'played_at' => 'datetime',
        'skipped' => 'boolean',
        'shuffle' => 'boolean',
        'offline' => 'boolean',
        'incognito_mode' => 'boolean',
    ];

    /**
     * Get the user that owns the streaming history.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
