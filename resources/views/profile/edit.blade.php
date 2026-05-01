@extends('layouts.app')

@section('content')
<div id="profile-container">

  <!-- CABECERA -->
  <div class="profile-header" style="margin-bottom:0;">
    <div style="display:flex;align-items:center;gap:14px;max-width:1100px;margin:0 auto 32px;">
      <a href="{{ route('profile.show') }}" class="btn-s" style="padding:8px 16px;font-size:.82rem;">
        <i class="fa-solid fa-arrow-left"></i> Volver al perfil
      </a>
      <h1 style="font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:var(--dark);">
        Editar perfil
      </h1>
    </div>
  </div>

  <div style="max-width:700px;margin:0 auto;">

    <!-- ALERTAS -->
    @if(session('success'))
      <div class="alert-banner" style="margin-bottom:24px;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
      </div>
    @endif
    @if($errors->any())
      <div class="alert-banner alert-banner--error" style="margin-bottom:24px;">
        <i class="fa-solid fa-circle-exclamation"></i>
        <ul style="margin:8px 0 0 18px;padding:0;">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <!-- IMÁGENES -->
      <div class="profile-card" style="margin-bottom:24px;">
        <div class="edit-section-title">
          <i class="fa-solid fa-image" style="color:var(--terra)"></i> Imágenes del perfil
        </div>

        <!-- BANNER -->
        <div class="edit-form-group">
          <label class="edit-form-label">Banner</label>
          <div class="edit-media-preview" id="banner-preview"
               style="{{ $user->banner ? 'background-image:url(' . $user->banner_url . ');' : '' }}">
            @unless($user->banner)
              <div class="edit-media-placeholder">
                <i class="fa-solid fa-image"></i>
                <span>Sin banner</span>
              </div>
            @endunless
          </div>
          <label class="edit-file-label" for="banner">
            <i class="fa-solid fa-upload"></i> Cambiar banner
          </label>
          <input type="file" id="banner" name="banner" accept="image/*"
                 class="edit-file-input" onchange="previewBanner(this)">
          <small class="edit-hint">JPG, PNG o GIF · Máximo 4 MB · Recomendado: 1400 × 350 px</small>
        </div>

        <!-- AVATAR -->
        <div class="edit-form-group" style="margin-bottom:0;">
          <label class="edit-form-label">Foto de perfil</label>
          <div style="display:flex;align-items:center;gap:22px;">
            @if($user->foto_perfil)
              <img id="avatar-preview" class="profile-avatar"
                   style="width:90px;height:90px;"
                   src="{{ $user->foto_perfil_url }}" alt="Avatar actual">
            @else
              <div id="avatar-preview" class="profile-avatar no-image"
                   style="width:90px;height:90px;">🐾</div>
            @endif
            <div>
              <label class="edit-file-label" for="foto_perfil">
                <i class="fa-solid fa-camera"></i> Cambiar foto
              </label>
              <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*"
                     class="edit-file-input" onchange="previewAvatar(this)">
              <small class="edit-hint" style="display:block;margin-top:6px;">
                JPG, PNG o GIF · Máximo 2 MB
              </small>
            </div>
          </div>
        </div>
      </div>

      <!-- DATOS PERSONALES -->
      <div class="profile-card" style="margin-bottom:24px;">
        <div class="edit-section-title">
          <i class="fa-solid fa-user" style="color:var(--terra)"></i> Datos personales
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
          <div class="edit-form-group locked-input-group">
            <label class="edit-form-label" for="nombre">Nombre</label>
            <div class="locked-overlay" title="Por motivos de seguridad, para cambiar tu nombre o apellidos debes contactar con el soporte de Patitas Unidas."></div>
            <input class="edit-form-input" type="text" id="nombre" name="nombre" readonly
                   value="{{ $user->nombre }}" placeholder="Tu nombre">
            <i class="fa-solid fa-lock input-lock-icon"></i>
          </div>
          <div class="edit-form-group locked-input-group">
            <label class="edit-form-label" for="apellidos">Apellidos</label>
            <div class="locked-overlay" title="Por motivos de seguridad, para cambiar tu nombre o apellidos debes contactar con el soporte de Patitas Unidas."></div>
            <input class="edit-form-input" type="text" id="apellidos" name="apellidos" readonly
                   value="{{ $user->apellidos }}" placeholder="Tus apellidos">
            <i class="fa-solid fa-lock input-lock-icon"></i>
          </div>
        </div>

        <div class="edit-form-group">
          <label class="edit-form-label" for="descripcion">Descripción</label>
          <textarea class="edit-form-textarea" id="descripcion" name="descripcion"
                    placeholder="Cuéntanos sobre ti, tu experiencia con animales..."
          >{{ old('descripcion', $user->descripcion) }}</textarea>
          <small class="edit-hint">Máximo 500 caracteres</small>
          @error('descripcion')<span class="edit-field-error">{{ $message }}</span>@enderror
        </div>

        <div class="edit-form-group" style="margin-bottom:0;">
          <label class="edit-form-label" for="fecha_nacimiento">Fecha de nacimiento</label>
          <input class="edit-form-input" type="date" id="fecha_nacimiento"
                 name="fecha_nacimiento" style="max-width:240px;"
                 value="{{ old('fecha_nacimiento', $user->fecha_nacimiento?->format('Y-m-d')) }}">
          @error('fecha_nacimiento')<span class="edit-field-error">{{ $message }}</span>@enderror
        </div>
      </div>

      <!-- UBICACIÓN -->
      <div class="profile-card" style="margin-bottom:32px;">
        <div class="edit-section-title">
          <i class="fa-solid fa-location-dot" style="color:var(--terra)"></i> Ubicación
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
          <div class="edit-form-group" style="margin-bottom:0;">
            <label class="edit-form-label" for="provincia">Provincia</label>
            <input class="edit-form-input" type="text" id="provincia" name="provincia"
                   value="{{ old('provincia', $user->provincia) }}" placeholder="Ej: Madrid">
            @error('provincia')<span class="edit-field-error">{{ $message }}</span>@enderror
          </div>
          <div class="edit-form-group" style="margin-bottom:0;">
            <label class="edit-form-label" for="ciudad">Ciudad</label>
            <input class="edit-form-input" type="text" id="ciudad" name="ciudad"
                   value="{{ old('ciudad', $user->ciudad) }}" placeholder="Ej: Alcobendas">
            @error('ciudad')<span class="edit-field-error">{{ $message }}</span>@enderror
          </div>
        </div>
      </div>

      <!-- ACCIONES -->
      <div style="display:flex;gap:14px;justify-content:flex-end;padding-bottom:44px;">
        <a href="{{ route('profile.show') }}" class="btn-s">Cancelar</a>
        <button type="submit" class="btn-p">
          <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
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
@endsection
