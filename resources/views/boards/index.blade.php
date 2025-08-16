@extends('layouts.app')

@section('content')
  <h1 class="text-2xl font-semibold mb-4">Your Boards</h1>

  <form action="{{ route('boards.store') }}" method="POST" class="mb-6 flex gap-2">
    @csrf
    <input name="name" required placeholder="New board name" class="border rounded px-3 py-2 w-64">
    <button class="bg-blue-600 text-white px-3 py-2 rounded">Create Board</button>
  </form>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @forelse($boards as $board)
      <a href="{{ route('boards.show', $board) }}" class="bg-white rounded-xl shadow p-4 hover:shadow-md transition">
        <div class="font-medium">{{ $board->name }}</div>
        <div class="text-xs text-zinc-500 mt-1">Updated {{ $board->updated_at->diffForHumans() }}</div>
      </a>
    @empty
      <div class="text-zinc-500">No boards yet.</div>
    @endforelse
  </div>
@endsection
