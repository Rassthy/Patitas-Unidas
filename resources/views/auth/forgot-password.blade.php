@extends('layouts.app')

@section('content')
<div style="display:flex;justify-content:center;align-items:center;min-height:60vh;padding:20px;">
  <div class="profile-card" style="width:100%;max-width:440px;padding:32px;">

    <div style="text-align:center;margin-bottom:24px;">
      <div style="font-size:2.5rem;margin-bottom:8px;">🔑</div>
      <h2 style="margin:0 0 8px;">{{ __('¿Olvidaste tu contraseña?') }}</h2>
      <p style="color:var(--muted);font-size:0.9rem;">
        {{ __('Introduce tu correo y te enviaremos un enlace para restablecerla.') }}
      </p>
    </div>

    @if(session('success'))
      <div class="alert-success" style="background:#d4edda;color:#155724;padding:12px 16px;
           border-radius:8px;margin-bottom:16px;font-size:0.9rem;">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="fg">
        <label class="fl">{{ __('Correo electrónico') }}</label>
        <input class="fi @error('email') input-error @enderror"
               type="email" name="email"
               value="{{ old('email') }}"
               placeholder="tucorreo@ejemplo.com" required>
        @error('email') <span class="error-msg">{{ $message }}</span> @enderror
      </div>

      <button type="submit" class="btn-p" style="width:100%;margin-top:8px;">
        {{ __('Enviar enlace de recuperación') }}
      </button>
    </form>

    <div style="text-align:center;margin-top:20px;font-size:0.875rem;color:var(--muted);">
      <a href="{{ route('home') }}" style="color:var(--terra);text-decoration:none;">
        ← {{ __('Volver al inicio') }}
      </a>
    </div>
  </div>
</div>
@endsection