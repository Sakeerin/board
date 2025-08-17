@extends('layouts.app')

@section('content')
  <div class="flex items-center gap-3 mb-6">
    <h1 class="text-2xl font-semibold">Board Settings</h1>
    <a href="{{ route('boards.show', $board) }}" class="ml-auto text-sm underline">Back to board</a>
  </div>

  @if(session('ok'))
    <div class="mb-4 p-3 rounded bg-green-50 text-green-700 border border-green-200">{{ session('ok') }}</div>
  @endif
  @if($errors->any())
    <div class="mb-4 p-3 rounded bg-red-50 text-red-700 border border-red-200">
      <ul class="list-disc pl-5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <div class="grid md:grid-cols-2 gap-6">
    {{-- Left: Board details --}}
    <div class="bg-white rounded-xl border p-4 space-y-4">
      <h2 class="text-lg font-medium">General</h2>
      <form action="{{ route('boards.settings.update', $board) }}" method="POST" class="space-y-4">
        @csrf
        <div>
          <label class="block text-sm font-medium mb-1">Board name</label>
          <input name="name" value="{{ old('name', $board->name) }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Start date</label>
            <input type="date" name="start_date" value="{{ old('start_date', optional($board->start_date)->format('Y-m-d')) }}"
                   class="w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">End date</label>
            <input type="date" name="end_date" value="{{ old('end_date', optional($board->end_date)->format('Y-m-d')) }}"
                   class="w-full border rounded px-3 py-2">
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Priority</label>
          <select name="priority" class="w-full border rounded px-3 py-2">
            <option value="">— None —</option>
            @foreach($priorities as $p)
              <option value="{{ $p }}" @selected(old('priority',$board->priority)===$p)>
                {{ ucwords(str_replace('_',' ', $p)) }}
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Board color</label>
          <input type="color" name="color" value="{{ old('color', $board->color ?? '#ffffff') }}"
                 class="h-10 w-16 p-1 border rounded">
        </div>

        <div class="pt-2">
          <button class="bg-zinc-900 text-white px-4 py-2 rounded">Save changes</button>
        </div>
      </form>
    </div>

    {{-- Right: Members & Owner --}}
    <div class="bg-white rounded-xl border p-4 space-y-4">
      <h2 class="text-lg font-medium">Members</h2>

      <div class="text-sm">
        <div class="font-medium">Owner</div>
        <div class="text-zinc-700">{{ $board->owner->name }} <span class="text-zinc-500">({{ $board->owner->email }})</span></div>
      </div>

      <div class="mt-3">
        <div class="font-medium text-sm mb-2">Current members</div>
        <ul class="space-y-2">
          @forelse($board->members as $m)
            <li class="flex items-center justify-between border rounded px-3 py-2">
              <div>
                <div class="font-medium text-sm">{{ $m->name }}</div>
                <div class="text-xs text-zinc-500">{{ $m->email }}</div>
              </div>
              <div class="flex items-center gap-3">
                <span class="text-xs px-2 py-1 rounded bg-zinc-100 border">{{ $m->pivot->role }}</span>
                @if($board->owner_id === auth()->id() && $m->id !== $board->owner_id)
                  <form action="{{ route('boards.members.remove', [$board,$m]) }}" method="POST"
                        onsubmit="return confirm('Remove this member?')">
                    @csrf @method('DELETE')
                    <button class="text-xs text-red-600 underline">Remove</button>
                  </form>
                @endif
              </div>
            </li>
          @empty
            <li class="text-sm text-zinc-500">No members yet.</li>
          @endforelse
        </ul>
      </div>

      @if($board->owner_id === auth()->id())
      <div class="mt-4">
        <div class="font-medium text-sm mb-2">Invite by email</div>
        <form action="{{ route('boards.invite', $board) }}" method="POST" class="flex items-center gap-2">
          @csrf
          <input type="email" name="email" required placeholder="user@example.com" class="border rounded px-3 py-2 flex-1">
          <select name="role" class="border rounded px-2 py-2">
            <option value="member">member</option>
            <option value="owner">owner</option>
          </select>
          <button class="bg-blue-600 text-white px-3 py-2 rounded">Invite</button>
        </form>
        <p class="text-xs text-zinc-500 mt-2">Only existing users can be invited by email in this simple flow.</p>
      </div>
      @endif
    </div>
  </div>
@endsection
