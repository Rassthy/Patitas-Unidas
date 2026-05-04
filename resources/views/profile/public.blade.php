@extends('layouts.app')

@section('content')
<div id="profile-container">

  <!--  CABECERA  -->
  <div class="profile-header">

    <div class="profile-banner {{ $user->banner ? '' : 'no-image' }}"
         style="{{ $user->banner ? 'background-image:url(' . $user->banner_url . ');' : '' }}">
    </div>

    <div class="profile-card">
      <div class="profile-top">

        <!-- AVATAR -->
        <div class="profile-avatar-wrap">
            <img class="profile-avatar" src="{{ $user->foto_perfil_url }}" alt="Foto de {{ $user->nombre }}">
            
            <div class="profile-badge" title="Usuario verificado">
                <i class="fa-solid fa-check"></i>
            </div>
        </div>

        <!-- INFO -->
        <div>
          <div class="profile-type">
            <i class="fa-solid fa-user"></i> Usuario registrado
          </div>
          <h1 class="profile-name">{{ $user->nombre }}</h1>
          <p class="profile-username"><a>@</a>{{ $user->username }}</p>

          @if($user->descripcion)
            <p class="profile-bio">{{ $user->descripcion }}</p>
          @endif

          <div class="profile-meta">
            @if($user->ciudad || $user->provincia)
              <div class="meta-item">
                <i class="fa-solid fa-location-dot" style="color:var(--terra)"></i>
                <p>{{ implode(', ', array_filter([$user->ciudad, $user->provincia])) }}</p>
              </div>
            @endif
            <div class="meta-item">
              <i class="fa-solid fa-calendar" style="color:var(--terra)"></i>
              <p>Miembro desde {{ $user->created_at->translatedFormat('M Y') }}</p>
            </div>
          </div>
        </div>

        <!-- ACCIONES (vista pública) -->
        @auth
          @if(Auth::id() !== $user->id)
            <div class="profile-actions">
              <button class="btn-p" onclick="showToast('Mensajería disponible próximamente 🐾')">
                <i class="fa-solid fa-comment"></i> Mensaje
              </button>
              <button class="btn-s" onclick="showToast('Función próximamente 🐾')">
                <i class="fa-solid fa-user-plus"></i> Seguir
              </button>
            </div>
          @endif
        @else
          <div class="profile-actions">
            <a href="{{ route('login') }}" class="btn-p">
              <i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión para contactar
            </a>
          </div>
        @endauth

      </div>
    </div>
  </div>

  <!--  CONTENIDO PÚBLICO  -->
  <div class="profile-sections">

    <div class="profile-tabs">
      <button class="profile-tab active" onclick="switchTab(this,'tab-pub')">
        <i class="fa-solid fa-paw"></i> Publicaciones
      </button>
      <button class="profile-tab" onclick="switchTab(this,'tab-val')">
        <i class="fa-solid fa-star"></i> Valoraciones
      </button>
    </div>

    <div id="tab-pub" class="profile-tab-content">
      <!-- TODO: renderizar $publications cuando esté disponible -->
      <div class="empty-state">
        <div class="empty-state-ico">🐾</div>
        <div class="empty-state-title">{{ $user->nombre }} aún no tiene publicaciones</div>
        <p class="empty-state-desc">Cuando comparta animales o publicaciones, aparecerán aquí.</p>
      </div>
    </div>

    <div id="tab-val" class="profile-tab-content" style="display:none;">
      <div class="comments-section">
        <div class="empty-state">
          <div class="empty-state-ico">⭐</div>
          <div class="empty-state-title">Sin valoraciones todavía</div>
          <p class="empty-state-desc">Aún nadie ha valorado a este usuario.</p>
        </div>
      </div>
    </div>

  </div>

</div>

<script>
function switchTab(btn, tabId) {
  document.querySelectorAll('.profile-tab').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.profile-tab-content').forEach(t => t.style.display = 'none');
  btn.classList.add('active');
  document.getElementById(tabId).style.display = '';
}
</script>
@endsection
