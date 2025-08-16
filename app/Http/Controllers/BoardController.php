<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $boards = auth()->user()
            ->belongsToMany(Board::class, 'board_user') // via pivot
            ->getQuery()
            ->get()
            ->merge(Board::where('owner_id', auth()->id())->get())
            ->unique('id');
        return view('boards.index', compact('boards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $board = Board::create(['name' => $data['name'], 'owner_id' => auth()->id()]);
        $board->members()->attach(auth()->id(), ['role' => 'owner']);
        return redirect()->route('boards.show', $board);
    }

    /**
     * Display the specified resource.
     */
    public function show(Board $board)
    {
        $this->authorizeBoard($board);
        $board->load(['lists.cards.labels', 'labels']);
        return view('boards.show', compact('board'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Board $board)
    {
        $this->authorizeBoard($board);
        $board->delete();
        return redirect()->route('boards.index');
    }

    protected function authorizeBoard(Board $board)
    {
        abort_unless($board->owner_id === auth()->id() || $board->members()->where('user_id', auth()->id())->exists(), 403);
    }
}
