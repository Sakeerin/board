<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Card;
use App\Models\Label;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function store(Request $request, Board $board)
    {
        $this->authorizeBoard($board);
        $data = $request->validate(['name' => 'required|string|max:64', 'color' => 'required|string|max:32']);
        $board->labels()->create($data);
        return back();
    }

    public function attach(Board $board, Card $card, Label $label)
    {
        $this->authorizeBoard($board);
        $card->labels()->syncWithoutDetaching($label->id);
        return back();
    }

    public function detach(Board $board, Card $card, Label $label)
    {
        $this->authorizeBoard($board);
        $card->labels()->detach($label->id);
        return back();
    }

    protected function authorizeBoard(Board $board)
    {
        abort_unless($board->owner_id === auth()->id() || $board->members()->where('user_id', auth()->id())->exists(), 403);
    }
}
