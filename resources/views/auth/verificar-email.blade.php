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
  ">

    {{-- Logo --}}
    <div class="lm-logo" style="margin-bottom: 8px;">
      <div class="lm-logo-ico">📬</div>
      <span class="lm-logo-txt">PatitasUnidas</span>
    </div>

    {{-- Título --}}
    <h2 style="font-family:'Fraunces',serif;font-size:1.6rem;margin:16px 0 8px;">
      {{ __('Verifica tu correo') }}
    </h2>

    <p style="color:var(--muted);font-size:0.92em;margin-bottom:28px;line-height:1.6;">
      Te hemos enviado un código de 6 dígitos a<br>
      <strong style="color:var(--dark);">{{ session('verificacion_email') }}</strong>.<br>
      Introdúcelo aquí para activar tu cuenta.
    </p>

    {{-- Éxito (reenvío) --}}
    @if(session('success'))
      <div class="flash flash-success" style="margin-bottom:16px;">
        {{ session('success') }}
      </div>
    @endif

    {{-- Error del código --}}
    @if($errors->has('codigo'))
      <div class="flash flash-error" style="margin-bottom:16px;">
        {{ $errors->first('codigo') }}
      </div>
    @endif

    {{-- Formulario --}}
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
          autocomplete="one-time-code"
          placeholder="000000"
          autofocus
          style="font-size:2rem;letter-spacing:12px;text-align:center;font-weight:700;"
        >
      </div>

      <button class="lm-submit" type="submit" style="margin-top:8px;">
        {{ __('Verificar cuenta') }}
      </button>
    </form>

    {{-- Reenviar --}}
    <div style="text-align:center;margin-top:20px;border-top:1px solid var(--border, #eee);padding-top:20px;">
      <p style="color:var(--muted);font-size:0.9em;margin-bottom:10px;">
        ¿No recibiste el código?
      </p>
      <form method="POST" action="{{ route('verificar.email.reenviar') }}">
        @csrf
        <button type="submit" class="btn-s">
          🔁 {{ __('Reenviar código') }}
        </button>
      </form>
    </div>

  </div>
</section>
@endsection