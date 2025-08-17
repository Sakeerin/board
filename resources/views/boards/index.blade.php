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
      @php
        $bg = $board->color ?: '#ffffff';
      @endphp
      <div class="relative bg-white rounded-xl shadow hover:shadow-md transition">
        <a href="{{ route('boards.show', $board) }}" class="block p-4 rounded-xl" style="background: {{ $bg }};">
          <div class="font-medium">{{ $board->name }}</div>
          <div class="text-xs text-zinc-600 mt-1">Updated {{ $board->updated_at->diffForHumans() }}</div>
        </a>

        {{-- Settings (top-right gear) --}}
        <a href="{{ route('boards.settings', $board) }}"
           class="absolute top-2 right-2 inline-flex items-center justify-center h-8 w-8 rounded-lg bg-white/80 hover:bg-white border">
          {{-- simple gear svg --}}
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M10.325 4.317a1 1 0 011.35-.936l1.387.463a1 1 0 00.95-.174l1.1-.85a1 1 0 011.497.28l.8 1.387a1 1 0 00.746.5l1.486.248a1 1 0 01.832 1.16l-.248 1.486a1 1 0 00.5.747l1.386.8a1 1 0 01.281 1.497l-.85 1.1a1 1 0 00-.174.95l.463 1.387a1 1 0 01-.936 1.35l-1.496.25a1 1 0 00-.746.5l-.8 1.386a1 1 0 01-1.497.281l-1.1-.85a1 1 0 00-.95-.174l-1.387.463a1 1 0 01-1.35-.936l-.25-1.496a1 1 0 00-.5-.746l-1.386-.8a1 1 0 01-.281-1.497l.85-1.1a1 1 0 00.174-.95l-.463-1.387a1 1 0 01.936-1.35l1.496-.25a1 1 0 00.746-.5l.8-1.386z" />
            <circle cx="12" cy="12" r="3" stroke-width="2"></circle>
          </svg>
        </a>

        {{-- Owner pill (just under the gear) --}}
        @if($board->owner)
          <div class="absolute right-2 top-12 pointer-events-none">
            <div class="text-[11px] leading-none text-zinc-700 bg-white/90 backdrop-blur rounded border px-2 py-1 shadow-sm">
              <span class="font-medium">Owner:</span>
              {{ $board->owner->name }}
              {{-- uncomment to show email as well: --}}
              {{-- <span class="text-zinc-500">({{ $board->owner->email }})</span> --}}
            </div>
          </div>
        @endif
      </div>
    @empty
      <div class="text-zinc-500">No boards yet.</div>
    @endforelse
  </div>
@endsection
