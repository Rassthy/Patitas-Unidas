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
      <button class="lm-tab active" onclick="setLoginTab(this,'login')">
        Iniciar sesión
      </button>
      <button class="lm-tab" onclick="setLoginTab(this,'register')">
        Registrarse
      </button>
    </div>

    @php
      $registerErrorKeys = ['nombre','apellidos','username','dni_nie','telefono','email','password'];
      $loginErrorKeys = ['email','password'];
      $registerAttempt = old('nombre') || old('apellidos') || old('username') || old('dni_nie') || old('telefono');
    @endphp

    <!-- LOGIN FORM -->
    <div id="loginForm">
      <h2 class="lm-h2">Bienvenido de vuelta 👋</h2>

      @if ($errors->hasAny($loginErrorKeys) && !$registerAttempt)
        <div class="lm-alert lm-alert-error">
          <p>Hay un problema con tu inicio de sesión:</p>
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="fg">
          <label class="fl">Correo electrónico</label>
          <input class="fi" type="email" name="email" value="{{ old('email') }}" placeholder="tu@email.com" required>
        </div>

        <div class="fg">
          <label class="fl">Contraseña</label>
          <input class="fi" type="password" name="password" placeholder="••••••••" required>
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

      @if ($errors->hasAny($registerErrorKeys) || $registerAttempt)
        <div class="lm-alert lm-alert-error">
          <p>Revisa los campos del registro:</p>
        </div>
      @endif

      <form method="POST" action="{{ route('register') }}">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div class="fg">
            <label class="fl">Nombre</label>
            <input class="fi" type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Tu nombre" required>
          </div>

          <div class="fg">
            <label class="fl">Apellidos</label>
            <input class="fi" type="text" name="apellidos" value="{{ old('apellidos') }}" placeholder="Tus apellidos" required>
          </div>
        </div>

        <div class="fg">
          <label class="fl">Nombre de usuario</label>
          <input class="fi" type="text" name="username" value="{{ old('username') }}" placeholder="@tunombre" required>
        </div>

        <div class="fg">
          <label class="fl">Correo electrónico</label>
          <input class="fi" type="email" name="email" value="{{ old('email') }}" placeholder="tu@email.com" required>
        </div>

        <div class="fg">
          <label class="fl">DNI / NIE</label>
          <input class="fi" type="text" name="dni_nie" value="{{ old('dni_nie') }}" placeholder="12345678A" required>
        </div>

        <div class="fg">
          <label class="fl">Teléfono</label>
          <input class="fi" type="tel" name="telefono" value="{{ old('telefono') }}" placeholder="+34 600 000 000" required>
        </div>

        <div class="fg">
          <label class="fl">Contraseña</label>
          <input class="fi" type="password" name="password" placeholder="Mínimo 8 caracteres" required>
        </div>

        <button class="lm-submit" type="submit">
          Crear cuenta
        </button>
      </form>

      <p class="lm-footer">
        Al registrarte aceptas nuestros <a href="#">términos de uso</a>
      </p>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var registerAttempt = {{ $registerAttempt ? 'true' : 'false' }};
        if (registerAttempt) {
          var tabs = document.querySelectorAll('.lm-tab');
          if (tabs.length > 1) {
            setLoginTab(tabs[1], 'register');
          }
        }
      });
    </script>

  </div>
</div>