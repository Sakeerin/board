<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\BoardList;
use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function store(Request $request, BoardList $list)
    {
        $this->authorizeBoard($list->board);
        $data = $request->validate(['title' => 'required|string|max:255']);
        $position = ($list->cards()->max('position') ?? 0) + 1000;
        $list->cards()->create([
            'title' => $data['title'],
            'position' => $position,
            'owner_id' => auth()->id()
        ]);
        return back();
    }

    public function update(Request $request, Card $card)
    {
        $this->authorizeBoard($card->list->board);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_at' => 'nullable|date'
        ]);
        $card->update($data);
        return back();
    }

    // Drag & drop: move + reorder
    public function reorder(Request $request, Board $board)
    {
        $this->authorizeBoard($board);
        $payload = $request->validate([
            'from_list_id' => 'required|integer',
            'to_list_id'   => 'required|integer',
            'ordered_ids'  => 'required|array', // card IDs in the target list (new order)
        ]);

        // Move all cards in to_list to the new order
        foreach ($payload['ordered_ids'] as $i => $cardId) {
            Card::where('id', $cardId)->update([
                'board_list_id' => $payload['to_list_id'],
                'position' => ($i + 1) * 1000
            ]);
        }
        return response()->json(['ok' => true]);
    }

    public function destroy(Card $card)
    {
        $this->authorizeBoard($card->list->board);
        $card->delete();
        return back();
    }

    protected function authorizeBoard(Board $board)
    {
        abort_unless($board->owner_id === auth()->id() || $board->members()->where('user_id', auth()->id())->exists(), 403);
    }
}
