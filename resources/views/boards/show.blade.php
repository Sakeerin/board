@extends('layouts.app')

@section('content')
  <div class="flex items-center gap-3 mb-4">
    <h1 class="text-xl font-semibold">{{ $board->name }}</h1>
    <form action="{{ route('boards.destroy', $board) }}" method="POST" class="ml-auto">
      @csrf @method('DELETE')
      <button class="text-sm text-red-600 underline">Delete board</button>
    </form>
  </div>

  <div class="mb-4">
    <form action="{{ route('lists.store', $board) }}" method="POST" class="flex gap-2">
      @csrf
      <input name="name" required placeholder="Add list" class="border rounded px-3 py-2">
      <button class="bg-zinc-900 text-white px-3 py-2 rounded">Add</button>
    </form>
  </div>

  <div id="board" class="flex gap-4 overflow-x-auto pb-4">
    @foreach($board->lists as $list)
      <div class="w-80 shrink-0" data-list-id="{{ $list->id }}">
        <div class="bg-zinc-50 border rounded-xl p-3">
          <div class="flex items-center justify-between mb-2">
            <form action="{{ route('lists.rename', $list) }}" method="POST" class="flex-1">
              @csrf
              <input name="name" value="{{ $list->name }}" class="font-medium bg-transparent w-full">
            </form>
          </div>
          <div class="space-y-2 min-h-[20px] card-list" data-list-id="{{ $list->id }}">
            @foreach($list->cards as $card)
              <div class="bg-white rounded-lg shadow p-3 card" data-card-id="{{ $card->id }}">
                <div class="font-medium">{{ $card->title }}</div>
                <div class="mt-2 flex flex-wrap gap-1">
                  @foreach($card->labels as $label)
                    <span class="text-xs px-2 py-0.5 rounded bg-{{ $label->color }}/20 text-{{ $label->color }}">● {{ $label->name }}</span>
                  @endforeach
                </div>
                @if($card->due_at)
                  <div class="text-xs text-zinc-500 mt-2">Due {{ $card->due_at->diffForHumans() }}</div>
                @endif
              </div>
            @endforeach
          </div>

          <form action="{{ route('cards.store', $list) }}" method="POST" class="mt-3">
            @csrf
            <input name="title" placeholder="Add a card" class="border rounded px-2 py-2 w-full">
          </form>
        </div>
      </div>
    @endforeach
  </div>

  <script type="module">
    // import Sortable from 'sortablejs' // if installed via npm; otherwise include CDN and remove this line

    const csrf = document.querySelector('meta[name="csrf-token"]').content
    const boardId = {{ $board->id }}

    // Make each list sortable and connected
    document.querySelectorAll('.card-list').forEach(el => {
      new Sortable(el, {
        group: 'cards',
        animation: 150,
        onEnd: async (evt) => {
          const toListId = evt.to.dataset.listId
          const fromListId = evt.from.dataset.listId
          const orderedIds = [...evt.to.querySelectorAll('.card')].map(c => c.dataset.cardId)

          await fetch(`{{ url('/boards') }}/${boardId}/cards/reorder`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ from_list_id: fromListId, to_list_id: toListId, ordered_ids: orderedIds })
          })
        }
      })
    })

    // Reordering whole lists horizontally (optional bonus)
    new Sortable(document.getElementById('board'), {
      group: 'lists',
      draggable: '[data-list-id]',
      animation: 150,
      onEnd: async () => {
        const ordered = [...document.querySelectorAll('[data-list-id]')].map(el => el.dataset.listId)
        await fetch(`{{ route('lists.reorder', $board) }}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
          body: JSON.stringify({ ordered_ids: ordered })
        })
      }
    })
  </script>
@endsection
