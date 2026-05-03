@extends('layouts.app')

@section('content')
<div id="profile-container">

  <!-- CABECERA -->
  <div class="profile-header">
    <div class="profile-banner {{ $user->banner ? '' : 'no-image' }}"
      style="{{ $user->banner ? 'background-image:url(' . $user->banner_url . ');' : '' }}">

      <!-- Botones integrados en el banner (Solo para el dueño) -->
      @if(auth()->check() && auth()->id() === $user->id)
      <div class="banner-actions">
        <a href="{{ route('profile.edit') }}" class="banner-btn-p" title="Editar perfil">
          <div class="banner-btn-icon"><i class="fa-solid fa-pen"></i></div>
          <span class="banner-btn-text">Editar perfil</span>
        </a>
        <a href="{{ route('profile.settings') }}" class="banner-btn-s" title="Ajustes">
          <div class="banner-btn-icon"><i class="fa-solid fa-gear"></i></div>
          <span class="banner-btn-text">Ajustes</span>
        </a>
      </div>
      @endif
    </div>
  </div>

  <div style="max-width:700px;margin:0 auto;">

    @if(session('success'))
    <div class="alert-banner" style="margin-bottom:24px;">
      <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif

    <!-- TABS DE AJUSTES -->
    <div class="profile-tabs" style="margin-bottom:32px;">
      <button class="profile-tab active" onclick="switchSettings(this,'st-apariencia')">
        <i class="fa-solid fa-palette"></i> Apariencia e idioma
      </button>
      <button class="profile-tab" onclick="switchSettings(this,'st-privacidad')">
        <i class="fa-solid fa-lock"></i> Privacidad
      </button>
      <button class="profile-tab" onclick="switchSettings(this,'st-cuenta')">
        <i class="fa-solid fa-shield-halved"></i> Cuenta
      </button>
    </div>

    <!-- APARIENCIA E IDIOMA  -->
    <div id="st-apariencia">
      <form method="POST" action="{{ route('profile.settings.update') }}">
        @csrf
        @method('PUT')

        <div class="profile-card" style="margin-bottom:24px;">
          <div class="edit-section-title">
            <i class="fa-solid fa-sun" style="color:var(--terra)"></i> Apariencia
          </div>

          <!-- TEMA -->
          <div class="edit-form-group">
            <label class="edit-form-label">Tema de la plataforma</label>
            <div class="settings-option-grid">
              <label class="settings-option {{ ($user->user_settings['tema'] ?? 'claro') === 'claro' ? 'selected' : '' }}">
                <input type="radio" name="settings[tema]" value="claro"
                  {{ ($user->user_settings['tema'] ?? 'claro') === 'claro' ? 'checked' : '' }}>
                <span class="settings-option-ico">☀️</span>
                <span class="settings-option-lbl">Modo claro</span>
              </label>
              <label class="settings-option {{ ($user->user_settings['tema'] ?? '') === 'oscuro' ? 'selected' : '' }}">
                <input type="radio" name="settings[tema]" value="oscuro"
                  {{ ($user->user_settings['tema'] ?? '') === 'oscuro' ? 'checked' : '' }}>
                <span class="settings-option-ico">🌙</span>
                <span class="settings-option-lbl">Modo oscuro</span>
              </label>
            </div>
          </div>

          <!-- IDIOMA -->
          <div class="edit-form-group" style="margin-bottom:0;">
            <label class="edit-form-label" for="idioma">Idioma de la interfaz</label>
            <select id="idioma" name="settings[idioma]" class="settings-select">
              <option value="es" {{ ($user->user_settings['idioma'] ?? 'es') === 'es' ? 'selected' : '' }}>
                🇪🇸 Español
              </option>
              <option value="en" {{ ($user->user_settings['idioma'] ?? '') === 'en' ? 'selected' : '' }}>
                🇬🇧 Inglés
              </option>
            </select>
          </div>
        </div>

        <div style="display:flex;justify-content:flex-end;margin-bottom:32px;">
          <button type="submit" class="btn-p">
            <i class="fa-solid fa-floppy-disk"></i> Guardar preferencias
          </button>
        </div>
      </form>
    </div>

    <!--  PRIVACIDAD  -->
    <div id="st-privacidad" style="display:none;">
      <form method="POST" action="{{ route('profile.settings.update') }}">
        @csrf
        @method('PUT')
        <div class="profile-card" style="margin-bottom:24px;">
          <div class="edit-section-title">
            <i class="fa-solid fa-eye" style="color:var(--terra)"></i> Visibilidad de datos
          </div>

          <div class="settings-toggle-row">
            <div>
              <div class="settings-toggle-lbl">Mostrar apellidos</div>
              <div class="settings-toggle-hint">Otros usuarios verán tu nombre completo</div>
            </div>
            <label class="toggle-switch">
              <input type="hidden" name="settings[mostrar_apellidos]" value="0">
              <input type="checkbox" name="settings[mostrar_apellidos]" value="1"
                {{ ($user->user_settings['mostrar_apellidos'] ?? '1') == '1' ? 'checked' : '' }}>
              <span class="toggle-thumb"></span>
            </label>
          </div>

          <div class="settings-toggle-row">
            <div>
              <div class="settings-toggle-lbl">Mostrar fecha de nacimiento</div>
              <div class="settings-toggle-hint">Visible en tu perfil público</div>
            </div>
            <label class="toggle-switch">
              <input type="hidden" name="settings[mostrar_fecha]" value="0">
              <input type="checkbox" name="settings[mostrar_fecha]" value="1"
                {{ ($user->user_settings['mostrar_fecha'] ?? '0') == '1' ? 'checked' : '' }}>
              <span class="toggle-thumb"></span>
            </label>
          </div>
        </div>
        <div style="display:flex;justify-content:flex-end;margin-top:24px;">
          <button type="submit" class="btn-p">
            <i class="fa-solid fa-floppy-disk"></i> Guardar privacidad
          </button>
        </div>
      </form>
    </div>

    <!--  CUENTA  -->
    <div id="st-cuenta" style="display:none;">
      <div class="profile-card" style="margin-bottom:44px;">
        <div class="edit-section-title">
          <i class="fa-solid fa-user-shield" style="color:var(--terra)"></i> Seguridad
        </div>
        <p style="color:var(--muted);font-size:.9rem;margin-bottom:20px;">
          Gestiona tu contraseña y la seguridad de tu cuenta.
        </p>
        <div style="display:flex;flex-direction:column;gap:12px;">
          <button class="btn-s" style="width:fit-content;" disabled>
            <i class="fa-solid fa-key"></i> Cambiar contraseña
            <span style="font-size:.75rem;margin-left:6px;opacity:.6">(próximamente)</span>
          </button>
          <button class="btn-s" style="width:fit-content;color:#c0392b;border-color:#f5c6a8;" disabled>
            <i class="fa-solid fa-trash"></i> Eliminar cuenta
            <span style="font-size:.75rem;margin-left:6px;opacity:.6">(próximamente)</span>
          </button>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
  function switchSettings(btn, id) {
    document.querySelectorAll('.profile-tab').forEach(b => b.classList.remove('active'));
    ['st-apariencia', 'st-privacidad', 'st-cuenta'].forEach(s => {
      document.getElementById(s).style.display = 'none';
    });
    btn.classList.add('active');
    document.getElementById(id).style.display = '';
  }

  document.querySelectorAll('.settings-option input[type=radio]').forEach(r => {
    r.addEventListener('change', () => {
      document.querySelectorAll('.settings-option').forEach(o => o.classList.remove('selected'));
      r.closest('.settings-option').classList.add('selected');
    });
  });
</script>
@endsection