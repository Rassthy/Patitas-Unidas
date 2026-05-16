@extends('layouts.app')

@section('content')
<section class="section section-verify">

  <div style="
    max-width: 460px;
    margin: 60px auto;
    background: #fff;
    border-radius: 20px;
    padding: 48px 40px;
    box-shadow: var(--sh-l);
    text-align: center;
  ">

    <div class="lm-logo" style="margin-bottom: 15px; display: flex; justify-content: center;">
      <img src="{{ asset('img/defaults/h-logo.png') }}" alt="PatitasUnidas" style="height: 50px; width: auto; object-fit: contain;">
    </div>

    <h2 style="font-family:'Fraunces',serif;font-size:1.6rem;margin:16px 0 8px;">
      {{ __('Verifica tu correo') }}
    </h2>

    {{-- ALERTAS GENERALES --}}
    @if(session('success'))
    <div class="flash flash-success" style="margin-bottom:16px;">
      {{ session('success') }}
    </div>
    @endif

    @if($errors->any() && !$errors->has('codigo'))
      <div class="flash flash-error" style="margin-bottom:16px;">
        {{ $errors->first() }}
      </div>
    @endif

    {{-- CONTROL DE SESIÓN ACTIVA (Desde Hotfix) --}}
    @if(session('verificacion_email'))
        
        <p style="color:var(--muted);font-size:0.92em;margin-bottom:28px;line-height:1.6;">
          Te hemos enviado un código de 6 dígitos a<br>
          <strong style="color:var(--dark);">{{ session('verificacion_email') }}</strong>.<br>
          Introdúcelo aquí para activar tu cuenta.
        </p>

        <div style="text-align: center; margin-bottom: 24px;">
            <a href="#" onclick="document.getElementById('form-cambiar-email').style.display='block'; return false;" style="color: var(--primary); font-size: 0.85rem; text-decoration: underline;">
                ¿Te has equivocado de correo? Cámbialo aquí
            </a>
        </div>

        <form id="form-cambiar-email" action="{{ route('verificar.cambiar.email') }}" method="POST" style="display: none; background: var(--bg-alt, #f9f9f9); border: 1px solid var(--border, #eee); padding: 15px; border-radius: 8px; margin-bottom: 24px;">
            @csrf
            <div class="fg" style="margin-bottom: 12px;">
                <label class="fl" style="font-size: 0.85rem;">Nuevo correo electrónico</label>
                <input type="email" name="nuevo_email" class="fi" placeholder="nuevo@correo.com" required>
            </div>
            <button type="submit" class="lm-submit" style="width: 100%; padding: 10px; font-size: 0.9rem;">
                Actualizar y reenviar código
            </button>
        </form>

        <form method="POST" action="{{ route('verificar.email') }}">
          @csrf
          <div class="fg">
            <label class="fl">{{ __('Código de verificación') }}</label>
            <input
              class="fi @error('codigo') input-error @enderror"
              type="text"
              name="codigo"
              maxlength="6"
              inputmode="numeric"
              pattern="[0-9]{6}"
              value="{{ old('codigo') }}"
              autocomplete="one-time-code"
              placeholder="000000"
              autofocus
              style="font-size:2rem;letter-spacing:12px;text-align:center;font-weight:700;"
            >

            @error('codigo')
              <span style="color: #e74c3c; font-size: 0.85rem; margin-top: 5px; display: block; text-align: center; font-weight: 600;">
                {{ $message }}
              </span>
            @enderror
          </div>

          <button class="lm-submit" type="submit" style="margin-top:8px;">
            {{ __('Verificar cuenta') }}
          </button>
        </form>

        <div style="text-align:center;margin-top:20px;border-top:1px solid var(--border, #eee);padding-top:20px;">
          <p style="color:var(--muted);font-size:0.9em;font-weight:bold;margin-bottom:10px;">
            ¿No recibiste el código?
          </p>
          <form method="POST" action="{{ route('verificar.email.reenviar') }}">
            @csrf
            <button type="submit" class="btn-s" style="display: inline-flex; align-items: center; gap: 8px; justify-content: center;">
              <x-icons.recarga /> {{ __('Reenviar código') }}
            </button>
          </form>
        </div>

    @else
        <div style="text-align:center; padding: 20px 0;">
            <p style="color:var(--muted);font-size:0.95em;margin-bottom:20px;">
                No hay ninguna verificación pendiente o tu sesión ha caducado.
            </p>
            <a href="{{ route('home') }}" class="lm-submit" style="display:inline-block; text-decoration:none;">
                Volver al inicio
            </a>
        </div>
    @endif

  </div>
</section>
@endsection