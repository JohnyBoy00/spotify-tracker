<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'spotify_id',
        'spotify_display_name',
        'spotify_email',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'total_listening_minutes',
        'last_tracked_at',
        'accepted_terms_version',
        'terms_accepted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'access_token',
        'refresh_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'token_expires_at' => 'datetime',
            'last_tracked_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'total_listening_minutes' => 'integer',
        ];
    }

    /**
     * Check if user has accepted the current version of terms.
     *
     * @return bool
     */
    public function hasAcceptedCurrentTerms(): bool
    {
        return $this->accepted_terms_version === config('terms.version');
    }

    /**
     * Get the listening history for the user.
     */
    public function listeningHistory()
    {
        return $this->hasMany(ListeningHistory::class);
    }

    /**
     * Get the daily listening summaries for the user.
     */
    public function dailyListeningSummaries()
    {
        return $this->hasMany(DailyListeningSummary::class);
    }
}
