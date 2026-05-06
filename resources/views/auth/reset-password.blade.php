@extends('layouts.app')

@section('content')
<div style="display:flex;justify-content:center;align-items:center;min-height:60vh;padding:20px;">
  <div class="profile-card" style="width:100%;max-width:440px;padding:32px;">

    <div style="text-align:center;margin-bottom:24px;">
      <div style="font-size:2.5rem;margin-bottom:8px;">🔐</div>
      <h2 style="margin:0 0 8px;">{{ __('Nueva contraseña') }}</h2>
      <p style="color:var(--muted);font-size:0.9rem;">
        {{ __('Introduce tu nueva contraseña para recuperar el acceso.') }}
      </p>
    </div>

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <input type="hidden" name="email" value="{{ $email }}">

      <div class="fg">
        <label class="fl">{{ __('Nueva contraseña') }}</label>
        <div style="position:relative;">
          <input class="fi @error('password') input-error @enderror"
                 type="password" name="password"
                 placeholder="{{ __('Mín. 8 caracteres') }}"
                 style="padding-right:40px;" required>
          <button type="button" onclick="togglePassword(this)"
                  style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                         background:none;border:none;cursor:pointer;color:var(--muted);padding:0;">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>
        @error('password') <span class="error-msg">{{ $message }}</span> @enderror
      </div>

      <div class="fg">
        <label class="fl">{{ __('Confirmar contraseña') }}</label>
        <div style="position:relative;">
          <input class="fi" type="password" name="password_confirmation"
                 placeholder="{{ __('Repite tu contraseña') }}"
                 style="padding-right:40px;" required>
          <button type="button" onclick="togglePassword(this)"
                  style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                         background:none;border:none;cursor:pointer;color:var(--muted);padding:0;">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-p" style="width:100%;margin-top:8px;">
        {{ __('Restablecer contraseña') }}
      </button>
    </form>

  </div>
</div>
@endsection