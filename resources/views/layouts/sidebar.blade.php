<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sb-logo" onclick="showSection('bienvenida')">🐾</div>
  <button class="sb-btn active" onclick="showSection('bienvenida')" title="Inicio">
    <i class="fa-solid fa-house"></i>
    <span class="sb-tip">Inicio</span>
  </button>
  <button class="sb-btn" onclick="showSection('principal')" title="Publicaciones">
    <i class="fa-solid fa-paw"></i>
    <span class="sb-tip">Publicaciones</span>
  </button>
  <div class="sb-sep"></div>
  <button class="sb-btn" id="sidebarChatBtn" onclick="toggleChatPanel()">
    <i class="fa-solid fa-comment-dots"></i>
    <span class="sb-dot"></span>
    <span class="sb-tip">Mensajes</span>
  </button>
  <button class="sb-btn" onclick="openLoginModal()">
    <i class="fa-solid fa-bell"></i>
    <span class="sb-dot" id="notifDot"></span>
    <span class="sb-tip">Notificaciones</span>
  </button>
  <div class="sb-sep"></div>
  <button class="sb-btn" onclick="showSection('faq')">
    <i class="fa-solid fa-circle-info"></i>
    <span class="sb-tip">Información</span>
  </button>

  @guest
    <button class="sb-btn sb-session-btn" onclick="openLoginModal()">
      <i class="fa-solid fa-right-to-bracket"></i>
      <span class="sb-tip">Acceder</span>
    </button>
  @endguest

  @auth
    <form method="POST" action="{{ route('logout') }}" class="sb-session-btn">
      @csrf
      <button class="sb-btn sb-session-btn" type="submit">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span class="sb-tip">Cerrar sesion</span>
      </button>
    </form>
  @endauth

</div>
</aside>