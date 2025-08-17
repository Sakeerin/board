<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\User;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $boards = auth()->user()
        //     ->belongsToMany(Board::class, 'board_user') // via pivot
        //     ->getQuery()
        //     ->get()
        //     ->merge(Board::where('owner_id', auth()->id())->get())
        //     ->unique('id');
        $boards = auth()->user()
            ->belongsToMany(\App\Models\Board::class, 'board_user')
            ->getQuery()
            ->get()
            ->merge(\App\Models\Board::where('owner_id', auth()->id())->get())
            ->unique('id')
            ->sortByDesc('updated_at')
            ->values();

        // Eager-load owner on the collection to avoid N+1 queries
        $boards->load('owner');
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

    public function settings(Board $board)
    {
        $this->authorizeBoard($board);
        $board->load(['owner', 'members']);
        $priorities = ['general', 'medium', 'urgent', 'very_urgent'];
        return view('boards.settings', compact('board', 'priorities'));
    }

    public function updateSettings(Request $request, Board $board)
    {
        $this->authorizeBoard($board);
        $this->abortIfNotOwner($board); // only owner can change settings

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'priority'   => 'nullable|in:general,medium,urgent,very_urgent',
            'color'      => ['nullable', 'regex:/^#([0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/'],
        ]);

        $board->update($data);
        return redirect()->route('boards.settings', $board)->with('ok', 'Board updated.');
    }

    public function invite(Request $request, Board $board)
    {
        $this->authorizeBoard($board);
        $this->abortIfNotOwner($board);

        $data = $request->validate([
            'email' => 'required|email',
            'role'  => 'nullable|in:owner,member', // default member
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            return back()->withErrors(['email' => 'No user found with that email.']);
        }

        if ($user->id === $board->owner_id) {
            return back()->withErrors(['email' => 'This user is already the owner.']);
        }

        $board->members()->syncWithoutDetaching([$user->id => ['role' => $data['role'] ?? 'member']]);

        return back()->with('ok', 'User invited/added.');
    }

    public function removeMember(Board $board, User $user)
    {
        $this->authorizeBoard($board);
        $this->abortIfNotOwner($board);

        if ($user->id === $board->owner_id) {
            return back()->withErrors(['member' => 'Owner cannot be removed.']);
        }

        $board->members()->detach($user->id);
        return back()->with('ok', 'Member removed.');
    }

    // protected function authorizeBoard(Board $board)
    // {
    //     abort_unless(
    //         $board->owner_id === auth()->id() || $board->members()->where('user_id', auth()->id())->exists(),
    //         403
    //     );
    // }

    protected function abortIfNotOwner(Board $board)
    {
        abort_unless($board->owner_id === auth()->id(), 403);
    }

    protected function authorizeBoard(Board $board)
    {
        abort_unless($board->owner_id === auth()->id() || $board->members()->where('user_id', auth()->id())->exists(), 403);
    }
}
