<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sb-logo" onclick="showSection('bienvenida')">
    <img src="{{ asset('img/defaults/LogoPU.png') }}" alt="PatitasUnidas" class="sb-logo-img">
  </div>
  <button class="sb-btn active" onclick="if(document.getElementById('sec-bienvenida')){ showSection('bienvenida'); return false; } window.location.href='{{ route('home', ['tab' => 'bienvenida']) }}';" title="{{ __('Inicio') }}">
    <i class="fa-solid fa-house"></i>
    <span class="sb-tip">{{ __('Inicio') }}</span>
  </button>
  <button class="sb-btn" onclick="if(document.getElementById('sec-principal')){ showSection('principal'); return false; } window.location.href='{{ route('home', ['tab' => 'principal']) }}';" title="{{ __('Publicaciones') }}">
    <i class="fa-solid fa-paw"></i>
    <span class="sb-tip">{{ __('Publicaciones') }}</span>
  </button>
  <div class="sb-sep"></div>
  <button class="sb-btn" id="sidebarChatBtn"
    onclick="{{ Auth::check() ? 'toggleChatPanel()' : 'openLoginModal()' }}">
    <i class="fa-solid fa-comment-dots"></i>
    <span class="sb-dot" id="chatDot" style="display:none;"></span>
    <span class="sb-tip">{{ __('Mensajes') }}</span>
  </button>
  <button class="sb-btn"
    onclick="{{ Auth::check() ? 'openNotificationsPanel()' : 'openLoginModal()' }}">
    <i class="fa-solid fa-bell"></i>
    <span class="sb-dot" id="notifDot" style="display:none;"></span>
    <span class="sb-tip">{{ __('Notificaciones') }}</span>
  </button>
  <div class="sb-sep"></div>
  <button class="sb-btn" onclick="if(document.getElementById('sec-faq')){ showSection('faq'); return false; } window.location.href='{{ route('home', ['tab' => 'faq']) }}';">
    <i class="fa-solid fa-circle-info"></i>
    <span class="sb-tip">{{ __('Información') }}</span>
  </button>

  @guest
    <button class="sb-btn sb-session-btn" onclick="openLoginModal()">
      <i class="fa-solid fa-right-to-bracket"></i>
      <span class="sb-tip">{{ __('Acceder') }}</span>
    </button>
  @endguest

  @auth
    <button class="sb-btn" onclick="window.location.href='{{ route('profile.show') }}'">
      <i class="fa-solid fa-user"></i>
      <span class="sb-tip">{{ __('Perfil') }}</span>
    </button>
    <form method="POST" action="{{ route('logout') }}" class="sb-session-btn">
      @csrf
      <button class="sb-btn sb-session-btn" type="submit">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span class="sb-tip">{{ __('Cerrar sesion') }}</span>
      </button>
    </form>
  @endauth

</div>
</aside>