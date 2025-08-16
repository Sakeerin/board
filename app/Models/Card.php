<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
    protected $fillable = ['board_list_id', 'title', 'description', 'due_at', 'owner_id', 'position'];
    protected $casts = ['due_at' => 'datetime'];

    public function list(): BelongsTo
    {
        return $this->belongsTo(BoardList::class, 'board_list_id');
    }
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'card_label');
    }
}
