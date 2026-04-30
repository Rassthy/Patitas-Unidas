@extends('layouts.app')

@section('content')
<div id="profile-container">

  {{-- ===== CABECERA ===== --}}
  <div class="profile-header">

    <div class="profile-banner {{ $user->banner ? '' : 'no-image' }}"
         style="{{ $user->banner ? 'background-image:url(' . $user->banner_url . ');' : '' }}">
    </div>

    <div class="profile-card">
      <div class="profile-top">

        {{-- AVATAR --}}
        <div class="profile-avatar-wrap">
          @if($user->foto_perfil)
            <img class="profile-avatar" src="{{ $user->foto_perfil_url }}" alt="Foto de {{ $user->nombre }}">
          @else
            <div class="profile-avatar no-image">🐾</div>
          @endif
          <div class="profile-badge" title="Usuario verificado">
            <i class="fa-solid fa-check"></i>
          </div>
        </div>

        {{-- INFO --}}
        <div>
          <div class="profile-type">
            <i class="fa-solid fa-user"></i> Usuario registrado
          </div>
          <h1 class="profile-name">{{ $user->nombre }} {{ $user->apellidos }}</h1>
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
            <div class="meta-item">
              <i class="fa-solid fa-envelope" style="color:var(--terra)"></i>
              <p>{{ $user->email }}</p>
            </div>
            @if($user->fecha_nacimiento)
              <div class="meta-item">
                <i class="fa-solid fa-cake-candles" style="color:var(--terra)"></i>
                <p>{{ $user->fecha_nacimiento->format('d/m/Y') }}</p>
              </div>
            @endif
          </div>

          <div class="profile-location">
            <i class="fa-solid fa-phone"></i>
            {{ $user->telefono }}
            &nbsp;·&nbsp;
            <i class="fa-solid fa-id-card"></i>
            {{ $user->dni_nie }}
          </div>
        </div>

        {{-- ACCIONES --}}
        <div class="profile-actions">
          <a href="{{ route('profile.edit') }}" class="btn-p">
            <i class="fa-solid fa-pen"></i> Editar perfil
          </a>
          <a href="{{ route('profile.settings') }}" class="btn-s">
            <i class="fa-solid fa-gear"></i> Ajustes
          </a>
        </div>

      </div>
    </div>
  </div>

  {{-- FLASH --}}
  @if(session('success'))
    <div class="profile-sections">
      <div class="alert-banner" style="margin-bottom:22px;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
      </div>
    </div>
  @endif

  {{-- ===== ESTADÍSTICAS ===== --}}
  <div class="profile-sections">
    <div class="profile-stats">
      <div class="stat-item">
        <div class="stat-item-num">0</div>
        <div class="stat-item-lbl">Publicaciones</div>
      </div>
      <div class="stat-item">
        <div class="stat-item-num">0</div>
        <div class="stat-item-lbl">Animales adoptados</div>
      </div>
      <div class="stat-item">
        <div class="stat-item-num">0</div>
        <div class="stat-item-lbl">Mensajes enviados</div>
      </div>
      <div class="stat-item">
        <div class="stat-item-num">0</div>
        <div class="stat-item-lbl">Valoraciones recibidas</div>
      </div>
    </div>

    {{-- TABS --}}
    <div class="profile-tabs">
      <button class="profile-tab active" onclick="switchTab(this,'tab-publicaciones')">
        <i class="fa-solid fa-paw"></i> Publicaciones
      </button>
      <button class="profile-tab" onclick="switchTab(this,'tab-animales')">
        <i class="fa-solid fa-heart"></i> Mis animales
      </button>
      <button class="profile-tab" onclick="switchTab(this,'tab-valoraciones')">
        <i class="fa-solid fa-star"></i> Valoraciones
      </button>
    </div>

    <div id="tab-publicaciones" class="profile-tab-content">
      <div class="empty-state">
        <div class="empty-state-ico">🐾</div>
        <div class="empty-state-title">Aún no tienes publicaciones</div>
        <p class="empty-state-desc">Cuando publiques animales en adopción, casos perdidos o llamadas de apoyo, aparecerán aquí.</p>
        <a href="{{ url('/') }}" class="btn-p"><i class="fa-solid fa-plus"></i> Crear publicación</a>
      </div>
    </div>

    <div id="tab-animales" class="profile-tab-content" style="display:none;">
      <div class="empty-state">
        <div class="empty-state-ico">🐶</div>
        <div class="empty-state-title">Todavía no tienes animales registrados</div>
        <p class="empty-state-desc">AREA EN CONSTRUCCION...</p>
      </div>
    </div>

    <div id="tab-valoraciones" class="profile-tab-content" style="display:none;">
      <div class="comments-section">
        <div class="empty-state">
          <div class="empty-state-ico">⭐</div>
          <div class="empty-state-title">Sin valoraciones todavía</div>
          <p class="empty-state-desc">Las valoraciones que otros usuarios te dejen aparecerán aquí.</p>
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
