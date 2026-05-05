<!-- BIENVENIDA -->
<section id="sec-bienvenida" class="section active">
  <div class="hero">
    <div>
      <div class="hero-tag">{{ __('🐾 La comunidad animal de España') }}</div>
      <h1 class="hero-title">{!! __('Un hogar para cada patita, una voz para cada animal') !!}</h1>
      <p class="hero-desc">{{ __('PatitasUnidas es el punto de encuentro seguro entre personas, protectoras y organizaciones. Adopta, busca animales perdidos y apoya a los que más lo necesitan.') }}</p>
      <div class="hero-ctas">
        <button class="btn-p" onclick="setNav(document.querySelectorAll('.nav-btn')[1],'principal')">
          {{ __('Explorar publicaciones →') }}
        </button>
        <button class="btn-s" onclick="openLoginModal()">{{ __('Registrarse gratis') }}</button>
      </div>
    </div>
    <div class="hero-img-wrap">
      <img class="hero-img"
           src="https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=600&q=80"
           alt="{{ __('Perros felices') }}"
           onerror="this.src='https://picsum.photos/seed/dogs/600/450'">
      <div class="hero-float">
        <div class="hf-icon">🏠</div>
        <div>
          <div class="hf-num">{{ __('2.400+') }}</div>
          <div class="hf-lbl">{{ __('adopciones realizadas') }}</div>
        </div>
      </div>
      <div class="hero-float2">{{ __('✅ Registro verificado') }}</div>
    </div>
  </div>

  <div class="stats-row">
    <div class="stat-card">
      <div class="stat-card-ico">🐕</div>
      <div class="stat-card-num">1.240</div>
      <div class="stat-card-lbl">{{ __('Animales en adopción') }}</div>
    </div>
    <div class="stat-card">
      <div class="stat-card-ico">🔍</div>
      <div class="stat-card-num">318</div>
      <div class="stat-card-lbl">{{ __('Animales encontrados') }}</div>
    </div>
    <div class="stat-card">
      <div class="stat-card-ico">🤝</div>
      <div class="stat-card-num">95</div>
      <div class="stat-card-lbl">{{ __('Protectoras registradas') }}</div>
    </div>
    <div class="stat-card">
      <div class="stat-card-ico">👥</div>
      <div class="stat-card-num">12.800</div>
      <div class="stat-card-lbl">{{ __('Usuarios activos') }}</div>
    </div>
  </div>

  <div class="how-wrap">
    <div class="sec-hdr">
      <h2>{{ __('¿Cómo funciona PatitasUnidas?') }}</h2>
      <p>{{ __('Tres sencillos pasos para ayudar o encontrar a tu compañero ideal') }}</p>
    </div>
    <div class="steps-grid">
      <div class="step-card">
        <div class="step-ico">✍️</div>
        <div class="step-num">1</div>
        <h3>{{ __('Regístrate de forma segura') }}</h3>
        <p>{{ __('Crea tu perfil verificado con DNI/NIE y teléfono. Nuestro sistema evita perfiles falsos y protege a los animales de adopciones fraudulentas.') }}</p>
      </div>
      <div class="step-card">
        <div class="step-ico">📋</div>
        <div class="step-num">2</div>
        <h3>{{ __('Publica o encuentra') }}</h3>
        <p>{{ __('Crea publicaciones en las secciones de adopción, animales perdidos o apoyo. Usa filtros por ubicación, especie y más para encontrar exactamente lo que buscas.') }}</p>
      </div>
      <div class="step-card">
        <div class="step-ico">💬</div>
        <div class="step-num">3</div>
        <h3>{{ __('Conecta y actúa') }}</h3>
        <p>{{ __('Usa nuestra mensajería privada para contactar directamente con protectoras y usuarios. Cada conversación queda registrada de forma segura.') }}</p>
      </div>
    </div>
  </div>
</section>