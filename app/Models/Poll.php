<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    protected $fillable = [
        'title',
        'question',
        'is_draft',
        'allow_multiple_choices',
        'results_public',
        'started_at',
        'ends_at',
        'secret_token',
        'user_id',
    ];

    protected $casts = [
        'is_draft' => 'boolean',
        'allow_multiple_choices' => 'boolean',
        'results_public' => 'boolean',
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }
}
