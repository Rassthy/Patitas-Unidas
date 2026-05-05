@extends('layouts.app')

@section('content')
<div id="profile-container">

  <!-- CABECERA -->
  <div class="profile-header" style="margin-bottom:0;">
    <div style="display:flex;align-items:center;gap:14px;max-width:1100px;margin:0 auto 32px;">
      <a href="{{ route('profile.show') }}" class="btn-s" style="padding:8px 16px;font-size:.82rem;">
        <i class="fa-solid fa-arrow-left"></i> {{ __('Volver al perfil') }}
      </a>
      <h1 style="font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:var(--dark);">
        {{ __('Editar perfil') }}
      </h1>
    </div>
  </div>

  <div style="max-width:700px;margin:0 auto;">

    @if(session('success'))
    <div class="alert-banner" style="margin-bottom:24px;">
      <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <!-- IMÁGENES -->
      <div class="profile-card" style="margin-bottom:24px;">
        <div class="edit-section-title">
          <i class="fa-solid fa-image" style="color:var(--terra)"></i> {{ __('Imágenes del perfil') }}
        </div>

        <!-- BANNER -->
        <div class="edit-form-group">
          <label class="edit-form-label">{{ __('Banner') }}</label>
          <div class="edit-media-preview" id="banner-preview"
            style="{{ $user->banner ? 'background-image:url(' . $user->banner_url . ');' : '' }}">
            @unless($user->banner)
            <div class="edit-media-placeholder">
              <i class="fa-solid fa-image"></i>
              <span>{{ __('Sin banner') }}</span>
            </div>
            @endunless
          </div>
          <label class="edit-file-label" for="banner">
            <i class="fa-solid fa-upload"></i> {{ __('Cambiar banner') }}
          </label>
          <input type="file" id="banner" name="banner" accept="image/*"
            class="edit-file-input" onchange="previewBanner(this)">
          <small class="edit-hint">JPG, PNG o GIF · Máximo 4 MB · {{ __('Recomendado: 1400 × 350 px') }}</small>
        </div>

        <!-- AVATAR -->
        <div class="edit-form-group" style="margin-bottom:0;">
          <label class="edit-form-label">{{ __('Foto de perfil') }}</label>
          <div style="display:flex;align-items:center;gap:22px;">
            <img id="avatar-preview" class="profile-avatar"
              style="width:90px;height:90px;object-fit:cover;"
              src="{{ $user->foto_perfil_url }}" alt="Avatar de {{ $user->nombre }}">
            <div>
              <label class="edit-file-label" for="foto_perfil">
                <i class="fa-solid fa-camera"></i> {{ __('Cambiar foto') }}
              </label>
              <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*"
                     class="edit-file-input" onchange="previewAvatar(this)">
              <small class="edit-hint" style="display:block;margin-top:6px;">JPG, PNG o GIF · Máximo 2 MB</small>
            </div>
          </div>
        </div>

        <!-- DATOS PERSONALES -->
        <div class="profile-card" style="margin-bottom:24px;">
          <div class="edit-section-title">
            <i class="fa-solid fa-user" style="color:var(--terra)"></i> {{ __('Datos personales') }}
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
            <div class="edit-form-group locked-input-group">
              <label class="edit-form-label" for="nombre">{{ __('Nombre') }}</label>
              <div class="locked-overlay"></div>
              <input class="edit-form-input" type="text" id="nombre" name="nombre" readonly
                value="{{ $user->nombre }}" placeholder="{{ __('Nombre') }}">
              <i class="fa-solid fa-lock input-lock-icon"></i>
            </div>
            <div class="edit-form-group locked-input-group">
              <label class="edit-form-label" for="apellidos">{{ __('Apellidos') }}</label>
              <div class="locked-overlay"></div>
              <input class="edit-form-input" type="text" id="apellidos" name="apellidos" readonly
                value="{{ $user->apellidos }}" placeholder="{{ __('Apellidos') }}">
              <i class="fa-solid fa-lock input-lock-icon"></i>
            </div>
          </div>

          <div class="edit-form-group">
            <label class="edit-form-label" for="descripcion">{{ __('Descripción') }}</label>
            <textarea class="edit-form-textarea @error('descripcion') is-invalid @enderror"
                      id="descripcion" name="descripcion"
                      placeholder="{{ __('Cuéntanos sobre ti, tu experiencia con animales...') }}">{{ old('descripcion', $user->descripcion) }}</textarea>
            <small class="edit-hint">{{ __('Máximo 500 caracteres') }}</small>
            @error('descripcion')
              <span class="edit-field-error">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
              </span>
            @enderror
          </div>

          <div class="edit-form-group" style="margin-bottom:0;">
            <label class="edit-form-label" for="fecha_nacimiento">{{ __('Fecha de nacimiento') }}</label>
            <input class="edit-form-input @error('fecha_nacimiento') is-invalid @enderror"
                  type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                  style="max-width:240px;"
                  value="{{ old('fecha_nacimiento', $user->fecha_nacimiento?->format('Y-m-d')) }}">
            @error('fecha_nacimiento')
              <span class="edit-field-error" style="color:var(--terra);display:block;margin-top:5px;font-weight:600;">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
              </span>
            @enderror
          </div>
        </div>

        <!-- UBICACIÓN -->
        <div class="profile-card" style="margin-bottom:32px;">
          <div class="edit-section-title">
            <i class="fa-solid fa-location-dot" style="color:var(--terra)"></i> {{ __('Ubicación') }}
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
            <div class="edit-form-group" style="margin-bottom:0;">
              <label class="edit-form-label" for="provincia">{{ __('Provincia') }}</label>
              <input class="edit-form-input" type="text" id="provincia" name="provincia"
                value="{{ old('provincia', $user->provincia) }}" placeholder="Ej: Madrid">
              @error('provincia')<span class="edit-field-error">{{ $message }}</span>@enderror
            </div>
            <div class="edit-form-group" style="margin-bottom:0;">
              <label class="edit-form-label" for="ciudad">{{ __('Ciudad') }}</label>
              <input class="edit-form-input" type="text" id="ciudad" name="ciudad"
                value="{{ old('ciudad', $user->ciudad) }}" placeholder="Ej: Alcobendas">
              @error('ciudad')<span class="edit-field-error">{{ $message }}</span>@enderror
            </div>
          </div>
        </div>

        <!-- ACCIONES -->
        <div style="display:flex;gap:14px;justify-content:flex-end;padding-bottom:44px;">
          <a href="{{ route('profile.show') }}" class="btn-s">{{ __('Cancelar') }}</a>
          <button type="submit" class="btn-p">
            <i class="fa-solid fa-floppy-disk"></i> {{ __('Guardar cambios') }}
          </button>
        </div>

    </form>
  </div>
</div>

<script>
  function previewAvatar(input) {
    if (!input.files?.[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
      const el = document.getElementById('avatar-preview');
      if (el.tagName === 'IMG') {
        el.src = e.target.result;
      } else {
        const img = document.createElement('img');
        img.id = 'avatar-preview';
        img.className = 'profile-avatar';
        img.style.cssText = 'width:90px;height:90px;';
        img.src = e.target.result;
        el.replaceWith(img);
      }
    };
    reader.readAsDataURL(input.files[0]);
  }

  function previewBanner(input) {
    if (!input.files?.[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
      const el = document.getElementById('banner-preview');
      el.style.backgroundImage = `url(${e.target.result})`;
      el.querySelector('.edit-media-placeholder')?.remove();
    };
    reader.readAsDataURL(input.files[0]);
  }
</script>

@if($errors->any())
<script>
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof showToast === 'function') {
      showToast('Revisa los campos marcados en rojo.', 'error');
    }
  });
</script>
@endif
@endsection