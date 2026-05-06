<!-- LOGIN MODAL -->
<div class="login-overlay" id="loginOverlay" onclick="closeLoginModal(event)">
  <div class="login-modal" onclick="event.stopPropagation()">

    <button class="lm-close" onclick="closeLoginModal()">
      <i class="fa-solid fa-xmark"></i>
    </button>

    <!-- LOGO -->
    <div class="lm-logo">
      <div class="lm-logo-ico">🐾</div>
      <span class="lm-logo-txt">PatitasUnidas</span>
    </div>

    <!-- TABS -->
    <div class="lm-tabs">
      <button class="lm-tab active" onclick="setLoginTab(this,'login')">{{ __('Iniciar sesión') }}</button>
      <button class="lm-tab" onclick="setLoginTab(this,'register')">{{ __('Registrarse') }}</button>
    </div>

    @php
      $isLoginAttempt = old('login') !== null;
      $isRegisterAttempt = (old('username') !== null || old('nombre') !== null) && !request()->routeIs('profile.*');
      $openModal = ($errors->any() && !request()->routeIs('profile.*')) || $isLoginAttempt || $isRegisterAttempt;
    @endphp

    <!-- LOGIN FORM -->
    <div id="loginForm">
      <h2 class="lm-h2">{{ __('Bienvenido de vuelta 👋') }}</h2>

      <form method="POST" action="{{ route('login') }}" id="loginFormEl">
        @csrf

        <div class="fg">
          <label class="fl">{{ __('Usuario o Correo electrónico') }}</label>
          <input class="fi @error('login') input-error @enderror"
                 type="text" name="login" value="{{ old('login') }}"
                 placeholder="{{ __('tu@email.com o @tunombre') }}" required>
          @error('login') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        <div class="fg">
          <label class="fl">{{ __('Contraseña') }}</label>
          <div style="position:relative;">
            <input class="fi @error('password') input-error @enderror"
                  type="password" name="password" placeholder="••••••••" required
                  style="padding-right:40px;">
            <button type="button" onclick="togglePassword(this)"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                          background:none;border:none;cursor:pointer;color:var(--muted);padding:0;">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
          @error('password') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        <div class="fg" style="display:flex;align-items:center;gap:8px;">
          <input type="checkbox" name="remember" id="rememberLogin" style="width:16px;cursor:pointer;">
          <label for="rememberLogin" style="margin:0;cursor:pointer;font-size:0.9em;">{{ __('Recuérdame') }}</label>
        </div>

        <button class="lm-submit" type="submit">{{ __('Entrar a PatitasUnidas') }}</button>
      </form>

      <p class="lm-footer">
        <a href="{{ route('password.request') }}">{{ __('¿Olvidaste tu contraseña?') }}</a>
      </p>
    </div>

    <!-- REGISTER FORM -->
    <div id="registerForm" class="hidden">
      <h2 class="lm-h2">{{ __('Crea tu cuenta 🐾') }}</h2>

      <!-- Selector tipo de cuenta -->
      <div style="display:flex;gap:8px;margin-bottom:16px;">
        <button type="button" id="tipoUsuarioBtn" onclick="setRegisterTipo('usuario')"
          style="flex:1;padding:10px;border:2px solid var(--terra);border-radius:var(--r-s);
                 background:var(--terra);color:#fff;cursor:pointer;font-weight:600;font-size:0.85rem;">
          👤 {{ __('Usuario') }}
        </button>
        <button type="button" id="tipoOrgBtn" onclick="setRegisterTipo('organizacion')"
          style="flex:1;padding:10px;border:2px solid var(--terra);border-radius:var(--r-s);
                 background:transparent;color:var(--terra);cursor:pointer;font-weight:600;font-size:0.85rem;">
          🏥 {{ __('Organización') }}
        </button>
      </div>

      <!-- FORM USUARIO -->
      <form method="POST" action="{{ route('register') }}" id="registerFormUsuario">
        @csrf
        <input type="hidden" name="tipo" value="usuario">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div class="fg">
            <label class="fl">{{ __('Nombre') }}</label>
            <input class="fi @error('nombre') input-error @enderror" type="text" name="nombre"
                   value="{{ old('nombre') }}" placeholder="{{ __('Nombre') }}" required>
            @error('nombre') <span class="error-msg">{{ $message }}</span> @enderror
          </div>
          <div class="fg">
            <label class="fl">{{ __('Apellidos') }}</label>
            <input class="fi @error('apellidos') input-error @enderror" type="text" name="apellidos"
                   value="{{ old('apellidos') }}" placeholder="{{ __('Apellidos') }}" required>
            @error('apellidos') <span class="error-msg">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="fg">
          <label class="fl">{{ __('Nombre de usuario') }}</label>
          <input class="fi @error('username') input-error @enderror" type="text" name="username"
                 value="{{ old('username') }}" placeholder="@tunombre" required>
          @error('username') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        <div class="fg">
          <label class="fl">{{ __('Correo electrónico') }}</label>
          <input class="fi @error('email') input-error @enderror" type="email" name="email"
                 value="{{ old('email') }}" placeholder="tu@email.com" required>
          @error('email') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        <div class="fg">
          <label class="fl">{{ __('DNI / NIE') }}</label>
          <input class="fi @error('dni_nie') input-error @enderror" type="text" name="dni_nie"
                 value="{{ old('dni_nie') }}" placeholder="12345678A" required>
          @error('dni_nie') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        <div class="fg">
          <label class="fl">{{ __('Teléfono') }}</label>
          <input class="fi @error('telefono') input-error @enderror" type="tel" name="telefono"
                 value="{{ old('telefono') }}" placeholder="+34 600 000 000" required>
          @error('telefono') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        <div class="fg">
          <label class="fl">{{ __('Contraseña') }}</label>
          <div style="position:relative;">
            <input class="fi @error('password') input-error @enderror" type="password" name="password"
                  placeholder="{{ __('Mín. 8 caracteres, mayús., números') }}" required
                  style="padding-right:40px;">
            <button type="button" onclick="togglePassword(this)"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                          background:none;border:none;cursor:pointer;color:var(--muted);padding:0;">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
          @error('password') <span class="error-msg">{{ $message }}</span> @enderror
          <small style="display:block;margin-top:4px;color:var(--muted);font-size:0.85em;">
            {{ __('Debe contener: mayúscula, minúscula y número') }}
          </small>
        </div>

        <div class="fg">
          <label class="fl">{{ __('Confirmar contraseña') }}</label>
          <div style="position:relative;">
            <input class="fi" type="password" name="password_confirmation"
                  placeholder="{{ __('Repite tu contraseña') }}" required
                  style="padding-right:40px;">
            <button type="button" onclick="togglePassword(this)"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                          background:none;border:none;cursor:pointer;color:var(--muted);padding:0;">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        <button class="lm-submit" type="submit">{{ __('Crear cuenta') }}</button>
      </form>

      <!-- FORM ORGANIZACIÓN -->
      <form method="POST" action="{{ route('register') }}" id="registerFormOrg" style="display:none;">
        @csrf
        <input type="hidden" name="tipo" value="organizacion">

        <div class="fg">
          <label class="fl">{{ __('Nombre de la organización *') }}</label>
          <input class="fi" type="text" name="nombre_organizacion"
                 placeholder="{{ __('Nombre de la organización *') }}" required>
        </div>

        <div class="fg">
          <label class="fl">{{ __('Tipo de organización *') }}</label>
          <select class="fi" name="tipo_organizacion" required>
            <option value="">{{ __('Selecciona un tipo') }}</option>
            <option value="protectora">🏠 {{ __('Protectora de animales') }}</option>
            <option value="veterinaria">🏥 {{ __('Clínica veterinaria') }}</option>
            <option value="refugio">🌿 {{ __('Refugio') }}</option>
            <option value="asociacion">🤝 {{ __('Asociación') }}</option>
          </select>
        </div>

        <div class="fg">
          <label class="fl">{{ __('Nombre de usuario') }}</label>
          <input class="fi" type="text" name="username" placeholder="@tuorganizacion" required>
        </div>

        <div class="fg">
          <label class="fl">{{ __('Correo electrónico') }}</label>
          <input class="fi" type="email" name="email" placeholder="contacto@organizacion.com" required>
        </div>

        <div class="fg">
          <label class="fl">{{ __('CIF *') }}</label>
          <input class="fi" type="text" name="cif" placeholder="A12345678" required>
        </div>

        <div class="fg">
          <label class="fl">{{ __('Teléfono') }}</label>
          <input class="fi" type="tel" name="telefono" placeholder="+34 600 000 000" required>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div class="fg">
            <label class="fl">{{ __('Provincia') }}</label>
            <input class="fi" type="text" name="provincia" placeholder="{{ __('Provincia') }}">
          </div>
          <div class="fg">
            <label class="fl">{{ __('Ciudad') }}</label>
            <input class="fi" type="text" name="ciudad" placeholder="{{ __('Ciudad') }}">
          </div>
        </div>

        <div class="fg">
          <label class="fl">{{ __('Dirección') }}</label>
          <input class="fi" type="text" name="direccion" placeholder="{{ __('Dirección') }}">
        </div>

        <div class="fg">
          <label class="fl">{{ __('Web') }} <span style="color:var(--muted);font-size:0.8rem;">({{ __('opcional') }})</span></label>
          <input class="fi" type="url" name="web" placeholder="https://tuorganizacion.com">
        </div>

        <div class="fg">
          <label class="fl">{{ __('Contraseña *') }}</label>
          <input class="fi" type="password" name="password"
                 placeholder="{{ __('Mín. 8 caracteres, mayús., números') }}" required>
          <small style="display:block;margin-top:4px;color:var(--muted);font-size:0.85em;">
            {{ __('Debe contener: mayúscula, minúscula y número') }}
          </small>
        </div>

        <div class="fg">
          <label class="fl">{{ __('Confirmar contraseña *') }}</label>
          <input class="fi" type="password" name="password_confirmation"
                 placeholder="{{ __('Repite tu contraseña') }}" required>
        </div>

        <button class="lm-submit" type="submit">{{ __('Registrar organización') }}</button>
      </form>

      <p class="lm-footer">
        {{ __('Al registrarte aceptas nuestros') }}
        <a href="#" onclick="event.preventDefault(); openTermsModal()">
          {{ __('términos de uso') }}
        </a>
      </p>
  </div>
</div>

<style>
  .input-error { border-color: #e74c3c !important; background-color: #fadbd8; }
  .error-msg { display: block; color: #e74c3c; font-size: 0.85em; margin-top: 4px; font-weight: 500; }
  .login-modal { max-height: 80vh; overflow-y: auto; width: min(90vw, 500px); }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var openModal = {{ $openModal ? 'true' : 'false' }};
    var isRegisterAttempt = {{ $isRegisterAttempt ? 'true' : 'false' }};
    var isLoginAttempt = {{ $isLoginAttempt ? 'true' : 'false' }};
    var tabs = document.querySelectorAll('.lm-tab');

    if (openModal) {
      openLoginModal();
      if (tabs.length > 1) {
        if (isRegisterAttempt) {
          setLoginTab(tabs[1], 'register');
        } else if (isLoginAttempt || {{ ($errors->any() && !request()->routeIs('profile.*')) ? 'true' : 'false' }}) {
          setLoginTab(tabs[0], 'login');
        }
      }
    }
  });
</script>