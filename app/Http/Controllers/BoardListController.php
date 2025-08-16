<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\BoardList;
use Illuminate\Http\Request;

class BoardListController extends Controller
{
    public function store(Request $request, Board $board)
    {
        $this->authorizeBoard($board);
        $data = $request->validate(['name' => 'required|string|max:255']);
        $position = ($board->lists()->max('position') ?? 0) + 1000;
        $board->lists()->create(['name' => $data['name'], 'position' => $position]);
        return back();
    }

    public function rename(Request $request, BoardList $list)
    {
        $this->authorizeBoard($list->board);
        $data = $request->validate(['name' => 'required|string|max:255']);
        $list->update(['name' => $data['name']]);
        return back();
    }

    public function reorder(Request $request, Board $board)
    {
        $this->authorizeBoard($board);
        $ids = $request->validate(['ordered_ids' => 'required|array'])['ordered_ids'];
        foreach ($ids as $i => $id) {
            BoardList::where('id', $id)->where('board_id', $board->id)->update(['position' => ($i + 1) * 1000]);
        }
        return response()->json(['ok' => true]);
    }

    protected function authorizeBoard(Board $board)
    {
        abort_unless($board->owner_id === auth()->id() || $board->members()->where('user_id', auth()->id())->exists(), 403);
    }
}
