<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'TrelloClone') }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-100 text-zinc-900">
  <nav class="bg-white shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-4">
      <a href="{{ route('boards.index') }}" class="font-bold">TrelloClone</a>
      <div class="ml-auto flex items-center gap-3">
        <span class="text-sm">{{ auth()->user()->name }}</span>
        <form action="{{ route('logout') }}" method="POST">@csrf<button class="text-sm underline">Logout</button></form>
      </div>
    </div>
  </nav>
  <main class="max-w-7xl mx-auto px-4 py-6">
    @yield('content')
  </main>
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
</body>
</html>
