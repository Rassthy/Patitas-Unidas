@extends('layouts.app')

@section('content')
<div id="profile-container">

  <!--  CABECERA  -->
  <div class="profile-header">
    <div class="profile-banner {{ $user->banner ? '' : 'no-image' }}"
         style="{{ $user->banner ? 'background-image:url(' . $user->banner_url . ');' : '' }}">
         
         <!-- Botones integrados en el banner (Expandibles) -->
         <div class="banner-actions">
            <a href="{{ route('profile.edit') }}" class="banner-btn-p" title="Editar perfil">
              <div class="banner-btn-icon"><i class="fa-solid fa-pen"></i></div>
              <span class="banner-btn-text">Editar perfil</span>
            </a>
            <a href="{{ route('profile.settings') }}" class="banner-btn-s" title="Ajustes">
              <div class="banner-btn-icon"><i class="fa-solid fa-gear"></i></div>
              <span class="banner-btn-text">Ajustes</span>
            </a>
         </div>
    </div>

    @php
        // Leemos la privacidad (por defecto mostramos apellidos y ocultamos fecha)
        $mostrarApellidos = ($user->user_settings['mostrar_apellidos'] ?? '1') == '1';
        $mostrarFecha = ($user->user_settings['mostrar_fecha'] ?? '0') == '1';
    @endphp

    <div class="profile-card">
      <div class="profile-top">
        {{-- AVATAR Y ESTADO --}}
        <div class="profile-avatar-wrap">
          @if($user->foto_perfil)
            <img class="profile-avatar" src="{{ $user->foto_perfil_url }}" alt="Foto de {{ $user->nombre }}">
          @else
            <div class="profile-avatar no-image">🐾</div>
          @endif
          {{-- Preparado para la lógica backend: online, away, offline --}}
          <div class="profile-status status-online" title="Conectado"></div>
        </div>

        {{-- INFO --}}
        <div>
          <h1 class="profile-name">
            {{ $user->nombre }}
            @if($mostrarApellidos)
                {{ $user->apellidos }}
            @endif
          </h1>
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
              <p>Miembro desde {{ \Carbon\Carbon::parse($user->created_at)->locale('es')->translatedFormat('F Y') }}</p>
            </div>
            
            {{-- Aplicamos la privacidad a la fecha de nacimiento --}}
            @if($user->fecha_nacimiento && $mostrarFecha)
              <div class="meta-item">
                <i class="fa-solid fa-cake-candles" style="color:var(--terra)"></i>
                <p>{{ $user->fecha_nacimiento->format('d/m/Y') }}</p>
              </div>
            @endif
          </div>
        </div>

        {{-- ETIQUETA DERECHA --}}
        <div class="profile-right-area" style="text-align: right;">
          <div class="profile-type">
            <i class="fa-solid fa-user"></i> Usuario
          </div>
        </div>

      </div>
    </div>
  </div>

  <!--  ESTADÍSTICAS (3 Elementos)  -->
  <div class="profile-sections">
    <div class="profile-stats">
      <div class="stat-item">
        <div class="stat-item-num">0</div>
        <div class="stat-item-lbl">Publicaciones</div>
      </div>
      <div class="stat-item">
        <div class="stat-item-num">0</div>
        <div class="stat-item-lbl">Donaciones</div>
      </div>
      <div class="stat-item">
        <div class="stat-item-num">0</div>
        <div class="stat-item-lbl">Valoraciones enviadas</div>
      </div>
    </div>

    <!--  SECCIÓN MIXTA: VALORACIONES O ANIMALES  -->
    <div class="profile-card" style="margin-bottom: 32px;">
        <div class="profile-tabs" style="border-bottom:none; margin-bottom: 16px;">
          <button class="profile-tab active" onclick="switchTab(this,'tab-valoraciones')">
            <i class="fa-solid fa-star"></i> Valoraciones
          </button>
          <button class="profile-tab" onclick="switchTab(this,'tab-animales')">
            <i class="fa-solid fa-heart"></i> Mis animales
          </button>
        </div>

        <div id="tab-valoraciones" class="profile-tab-content">
          <!-- Formulario para valorar (Solo visible si estás registrado y no es tu propio perfil) -->
          @auth
            @if(Auth::id() !== $user->id)
            <div class="review-form-container">
              <h4>Dejar una valoración</h4>
              <form action="#" method="POST">
                @csrf
                <div class="star-rating-input">
                  <i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i>
                </div>
                <textarea placeholder="Deja un comentario (opcional)..." class="edit-form-textarea" style="min-height: 60px; margin-bottom: 10px;"></textarea>
                <button class="btn-p btn-s" type="submit">Publicar valoración</button>
              </form>
            </div>
            @endif
          @endauth

          <div class="empty-state">
            <div class="empty-state-ico">⭐</div>
            <div class="empty-state-title">Sin valoraciones todavía</div>
            <p class="empty-state-desc">Nadie ha valorado a este usuario.</p>
          </div>
        </div>

        <div id="tab-animales" class="profile-tab-content" style="display:none;">
          <div class="empty-state">
            <div class="empty-state-ico">🐶</div>
            <div class="empty-state-title">Todavía no tienes animales registrados</div>
            <p class="empty-state-desc">ÁREA EN CONSTRUCCIÓN...</p>
          </div>
        </div>
    </div>

    <!--  SECCIÓN INFERIOR: PUBLICACIONES  -->
    <div class="profile-card">
        <div class="edit-section-title" style="margin-bottom: 16px;">
            <i class="fa-solid fa-paw" style="color:var(--terra)"></i> Publicaciones
        </div>
        <div class="empty-state">
            <div class="empty-state-ico">🐾</div>
            <div class="empty-state-title">Aún no hay publicaciones</div>
            <p class="empty-state-desc">Cuando publique algo, aparecerá aquí.</p>
        </div>
    </div>

  </div>
</div>

<script>
function switchTab(btn, tabId) {
  document.querySelectorAll('.profile-tabs .profile-tab').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-valoraciones').style.display = 'none';
  document.getElementById('tab-animales').style.display = 'none';
  btn.classList.add('active');
  document.getElementById(tabId).style.display = '';
}
</script>
@endsection