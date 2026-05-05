<!-- FAQ / INFO -->
<section id="sec-faq" class="section">
  <div class="faq-wrap">
    <div class="faq-intro">
      <div class="faq-intro-txt">
        <h2>{{ __('Todo lo que necesitas saber sobre PatitasUnidas') }}</h2>
        <p>{{ __('faq_intro_desc') }}</p>
        <div style="margin-top:20px;display:flex;gap:10px;flex-wrap:wrap;">
          <button class="btn-p" onclick="openLoginModal()">{{ __('Únete gratis') }}</button>
          <a href="{{ route('donate') }}" class="btn-s" style="text-decoration:none; display:inline-block;">{{ __('💛 Donar') }}</a>
        </div>
      </div>
      <img class="faq-intro-img" src="https://images.unsplash.com/photo-1450778869180-41d0601e046e?w=500&q=80"
        alt="{{ __('Gato y perro') }}" onerror="this.src='https://picsum.photos/seed/pets2/500/375'">
    </div>

    <div class="faq-section-title">{{ __('Preguntas frecuentes') }}</div>
    <div id="faqList"></div>

    <div class="sec-hdr mt22" style="text-align:left;margin-bottom:22px;margin-top:48px;">
      <h2 style="font-family:'Fraunces',serif;font-size:1.6rem;">{{ __('¿Qué puedes hacer en PatitasUnidas?') }}</h2>
    </div>
    <div class="about-cards">
      <div class="about-card">
        <div class="about-card-ico">🏠</div>
        <h3>{{ __('Adoptar') }}</h3>
        <p>{{ __('faq_adoptar_desc') }}</p>
      </div>
      <div class="about-card">
        <div class="about-card-ico">🔍</div>
        <h3>{{ __('Buscar') }}</h3>
        <p>{{ __('faq_buscar_desc') }}</p>
      </div>
      <div class="about-card">
        <div class="about-card-ico">❤️</div>
        <h3>{{ __('Apoyar') }}</h3>
        <p>{{ __('faq_apoyar_desc') }}</p>
      </div>
    </div>

    <div class="sec-hdr mt22" style="text-align:left;margin-bottom:22px;margin-top:48px;">
      <h2 style="font-family:'Fraunces',serif;font-size:1.6rem;">{{ __('El equipo detrás del proyecto') }}</h2>
      <p style="color:var(--muted);font-size:.88rem;margin-top:6px;">{{ __('Proyecto DAW') }}</p>
    </div>
    <div class="team-row">
      <div class="team-card">
        <div class="team-av" style="background:#C8582E;">CL</div>
        <div class="team-name">César Leonardo Calderón</div>
        <div class="team-role">{{ __('Backend & Arquitectura') }}</div>
      </div>
      <div class="team-card">
        <div class="team-av" style="background:#4E7A38;">JD</div>
        <div class="team-name">Jonathan Díez de Artazcoz</div>
        <div class="team-role">{{ __('Frontend & Diseño UI') }}</div>
      </div>
      <div class="team-card">
        <div class="team-av" style="background:#EDB840;color:var(--dark);">AF</div>
        <div class="team-name">Alejandro Fraile Lobato</div>
        <div class="team-role">{{ __('Desarrollo & Base de Datos') }}</div>
      </div>
    </div>
  </div>
</section>