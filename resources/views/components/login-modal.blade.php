<!-- LOGIN MODAL -->
<div class="login-overlay" id="loginOverlay" onclick="closeLoginModal(event)">
  <div class="login-modal">

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
      <button class="lm-tab active" onclick="setLoginTab(this,'login')">Iniciar sesión</button>
      <button class="lm-tab" onclick="setLoginTab(this,'register')">Registrarse</button>
    </div>

    @php
      $registerErrorKeys = ['nombre','apellidos','username','dni_nie','telefono','email','password','error'];
      $loginValidationKeys = ['email','password'];
      $registerAttempt = old('nombre') || old('apellidos') || old('username') || old('dni_nie') || old('telefono');
      $hasRegisterErrors = $errors->hasAny($registerErrorKeys);
      $hasLoginValidationErrors = $errors->hasAny($loginValidationKeys);
      $loginErrorBag = session('errors') ? session('errors')->getBag('login') : new \Illuminate\Support\ViewErrorBag();
      $hasLoginCredentialErrors = $loginErrorBag->any();
      $loginCredentialErrors = $hasLoginCredentialErrors ? $loginErrorBag->all() : [];
      $shouldOpenLogin = $hasLoginValidationErrors || $hasLoginCredentialErrors;
    @endphp

    <!-- LOGIN FORM -->
    <div id="loginForm">
      <h2 class="lm-h2">Bienvenido de vuelta 👋</h2>

      @if ($hasLoginCredentialErrors)
        <div class="lm-alert lm-alert-error">
          <i class="fa-solid fa-exclamation-circle"></i>
          <div>
            <p><strong>Error en el inicio de sesión:</strong></p>
            <ul>
              @foreach ($loginCredentialErrors as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      <form method="POST" action="{{ route('login', [], false) }}" id="loginFormEl">
        @csrf

        <div class="fg">
          <label class="fl">Correo electrónico</label>
          <input class="fi @error('email') input-error @enderror" 
                 type="email" 
                 name="email" 
                 value="{{ old('email') }}" 
                 placeholder="tu@email.com" 
                 required>
          @error('email')
            <span class="error-msg">{{ $message }}</span>
          @enderror
        </div>

        <div class="fg">
          <label class="fl">Contraseña</label>
          <input class="fi @error('password') input-error @enderror" 
                 type="password" 
                 name="password" 
                 placeholder="••••••••" 
                 required>
          @error('password')
            <span class="error-msg">{{ $message }}</span>
          @enderror
        </div>

        <div class="fg" style="display: flex; align-items: center; gap: 8px;">
          <input type="checkbox" name="remember" id="rememberLogin" style="width: 16px; cursor: pointer;">
          <label for="rememberLogin" style="margin: 0; cursor: pointer; font-size: 0.9em;">Recuérdame</label>
        </div>

        <button class="lm-submit" type="submit">
          Entrar a PatitasUnidas
        </button>
      </form>

      <p class="lm-footer">
        <a href="#">¿Olvidaste tu contraseña?</a>
      </p>
    </div>

    <!-- REGISTER FORM -->
    <div id="registerForm" class="hidden">
      <h2 class="lm-h2">Crea tu cuenta 🐾</h2>

      @if ($hasRegisterErrors || $registerAttempt)
        <div class="lm-alert lm-alert-error">
          <i class="fa-solid fa-exclamation-circle"></i>
          <div>
            <p><strong>Revisa los siguientes campos:</strong></p>
            <ul>
              @foreach ($registerErrorKeys as $key)
                @foreach ($errors->get($key) as $error)
                  <li>{{ $error }}</li>
                @endforeach
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      <form method="POST" action="{{ route('register', [], false) }}" id="registerFormEl">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div class="fg">
            <label class="fl">Nombre</label>
            <input class="fi @error('nombre') input-error @enderror" 
                   type="text" 
                   name="nombre" 
                   value="{{ old('nombre') }}" 
                   placeholder="Tu nombre" 
                   required>
            @error('nombre')
              <span class="error-msg">{{ $message }}</span>
            @enderror
          </div>

          <div class="fg">
            <label class="fl">Apellidos</label>
            <input class="fi @error('apellidos') input-error @enderror" 
                   type="text" 
                   name="apellidos" 
                   value="{{ old('apellidos') }}" 
                   placeholder="Tus apellidos" 
                   required>
            @error('apellidos')
              <span class="error-msg">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <div class="fg">
          <label class="fl">Nombre de usuario</label>
          <input class="fi @error('username') input-error @enderror" 
                 type="text" 
                 name="username" 
                 value="{{ old('username') }}" 
                 placeholder="@tunombre" 
                 required>
          @error('username')
            <span class="error-msg">{{ $message }}</span>
          @enderror
        </div>

        <div class="fg">
          <label class="fl">Correo electrónico</label>
          <input class="fi @error('email') input-error @enderror" 
                 type="email" 
                 name="email" 
                 value="{{ old('email') }}" 
                 placeholder="tu@email.com" 
                 required>
          @error('email')
            <span class="error-msg">{{ $message }}</span>
          @enderror
        </div>

        <div class="fg">
          <label class="fl">DNI / NIE</label>
          <input class="fi @error('dni_nie') input-error @enderror" 
                 type="text" 
                 name="dni_nie" 
                 value="{{ old('dni_nie') }}" 
                 placeholder="12345678A" 
                 required>
          @error('dni_nie')
            <span class="error-msg">{{ $message }}</span>
          @enderror
        </div>

        <div class="fg">
          <label class="fl">Teléfono</label>
          <input class="fi @error('telefono') input-error @enderror" 
                 type="tel" 
                 name="telefono" 
                 value="{{ old('telefono') }}" 
                 placeholder="+34 600 000 000" 
                 required>
          @error('telefono')
            <span class="error-msg">{{ $message }}</span>
          @enderror
        </div>

        <div class="fg">
          <label class="fl">Contraseña</label>
          <input class="fi @error('password') input-error @enderror" 
                 type="password" 
                 name="password" 
                 placeholder="Mín. 8 caracteres, mayús., números" 
                 required>
          @error('password')
            <span class="error-msg">{{ $message }}</span>
          @enderror
          <small style="display: block; margin-top: 4px; color: var(--gray); font-size: 0.85em;">
            Debe contener: mayúscula, minúscula y número
          </small>
        </div>

        <div class="fg">
          <label class="fl">Confirmar contraseña</label>
          <input class="fi @error('password_confirmation') input-error @enderror" 
                 type="password" 
                 name="password_confirmation" 
                 placeholder="Repite tu contraseña" 
                 required>
          @error('password_confirmation')
            <span class="error-msg">{{ $message }}</span>
          @enderror
        </div>

        <button class="lm-submit" type="submit">
          Crear cuenta
        </button>
      </form>

      <p class="lm-footer">
        Al registrarte aceptas nuestros <a href="#">términos de uso</a>
      </p>
    </div>

  </div>
</div>

<style>
  .input-error {
    border-color: #e74c3c !important;
    background-color: #fadbd8;
  }

  .error-msg {
    display: block;
    color: #e74c3c;
    font-size: 0.85em;
    margin-top: 4px;
    font-weight: 500;
  }

  .lm-alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    gap: 12px;
    align-items: flex-start;
  }

  .lm-alert-error {
    background-color: #fadbd8;
    border: 1px solid #e74c3c;
    color: #c0392b;
  }

  .lm-alert i {
    flex-shrink: 0;
    margin-top: 2px;
  }

  .lm-alert ul {
    margin: 0;
    padding-left: 20px;
  }

  .lm-alert li {
    margin: 4px 0;
  }

  .login-modal {
    max-height: 80vh;
    overflow-y: auto;
    width: min(90vw, 500px);
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var registerAttempt = {{ $registerAttempt ? 'true' : 'false' }};
    var hasRegisterErrors = {{ $hasRegisterErrors ? 'true' : 'false' }};
    var shouldOpenLogin = {{ $shouldOpenLogin ? 'true' : 'false' }};
    var tabs = document.querySelectorAll('.lm-tab');

    if (registerAttempt || hasRegisterErrors || shouldOpenLogin) {
      openLoginModal();
      if (tabs.length > 1) {
        if (registerAttempt || hasRegisterErrors) {
          setLoginTab(tabs[1], 'register');
        } else if (shouldOpenLogin) {
          setLoginTab(tabs[0], 'login');
        }
      }
    }
  });
</script>
