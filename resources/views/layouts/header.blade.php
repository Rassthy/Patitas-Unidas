<!-- HEADER -->
<header id="mainHeader">
  <a class="hdr-logo" href="#" onclick="showSection('bienvenida');return false;">
    <div class="hdr-logo-icon">🐾</div>
    <span class="hdr-logo-txt">PatitasUnidas</span>
  </a>
  <nav>
    <button class="nav-btn active" onclick="setNav(this,'bienvenida')">
      <span class="nav-paw">🐾</span>Bienvenida
    </button>
    <button class="nav-btn" onclick="setNav(this,'principal')">
      <span class="nav-paw">🐾</span>Principal
    </button>
    <button class="nav-btn" onclick="setNav(this,'faq')">
      <span class="nav-paw">🐾</span>Información / FAQ
    </button>
  </nav>
  <div class="hdr-right">
    <button class="hdr-icon-btn" onclick="openLoginModal()">
      <i class="fa-solid fa-bell"></i>
      <span class="hdr-badge">3</span>
    </button>
    <button class="login-btn" id="headerLoginBtn" onclick="openLoginModal()">
      <i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión
    </button>
    <div class="user-chip hidden" id="userChip" onclick="openLoginModal()">
      <img src="https://i.pravatar.cc/40?img=5" alt="User">
      <span class="user-chip-name">@usuario</span>
    </div>
  </div>
</header>