<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    protected $fillable = ['name', 'owner_id', 'start_date', 'end_date', 'priority', 'color'];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot('role');
    }
    public function lists(): HasMany
    {
        return $this->hasMany(BoardList::class)->orderBy('position');
    }
    public function labels(): HasMany
    {
        return $this->hasMany(Label::class);
    }
}
