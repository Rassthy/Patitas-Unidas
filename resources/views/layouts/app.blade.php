<!DOCTYPE html>
<html lang="es">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PatitasUnidas</title>

  <!-- Fuentes -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <!-- Iconos -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- CSS -->
  <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
  <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sections.css') }}">
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
</head>

<body>

@include('layouts.sidebar')
@include('layouts.header')
@include('components.chat-panel')

@if (session('success') || session('error'))
  <div class="flash-messages">
    @if (session('success'))
      <div class="flash flash-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="flash flash-error">{{ session('error') }}</div>
    @endif
  </div>
@endif

<main id="mainContent">
    @yield('content')
</main>

@include('components.modals')
@include('components.login-modal')
@include('components.toast')

<!-- JS -->
<!-- <script src="{{ asset('js/api.js') }}"></script> -->
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>

</body>
</html>