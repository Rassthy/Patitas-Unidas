@extends('layouts.app')

@section('content')

<div id="profile-container">

  @php
    $petPrivacy = ($user->user_settings['mascotas_publicas'] ?? '1') == '1';
    $canSeePets = $isOwner || $petPrivacy;
  @endphp

  <!-- CABECERA -->
  <div class="profile-header">
    <div class="profile-banner {{ $user->banner ? '' : 'no-image' }}"
      style="{{ $user->banner ? 'background-image:url(' . $user->banner_url . ');' : '' }}">
      @if($isOwner)
      <div class="banner-actions">
        <a href="{{ route('profile.edit') }}" class="banner-btn-p" title="{{ __('Editar perfil') }}">
          <div class="banner-btn-icon"><i class="fa-solid fa-pen"></i></div>
          <span class="banner-btn-text">{{ __('Editar perfil') }}</span>
        </a>
        <a href="{{ route('profile.settings') }}" class="banner-btn-s" title="{{ __('Ajustes') }}">
          <div class="banner-btn-icon"><i class="fa-solid fa-gear"></i></div>
          <span class="banner-btn-text">{{ __('Ajustes') }}</span>
        </a>
      </div>
      @endif
    </div>

    @php
      $mostrarApellidos = ($user->user_settings['mostrar_apellidos'] ?? '1') == '1';
      $mostrarFecha     = ($user->user_settings['mostrar_fecha']     ?? '0') == '1';
    @endphp

    <div class="profile-card">
      <div class="profile-top">
        <div class="profile-avatar-wrap">
          <img class="profile-avatar" src="{{ $user->foto_perfil_url }}"
               alt="{{ __('Foto de :nombre', ['nombre' => $user->nombre]) }}">
          <div class="profile-status status-online" title="{{ __('Conectado') }}"></div>
        </div>
        <div>
          <h1 class="profile-name">
            {{ $user->nombre }}
            @if($mostrarApellidos) {{ $user->apellidos }} @endif
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
              <p>{{ __('Miembro desde :fecha', ['fecha' => \Carbon\Carbon::parse($user->created_at)->locale(app()->getLocale())->translatedFormat('F Y')]) }}</p>
            </div>
            @if($user->fecha_nacimiento && $mostrarFecha)
            <div class="meta-item">
              <i class="fa-solid fa-cake-candles" style="color:var(--terra)"></i>
              <p>{{ $user->fecha_nacimiento->format('d/m/Y') }}</p>
            </div>
            @endif
          </div>
        </div>
        <div class="profile-right-area" style="text-align:right;">
          <div class="profile-type"><i class="fa-solid fa-user"></i> {{ __('Usuario') }}</div>
          @auth @if(!$isOwner)
            <br>
            <button class="btn-primary" onclick="startChatWith({{ $user->id }})" style="margin-top:5px;">
              <i class="fa-solid fa-comment"></i> {{ __('Enviar Mensaje') }}
            </button>
          @endif @endauth
        </div>
      </div>
    </div>
  </div>

  <!-- ESTADÍSTICAS -->
  <div class="profile-sections">
    <div class="profile-stats">
      <div class="stat-item">
        <div class="stat-item-num">{{ $user->posts->count() }}</div>
        <div class="stat-item-lbl">{{ __('Publicaciones') }}</div>
      </div>
      <div class="stat-item">
        <div class="stat-item-num">{{ $user->donations->count() }}</div>
        <div class="stat-item-lbl">{{ __('Donaciones') }}</div>
      </div>
      <div class="stat-item">
        <div class="stat-item-num">{{ $user->ratings->count() }}</div>
        <div class="stat-item-lbl">{{ __('Valoraciones recibidas') }}</div>
      </div>
    </div>

    <!-- TABS MIXTOS -->
    <div class="profile-card" id="profile-card-1" style="margin-bottom:32px;">
      <div class="profile-tabs" style="border-bottom:none;margin-bottom:16px;">
        <button class="profile-tab active" onclick="switchTab(this,'tab-valoraciones')">
          <i class="fa-solid fa-star"></i> {{ __('Valoraciones') }}
        </button>
        <button class="profile-tab" onclick="switchTab(this,'tab-animales')">
          <i class="fa-solid fa-paw"></i> {{ __('Mis animales') }}
          @if($user->pets->count() > 0)
            <span style="background:var(--terra);color:#fff;border-radius:50px;font-size:.65rem;
                         font-weight:700;padding:1px 7px;margin-left:4px;">{{ $user->pets->count() }}</span>
          @endif
        </button>
        <!-- Botón añadir mascota (solo propietario) -->
        @if($isOwner && $user->pets->isNotEmpty())
          <button class="btn-p" id="addPetButton" onclick="openAddPetModal()">
            <i class="fa-solid fa-plus"></i> {{ __('Añadir mascota') }}
          </button>
        @endif
      </div>

      <!-- TAB VALORACIONES -->
      <div id="tab-valoraciones" class="profile-tab-content">
        @auth @if(!$isOwner)
        <div class="review-form-container">
          <h4>{{ __('Dejar una valoración') }}</h4>
          <form action="{{ route('profile.rate', $user->id) }}" method="POST" id="rateForm">
            @csrf
            <div class="star-rating" id="starRating"
                 style="font-size:2rem;cursor:pointer;display:inline-flex;gap:5px;">
              @for($i = 0; $i < 5; $i++)
                <i class="fa-regular fa-star" data-index="{{ $i }}"></i>
              @endfor
            </div>
            <input type="hidden" name="puntuacion" id="ratingInput">
            <textarea name="comentario" placeholder="{{ __('Deja un comentario (opcional)...') }}"
                      class="edit-form-textarea" style="min-height:60px;margin-bottom:10px;"></textarea>
            <button class="btn-p btn-s" id="btn-valoracion" type="submit">{{ __('Enviar valoración') }}</button>
          </form>
        </div>

        <div id="overwriteWarning"
             style="display:none;position:fixed;top:50%;left:50%;
                    transform:translate(-50%,-50%);background:white;padding:24px;
                    border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.3);
                    z-index:9999;text-align:center;max-width:400px;">
          <div style="font-size:2rem;color:var(--terra);margin-bottom:10px;"><i class="fa-solid fa-triangle-exclamation"></i></div>
          <h3 style="margin-top:0;">{{ __('Actualizar valoración') }}</h3>
          <p>{{ __('Ya tienes una valoración en este perfil. Si decides enviar esta, la antigua se borrará y se sustituirá por la nueva.') }}</p>
          <div style="display:flex;gap:10px;justify-content:center;margin-top:20px;">
            <button type="button" class="btn-s" onclick="document.getElementById('overwriteWarning').style.display='none'">{{ __('Cancelar') }}</button>
            <button type="button" class="btn-p" onclick="submitRatingAnyway()">{{ __('Sí, reemplazar') }}</button>
          </div>
        </div>
        @endif @endauth

        <div class="review-list">
          @forelse($user->ratings as $rating)
          <div class="review-item">
            <div class="review-header">
              <div style="display:flex;align-items:center;gap:6px;">
                <img src="{{ $rating->voter->foto_perfil_url }}" alt="{{ $rating->voter->username }}"
                     style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                <strong>{{ $rating->voter->username }}</strong>
              </div>
              <span class="review-stars">
                @php $entera = floor($rating->puntuacion); $media = ($rating->puntuacion - $entera) >= .5 ? 1 : 0; $vacia = 5 - $entera - $media; @endphp
                @for($i=0;$i<$entera;$i++)<i class="fa-solid fa-star" style="color:var(--terra)"></i>@endfor
                @if($media)<i class="fa-solid fa-star-half-stroke" style="color:var(--terra)"></i>@endif
                @for($i=0;$i<$vacia;$i++)<i class="fa-regular fa-star" style="color:var(--terra)"></i>@endfor
              </span>
            </div>
            <p>{{ $rating->comentario }}</p>
            <small>{{ $rating->created_at->diffForHumans() }}</small>
          </div>
          @empty
          <div class="empty-state">
            <div class="empty-state-ico">⭐</div>
            <div class="empty-state-title">{{ __('Sin valoraciones todavía') }}</div>
          </div>
          @endforelse
        </div>
      </div>

      <!-- TAB ANIMALES -->
      <div id="tab-animales" class="profile-tab-content" style="display:none;">

        @if(!$canSeePets)
        <div style="position:relative;overflow:hidden;border-radius:var(--r);min-height:200px;">
          <div class="pets-private-blur">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
              @for($i=0;$i<3;$i++)
              <div style="background:var(--soft);border-radius:var(--r);height:180px;"></div>
              @endfor
            </div>
          </div>
          <div class="pets-private-overlay">
            <span style="font-size:2.5rem;">🔒</span>
            <p style="font-family:'Fraunces',serif;font-size:1.05rem;font-weight:700;color:var(--dark);margin:0;">
              {{ __('Mascotas privadas') }}
            </p>
            <p style="font-size:.85rem;color:var(--muted);margin:0;text-align:center;">
              {{ __('El dueño ha hecho sus mascotas privadas.') }}
            </p>
          </div>
        </div>

        @elseif($user->pets->isEmpty())
        <div class="empty-state">
          <div class="empty-state-ico">🐶</div>
          <div class="empty-state-title">
            {{ $isOwner
              ? __('Todavía no tienes animales registrados')
              : __('Este usuario no tiene animales registrados.') }}
          </div>
          @if($isOwner)
          <p class="empty-state-desc">{{ __('¡Añade a tu primer compañero peludo!') }}</p>
          <button class="btn-p" onclick="openAddPetModal()" style="margin-top:8px;">
            <i class="fa-solid fa-plus"></i> {{ __('Añadir mascota') }}
          </button>
          @endif
        </div>

        @else
        <div class="pets-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:18px;">
          @foreach($user->pets as $pet)
          @php
            $petMainImg = $pet->images->first();
            $petImgUrl  = $petMainImg ? asset('storage/'.$petMainImg->url) : $pet->foto_url;
          @endphp
          <div class="pet-card" onclick="openPetDetailModal({{ $pet->id }})">
            <img class="pet-card-img" src="{{ $petImgUrl }}" alt="{{ $pet->nombre }}"
                loading="lazy" onerror="this.src='/img/defaults/foto_perfil_generica.png'">
            <div class="pet-card-body">
              <div class="pet-card-name">{{ $pet->nombre }}</div>
              <div class="pet-card-tags">
                @if($pet->especie) <span class="pet-card-tag">🐾 {{ $pet->especie }}</span> @endif
                @if($pet->raza)    <span class="pet-card-tag">🏷️ {{ $pet->raza }}</span> @endif
                @if($pet->edad)
                  <span class="pet-card-tag">
                    📅 {{ $pet->edad }} {{ $pet->edad != 1 ? __('años') : __('año') }}
                  </span>
                @endif
              </div>
              @if($pet->descripcion)
                <p class="pet-card-desc">{{ $pet->descripcion }}</p>
              @endif
            </div>
          </div>
          @endforeach
        </div>
        @endif
      </div>

    <!-- PUBLICACIONES -->
    <div class="profile-card" id="profile-card-2">
      <div class="edit-section-title" style="margin-bottom:24px;">
        <div style="display:flex;justify-content:space-between;align-items:center;width:100%;">
          <span><i class="fa-solid fa-paw" style="color:var(--terra)"></i> {{ __('Publicaciones') }}</span>
          <span style="font-size:.8rem;background:var(--terra);color:white;padding:4px 12px;border-radius:20px;">{{ $user->posts->count() }}</span>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:20px;">
        @forelse($user->posts as $post)
          <a href="{{ route('posts.show', $post->id) }}" class="post-card-link" style="text-decoration:none;color:inherit;">
            <div class="post-card" style="border:1px solid #eee;border-radius:12px;overflow:hidden;background:white;transition:transform .2s;height:100%;">
              @if($post->imagen_url)
                <img src="{{ $post->imagen_url }}" alt="{{ $post->titulo }}" style="width:100%;height:160px;object-fit:cover;">
              @endif
              <div style="padding:15px;">
                <span style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--terra);">{{ $post->category->name ?? __('General') }}</span>
                <h4 style="margin:8px 0;font-size:1.1rem;color:var(--dark);">{{ $post->titulo }}</h4>
                <p style="font-size:.9rem;color:#666;">{{ Str::limit($post->descripcion, 80) }}</p>
                <div style="display:flex;justify-content:space-between;margin-top:10px;padding-top:10px;border-top:1px solid #f5f5f5;">
                  <small style="color:#999;">{{ $post->created_at->diffForHumans() }}</small>
                  <span style="color:var(--terra);font-weight:600;font-size:.85rem;">{{ __('Leer más →') }}</span>
                </div>
              </div>
            </div>
          </a>
        @empty
          <div class="empty-state" style="grid-column:1/-1;">
            <div class="empty-state-ico">🐾</div>
            <div class="empty-state-title">{{ __('Aún no hay publicaciones') }}</div>
            <p class="empty-state-desc">{{ __('Cuando este usuario publique algo, aparecerá aquí.') }}</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

<!--MODAL AÑADIR / EDITAR MASCOTA -->
<div class="pet-overlay" id="petModalOverlay" onclick="closePetModal(event)">
  <div class="pet-modal" onclick="event.stopPropagation()">
    <div style="padding:24px 28px 0;display:flex;align-items:center;justify-content:space-between;">
      <h2 id="petModalTitle" style="font-family:'Fraunces',serif;font-size:1.3rem;font-weight:700;margin:0;">
        {{ __('🐾 Añadir mascota') }}
      </h2>
      <button onclick="closePetModal()"
              style="background:var(--soft);border:none;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:13px;color:var(--muted);display:flex;align-items:center;justify-content:center;">✕</button>
    </div>

    <form id="petForm" onsubmit="submitPetForm(event)" style="padding:20px 28px 28px;">

      <!-- Imágenes existentes (edit) -->
      <div id="petExistingImagesWrap" style="display:none;margin-bottom:14px;">
        <label style="font-size:.82rem;font-weight:600;color:var(--muted);display:block;margin-bottom:6px;">
          {{ __('Fotos actuales (clic en ✕ para quitar)') }}
        </label>
        <div id="petExistingImages" style="display:flex;flex-wrap:wrap;gap:4px;"></div>
      </div>

      <!-- Subir imágenes -->
      <div class="form-group" style="margin-bottom:14px;">
        <label style="font-size:.83rem;font-weight:600;">
          📷 {{ __('Fotos') }} <span style="color:var(--muted);font-weight:400;">({{ __('máx. 5') }})</span>
        </label>
        <div class="pet-img-input-wrap" style="min-height:48px;">
          <span style="font-size:.82rem;color:var(--muted);pointer-events:none;">
            📁 {{ __('Selecciona imágenes (JPG/PNG · máx. 2 MB c/u)') }}
          </span>
          <input type="file" id="petImagesInput" accept="image/*" multiple onchange="handlePetImages(this)">
        </div>
        <div id="petImgPreviewList" style="display:flex;flex-wrap:wrap;gap:4px;margin-top:6px;"></div>
      </div>

      <!-- Nombre + Especie -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label style="font-size:.83rem;font-weight:600;">{{ __('Nombre *') }}</label>
          <input type="text" id="petNombre" name="nombre" class="fi"
                 placeholder="{{ __('Ej: Firulais') }}" required maxlength="100">
        </div>
        <div class="form-group" style="margin:0;">
          <label style="font-size:.83rem;font-weight:600;">{{ __('Especie') }}</label>
          <input type="text" id="petEspecie" name="especie" class="fi"
                 placeholder="{{ __('Ej: Perro') }}" maxlength="50">
        </div>
      </div>

      <!-- Raza + Edad -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label style="font-size:.83rem;font-weight:600;">{{ __('Raza') }}</label>
          <input type="text" id="petRaza" name="raza" class="fi"
                 placeholder="{{ __('Ej: Golden Retriever') }}" maxlength="50">
        </div>
        <div class="form-group" style="margin:0;">
          <label style="font-size:.83rem;font-weight:600;">{{ __('Edad (años)') }}</label>
          <input type="number" id="petEdad" name="edad" class="fi"
                 placeholder="0" min="0" max="100">
        </div>
      </div>

      <!-- Descripción -->
      <div class="form-group" style="margin-bottom:16px;">
        <label style="font-size:.83rem;font-weight:600;">{{ __('Descripción') }}</label>
        <textarea id="petDescripcion" name="descripcion" class="fi" rows="3"
                  placeholder="{{ __('Cuenta algo sobre tu mascota...') }}"
                  style="resize:vertical;font-family:'DM Sans',sans-serif;"></textarea>
      </div>

      <!-- Vacunas -->
      <div class="form-group" style="margin-bottom:18px;">
        <label style="font-size:.83rem;font-weight:600;">💉 {{ __('Vacunas registradas') }}</label>
        <p style="font-size:.74rem;color:var(--muted);margin:4px 0 10px;">
          {{ __('Selecciona las vacunas que tiene tu mascota.') }}
        </p>
        <div id="vaccineTagsContainer" style="display:flex;flex-wrap:wrap;gap:7px;"></div>
      </div>

      <!-- Botón añadir recordatorio (solo edición) -->
      <div id="petReminderSection" style="display:none;align-items:center;gap:8px;margin-bottom:16px;
           padding:12px 14px;background:var(--soft);border-radius:var(--r-s);border:1.5px solid var(--border);">
        <i class="fa-solid fa-bell" style="color:var(--terra);"></i>
        <span style="font-size:.83rem;color:var(--muted);flex:1;">
          {{ __('Añade recordatorios para vacunas, revisiones…') }}
        </span>
        <button type="button" class="btn-sm" onclick="closePetModal();openAddReminderModal(currentPetId);">
          <i class="fa-solid fa-plus"></i> {{ __('Recordatorio') }}
        </button>
      </div>

      <!-- Footer -->
      <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:14px;border-top:1px solid var(--border);">
        <button type="button" class="btn-s" onclick="closePetModal()">{{ __('Cancelar') }}</button>
        <button type="submit" id="petSubmitBtn" class="btn-p">{{ __('Añadir mascota') }}</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL DETALLE DE MASCOTA -->
<div class="pet-overlay" id="petDetailOverlay" onclick="closePetDetailModal(event)">
  <div class="pet-detail-modal" onclick="event.stopPropagation()">

    <!-- Galería -->
    <div style="position:relative;background:var(--soft);">
      <img id="petDetailGalImg" src="" alt=""
           style="width:100%;height:300px;object-fit:cover;display:block;">
      <button class="gal-arr l pet-detail-gal-arr" onclick="petDetailNavDir(-1)">
        <i class="fa-solid fa-chevron-left"></i>
      </button>
      <button class="gal-arr r pet-detail-gal-arr" onclick="petDetailNavDir(1)">
        <i class="fa-solid fa-chevron-right"></i>
      </button>
      <div class="gal-nav" id="petDetailGalDots"></div>
      <button onclick="closePetDetailModal()" class="modal-close-btn">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <!-- Cuerpo -->
    <div style="padding:24px 28px 30px;">

      <h2 id="petDetailNombre"
          style="font-family:'Fraunces',serif;font-size:1.7rem;font-weight:700;color:var(--dark);margin-bottom:6px;"></h2>
      <div id="petDetailMeta"
           style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:14px;font-size:.83rem;color:var(--muted);"></div>

      <!-- Acciones del dueño (JS las inyecta) -->
      <div id="petDetailOwnerActions"
           style="display:none;flex-wrap:wrap;gap:8px;margin-bottom:18px;padding-bottom:18px;border-bottom:1px solid var(--border);"></div>

      <p id="petDetailDesc"
         style="font-size:.88rem;line-height:1.75;color:var(--txt);margin-bottom:20px;"></p>

      <!-- Vacunas -->
      <div id="petDetailVaccineSection" style="margin-bottom:20px;">
        <div style="font-family:'Fraunces',serif;font-size:1rem;font-weight:700;margin-bottom:10px;display:flex;align-items:center;gap:8px;">
          <i class="fa-solid fa-syringe" style="color:var(--terra)"></i> {{ __('Vacunas') }}
        </div>
        <div id="petDetailVaccineList" style="display:flex;flex-wrap:wrap;gap:7px;"></div>
      </div>

      <!-- Recordatorios -->
      <div id="petDetailReminderSection">
        <div style="font-family:'Fraunces',serif;font-size:1rem;font-weight:700;margin-bottom:10px;display:flex;align-items:center;gap:8px;">
          <i class="fa-solid fa-bell" style="color:var(--terra)"></i> {{ __('Recordatorios') }}
        </div>
        <div id="petDetailReminderList"></div>
      </div>

    </div>
  </div>
</div>

<!-- MODAL AÑADIR RECORDATORIO -->
<div class="pet-overlay" id="reminderModalOverlay" onclick="closeReminderModal(event)">
  <div class="pet-modal" style="max-width:440px;" onclick="event.stopPropagation()">
    <div style="padding:24px 28px 0;display:flex;align-items:center;justify-content:space-between;">
      <h2 style="font-family:'Fraunces',serif;font-size:1.2rem;font-weight:700;margin:0;">
        🔔 {{ __('Añadir recordatorio') }}
      </h2>
      <button onclick="closeReminderModal()"
              style="background:var(--soft);border:none;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:13px;color:var(--muted);display:flex;align-items:center;justify-content:center;">✕</button>
    </div>

    <form id="reminderForm" onsubmit="submitReminderForm(event)" style="padding:20px 28px 28px;">
      <div class="form-group" style="margin-bottom:12px;">
        <label style="font-size:.83rem;font-weight:600;">{{ __('Título *') }}</label>
        <input type="text" id="reminderTitulo" class="fi"
               placeholder="{{ __('Ej: Vacuna anual de la Rabia') }}" required maxlength="100">
      </div>
      <div class="form-group" style="margin-bottom:12px;">
        <label style="font-size:.83rem;font-weight:600;">{{ __('Descripción') }}</label>
        <textarea id="reminderMensaje" class="fi" rows="3"
                  placeholder="{{ __('Notas adicionales...') }}"
                  style="resize:vertical;font-family:'DM Sans',sans-serif;"></textarea>
      </div>
      <div class="form-group" style="margin-bottom:20px;">
        <label style="font-size:.83rem;font-weight:600;">📅 {{ __('Fecha y hora *') }}</label>
        <input type="datetime-local" id="reminderFecha" class="fi" required
               min="{{ now()->addHour()->format('Y-m-d\TH:i') }}">
        <small style="font-size:.72rem;color:var(--muted);margin-top:4px;display:block;">
          {{ __('Recibirás notificaciones 5 días, 2 días, 12 h y 1 h antes.') }}
        </small>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:14px;border-top:1px solid var(--border);">
        <button type="button" class="btn-s" onclick="closeReminderModal()">{{ __('Cancelar') }}</button>
        <button type="submit" id="reminderSubmitBtn" class="btn-p">{{ __('Guardar recordatorio') }}</button>
      </div>
    </form>
  </div>
</div>

<!-- SCRIPTS -->
<script>
/* Control de pestañas */
function switchTab(btn, tabId) {
  document.querySelectorAll('.profile-tabs .profile-tab').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-valoraciones').style.display = 'none';
  document.getElementById('tab-animales').style.display     = 'none';
  btn.classList.add('active');
  document.getElementById(tabId).style.display = '';
}

/* Valoraciones (estrellitas) */
document.addEventListener('DOMContentLoaded', function () {
  const scrollbarW = window.innerWidth - document.documentElement.clientWidth;
  document.documentElement.style.setProperty('--scrollbar-compensation', scrollbarW + 'px');

  const starContainer = document.getElementById('starRating');
  const ratingInput   = document.getElementById('ratingInput');
  const rateForm      = document.getElementById('rateForm');
  if (!starContainer) return;
  const stars = starContainer.querySelectorAll('i');
  let selectedValue = 0;

  function valueFromEvent(star, e) {
    const rect = star.getBoundingClientRect();
    return parseInt(star.dataset.index) + ((e.clientX - rect.left) < rect.width / 2 ? 0.5 : 1);
  }
  function paintStars(value, mode) {
    stars.forEach((s, idx) => {
      s.classList.remove('fa-solid','fa-regular','fa-star','fa-star-half-stroke','star-lit','star-preview');
      if (value >= idx+1) { s.classList.add('fa-solid','fa-star'); if(mode==='selected') s.classList.add('star-lit'); if(mode==='preview') s.classList.add('star-preview'); }
      else if (value >= idx+.5) { s.classList.add('fa-solid','fa-star-half-stroke'); if(mode==='selected') s.classList.add('star-lit'); if(mode==='preview') s.classList.add('star-preview'); }
      else s.classList.add('fa-regular','fa-star');
    });
  }
  starContainer.addEventListener('mousemove', e => { const s = e.target.closest('i[data-index]'); if(s) paintStars(valueFromEvent(s,e),'preview'); });
  starContainer.addEventListener('mouseleave', () => { selectedValue ? paintStars(selectedValue,'selected') : paintStars(0,'reset'); });
  starContainer.addEventListener('click', e => { const s = e.target.closest('i[data-index]'); if(!s) return; selectedValue = valueFromEvent(s,e); ratingInput.value = selectedValue; paintStars(selectedValue,'selected'); });

  if (rateForm) {
    rateForm.addEventListener('submit', e => {
      e.preventDefault();
      if (!selectedValue) { if(typeof showToast==='function') showToast('{{ __('Debes marcar al menos media estrella para valorar.') }}'); return; }
      const hasRated = {{ auth()->check() && $user->ratings->where('voter_id', Auth::id())->count() > 0 ? 'true' : 'false' }};
      if (hasRated) { document.getElementById('overwriteWarning').style.display = 'block'; } else { rateForm.submit(); }
    });
  }
});

function submitRatingAnyway() {
  document.getElementById('overwriteWarning').style.display = 'none';
  document.getElementById('rateForm').submit();
}
</script>

@endsection
