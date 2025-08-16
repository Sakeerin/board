<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Card $card)
    {
        $this->authorizeBoard($card->list->board);
        $data = $request->validate(['body' => 'required|string']);
        $card->comments()->create(['user_id' => auth()->id(), 'body' => $data['body']]);
        return back();
    }

    protected function authorizeBoard($board)
    {
        abort_unless($board->owner_id === auth()->id() || $board->members()->where('user_id', auth()->id())->exists(), 403);
    }
}
