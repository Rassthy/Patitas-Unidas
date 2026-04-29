<!-- HEADER -->
<header id="mainHeader">
  <a class="hdr-logo" href="#" onclick="showSection('bienvenida');return false;">
    <div class="hdr-logo-icon">🐾</div>
    <span class="hdr-logo-txt">PatitasUnidas</span>
  </a>
  <nav>
    <button class="nav-btn active" onclick="if(document.getElementById('sec-bienvenida')){ setNav(this,'bienvenida'); return false; } window.location.href='{{ route('home') }}';">
      <span class="nav-paw">🐾</span>Bienvenida
    </button>
    <button class="nav-btn" onclick="if(document.getElementById('sec-principal')){ setNav(this,'principal'); return false; } window.location.href='{{ route('home') }}';">
      <span class="nav-paw">🐾</span>Principal
    </button>
    <button class="nav-btn" onclick="if(document.getElementById('sec-faq')){ setNav(this,'faq'); return false; } window.location.href='{{ route('home') }}';">
      <span class="nav-paw">🐾</span>Información / FAQ
    </button>
  </nav>
  <div class="hdr-right">
    @auth
      <button class="hdr-icon-btn" onclick="openLoginModal()">
        <i class="fa-solid fa-bell"></i>
        <span class="hdr-badge">3</span>
      </button>
      <div class="user-chip" onclick="window.location.href='{{ route('profile.show') }}'">
        <img src="{{ Auth::user()->foto_perfil_url }}" alt="Foto de perfil">
        <span class="user-chip-name">Hola {{ Auth::user()->nombre }}!</span>
      </div>
    @else
      <button class="hdr-icon-btn" onclick="openLoginModal()">
        <i class="fa-solid fa-bell"></i>
        <span class="hdr-badge">3</span>
      </button>
      <button class="login-btn" onclick="openLoginModal()">
        <i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión
      </button>
    @endauth
  </div>
</header>