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
      <input type="color" name="color" value="#f8fafc" title="List color" class="h-10 w-10 p-1 border rounded">
      <button class="bg-zinc-900 text-white px-3 py-2 rounded">Add</button>
    </form>
  </div>

  <div id="board" class="flex gap-4 overflow-x-auto pb-4">
    @foreach($board->lists as $list)
      <div class="w-80 shrink-0" data-list-id="{{ $list->id }}">
        @php $listBg = $list->color ?: '#f8fafc'; @endphp
        <div class="border rounded-xl p-3" style="background-color: {{ $listBg }};">
          <div class="flex items-center justify-between mb-2">
            <form action="{{ route('lists.rename', $list) }}" method="POST" class="flex items-center gap-2 flex-1">
              @csrf
              <input name="name" value="{{ $list->name }}" class="font-medium bg-transparent w-full">
              <input type="color" name="color" value="{{ $list->color ?? '#f8fafc' }}" class="h-8 w-8 border rounded"
                    onchange="this.form.submit()" title="Pick list color">
            </form>
          </div>
          <div class="space-y-2 min-h-[20px] card-list" data-list-id="{{ $list->id }}">
            @foreach($list->cards as $card)
              @php
              $cardColor = $card->color ?: '#ffffff';
              $leftBar = $card->color ?: '#e5e7eb'; // default gray if no color
              // Optional faint background using 8-digit hex (20% alpha = 33). Works in modern browsers.
              $bgSoft = $card->color ? ($card->color . '33') : '#ffffff';
            @endphp

            <div class="rounded-lg shadow p-3 card"
                data-card-id="{{ $card->id }}"
                style="border-left: 6px solid {{ $leftBar }}; background-color: {{ $bgSoft }};">
              <div class="flex items-start gap-2">
                <div class="flex-1">
                  <div class="font-medium">{{ $card->title }}</div>
                  {{-- labels, due date stay the same --}}
                  <div class="mt-2 flex flex-wrap gap-1">
                    @foreach($card->labels as $label)
                      <span class="text-xs px-2 py-0.5 rounded bg-{{ $label->color }}/20 text-{{ $label->color }}">● {{ $label->name }}</span>
                    @endforeach
                  </div>
                  @if($card->due_at)
                    <div class="text-xs text-zinc-600 mt-2">Due {{ $card->due_at->diffForHumans() }}</div>
                  @endif
                </div>

                {{-- Small color picker to update the card color --}}
                <form action="{{ route('cards.update', $card) }}" method="POST">
                  @csrf
                  <input type="color" name="color" value="{{ $card->color ?? '#ffffff' }}" class="h-6 w-6 border rounded"
                        onchange="this.form.submit()" title="Change card color">
                </form>
              </div>
            </div>

            @endforeach
          </div>

          <form action="{{ route('cards.store', $list) }}" method="POST" class="mt-3 flex gap-2">
            @csrf
            <input name="title" placeholder="Add a card" class="border rounded px-2 py-2 w-full">
            <input type="color" name="color" value="#ffffff" class="h-10 w-10 p-1 border rounded" title="Card color">
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
  <script>
  document.querySelectorAll('[data-auto-contrast]').forEach(el => {
    const bg = getComputedStyle(el).backgroundColor; // "rgb(r, g, b)"
    const [r,g,b] = bg.match(/\d+/g).map(Number);
    const yiq = ((r*299)+(g*587)+(b*114))/1000;
    el.style.color = yiq >= 128 ? '#111827' : '#f9fafb'; // dark text on light bg, light text on dark bg
  });
  </script>
@endsection
