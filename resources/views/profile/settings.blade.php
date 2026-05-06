@extends('layouts.app')

@section('content')
<div id="profile-container">

  <!-- CABECERA -->
  <div class="profile-header">
    <div class="profile-banner {{ $user->banner ? '' : 'no-image' }}"
      style="{{ $user->banner ? 'background-image:url(' . $user->banner_url . ');' : '' }}">
      @if(auth()->check() && auth()->id() === $user->id)
      <div class="banner-actions">
        <a href="{{ route('profile.edit') }}" class="banner-btn-p" title="{{ __('Editar perfil') }}">
          <div class="banner-btn-icon"><i class="fa-solid fa-pen"></i></div>
          <span class="banner-btn-text">{{ __('Editar perfil') }}</span>
        </a>
        <a href="{{ route('profile.settings') }}" class="banner-btn-s" title="{{ __('Ajustes') }}">
          <div class="banner-btn-icon"><i class="fa-solid fa-gear"></i></div>
          <span class="banner-btn-text">{{ __('Ajustes') }}</span>
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

    <!-- TABS -->
    <div class="profile-tabs" style="margin-bottom:32px;">
      <button class="profile-tab active" onclick="switchSettings(this,'st-apariencia')">
        <i class="fa-solid fa-palette"></i> {{ __('Apariencia e idioma') }}
      </button>
      <button class="profile-tab" onclick="switchSettings(this,'st-privacidad')">
        <i class="fa-solid fa-lock"></i> {{ __('Privacidad') }}
      </button>
      <button class="profile-tab" onclick="switchSettings(this,'st-cuenta')">
        <i class="fa-solid fa-shield-halved"></i> {{ __('Cuenta') }}
      </button>
    </div>

    <!-- APARIENCIA E IDIOMA -->
    <div id="st-apariencia">
      <form method="POST" action="{{ route('profile.settings.update') }}">
        @csrf
        @method('PUT')
        <div class="profile-card" style="margin-bottom:24px;">
          <div class="edit-section-title">
            <i class="fa-solid fa-sun" style="color:var(--terra)"></i> {{ __('Apariencia') }}
          </div>
          <div class="edit-form-group">
            <label class="edit-form-label">{{ __('Tema de la plataforma') }}</label>
            <div class="settings-option-grid">
              <label class="settings-option {{ ($user->user_settings['tema'] ?? 'claro') === 'claro' ? 'selected' : '' }}">
                <input type="radio" name="settings[tema]" value="claro"
                  {{ ($user->user_settings['tema'] ?? 'claro') === 'claro' ? 'checked' : '' }}>
                <span class="settings-option-ico">☀️</span>
                <span class="settings-option-lbl">{{ __('Modo claro') }}</span>
              </label>
              <label class="settings-option {{ ($user->user_settings['tema'] ?? '') === 'oscuro' ? 'selected' : '' }}">
                <input type="radio" name="settings[tema]" value="oscuro"
                  {{ ($user->user_settings['tema'] ?? '') === 'oscuro' ? 'checked' : '' }}>
                <span class="settings-option-ico">🌙</span>
                <span class="settings-option-lbl">{{ __('Modo oscuro') }}</span>
              </label>
            </div>
          </div>
          <div class="edit-form-group" style="margin-bottom:0;">
            <label class="edit-form-label" for="idioma">{{ __('Idioma de la interfaz') }}</label>
            <select id="idioma" name="settings[idioma]" class="settings-select">
              <option value="es" {{ ($user->user_settings['idioma'] ?? 'es') === 'es' ? 'selected' : '' }}>
                🇪🇸 Español
              </option>
              <option value="en" {{ ($user->user_settings['idioma'] ?? '') === 'en' ? 'selected' : '' }}>
                🇬🇧 English
              </option>
            </select>
          </div>
        </div>
        <div style="display:flex;justify-content:flex-end;margin-bottom:32px;">
          <button type="submit" class="btn-p">
            <i class="fa-solid fa-floppy-disk"></i> {{ __('Guardar preferencias') }}
          </button>
        </div>
      </form>
    </div>

    <!-- PRIVACIDAD -->
    <div id="st-privacidad" style="display:none;">
      <form method="POST" action="{{ route('profile.settings.update') }}">
        @csrf
        @method('PUT')
        <div class="profile-card" style="margin-bottom:24px;">
          <div class="edit-section-title">
            <i class="fa-solid fa-eye" style="color:var(--terra)"></i> {{ __('Visibilidad de datos') }}
          </div>
          <div class="settings-toggle-row">
            <div>
              <div class="settings-toggle-lbl">{{ __('Mostrar apellidos') }}</div>
              <div class="settings-toggle-hint">{{ __('Otros usuarios verán tu nombre completo') }}</div>
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
              <div class="settings-toggle-lbl">{{ __('Mostrar fecha de nacimiento') }}</div>
              <div class="settings-toggle-hint">{{ __('Visible en tu perfil público') }}</div>
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
            <i class="fa-solid fa-floppy-disk"></i> {{ __('Guardar privacidad') }}
          </button>
        </div>
      </form>
    </div>

    <!-- CUENTA -->
    <div id="st-cuenta" style="display:none;">
      <div class="profile-card" style="margin-bottom:44px;">
        <div class="edit-section-title">
          <i class="fa-solid fa-user-shield" style="color:var(--terra)"></i> {{ __('Seguridad') }}
        </div>
        <p style="color:var(--muted);font-size:.9rem;margin-bottom:20px;">
          {{ __('Gestiona tu contraseña y la seguridad de tu cuenta.') }}
        </p>

        <div style="display:flex;flex-direction:column;gap:24px;">

          {{-- CAMBIAR CONTRASEÑA --}}
          <form method="POST" action="{{ route('profile.password') }}">
            @csrf @method('PUT')
            <div style="display:flex;flex-direction:column;gap:12px;max-width:400px;">
              <div class="fg">
                <label class="fl">{{ __('Contraseña actual') }}</label>
                <div style="position:relative;">
                  <input class="fi @error('current_password') input-error @enderror"
                        type="password" name="current_password"
                        placeholder="••••••••" style="padding-right:40px;" required>
                  <button type="button" onclick="togglePassword(this)"
                          style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                                background:none;border:none;cursor:pointer;color:var(--muted);padding:0;">
                    <i class="fa-regular fa-eye"></i>
                  </button>
                </div>
                @error('current_password') <span class="error-msg">{{ $message }}</span> @enderror
              </div>
              <div class="fg">
                <label class="fl">{{ __('Nueva contraseña') }}</label>
                <div style="position:relative;">
                  <input class="fi @error('password') input-error @enderror"
                        type="password" name="password"
                        placeholder="{{ __('Mín. 8 caracteres') }}" style="padding-right:40px;" required>
                  <button type="button" onclick="togglePassword(this)"
                          style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                                background:none;border:none;cursor:pointer;color:var(--muted);padding:0;">
                    <i class="fa-regular fa-eye"></i>
                  </button>
                </div>
                @error('password') <span class="error-msg">{{ $message }}</span> @enderror
              </div>
              <div class="fg">
                <label class="fl">{{ __('Confirmar nueva contraseña') }}</label>
                <div style="position:relative;">
                  <input class="fi" type="password" name="password_confirmation"
                        placeholder="{{ __('Repite tu contraseña') }}" style="padding-right:40px;" required>
                  <button type="button" onclick="togglePassword(this)"
                          style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                                background:none;border:none;cursor:pointer;color:var(--muted);padding:0;">
                    <i class="fa-regular fa-eye"></i>
                  </button>
                </div>
              </div>
              <button type="submit" class="btn-s" style="width:fit-content;">
                <i class="fa-solid fa-key"></i> {{ __('Cambiar contraseña') }}
              </button>
            </div>
          </form>

          <hr style="border:none;border-top:1px solid var(--border);">

          {{-- ELIMINAR CUENTA --}}
          <div>
            <p style="font-size:0.85rem;color:#c0392b;margin-bottom:12px;">
              ⚠️ {{ __('Esta acción es irreversible. Se eliminarán todos tus datos permanentemente.') }}
            </p>
            <form method="POST" action="{{ route('profile.account.destroy') }}"
                  onsubmit="return confirm('{{ __('¿Estás seguro? Esta acción no se puede deshacer.') }}')">
              @csrf @method('DELETE')
              <div style="display:flex;flex-direction:column;gap:12px;max-width:400px;">
                <div class="fg">
                  <label class="fl">{{ __('Introduce tu contraseña para confirmar') }}</label>
                  <div style="position:relative;">
                    <input class="fi @error('confirm_password') input-error @enderror"
                          type="password" name="confirm_password"
                          placeholder="••••••••" style="padding-right:40px;" required>
                    <button type="button" onclick="togglePassword(this)"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                                  background:none;border:none;cursor:pointer;color:var(--muted);padding:0;">
                      <i class="fa-regular fa-eye"></i>
                    </button>
                  </div>
                  @error('confirm_password') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="btn-s" style="width:fit-content;color:#c0392b;border-color:#f5c6a8;">
                  <i class="fa-solid fa-trash"></i> {{ __('Eliminar cuenta') }}
                </button>
              </div>
            </form>
          </div>

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

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tab = '{{ session('tab') }}';
    if (tab) {
      const btn = document.querySelector(`[onclick*="${tab}"]`);
      if (btn) btn.click();
    }
  });
</script>
@endsection