@extends('layouts.app')

@section('content')
<div id="profile-container">

  <!--  CABECERA  -->
  <div class="profile-header">
    <div class="profile-banner {{ $user->banner ? '' : 'no-image' }}"
      style="{{ $user->banner ? 'background-image:url(' . $user->banner_url . ');' : '' }}">

      <!-- Botones integrados en el banner -->
      @if(auth()->check() && auth()->id() === $user->id)
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
    // Leemos la privacidad (por defecto mostramos apellidos y ocultamos fecha)
    $mostrarApellidos = ($user->user_settings['mostrar_apellidos'] ?? '1') == '1';
    $mostrarFecha     = ($user->user_settings['mostrar_fecha']     ?? '0') == '1';
    @endphp

    <div class="profile-card">
      <div class="profile-top">
        <!-- AVATAR Y SU ESTADO -->
        <div class="profile-avatar-wrap">
          <img class="profile-avatar"
               src="{{ $user->foto_perfil_url }}"
               alt="{{ __('Foto de :nombre', ['nombre' => $user->nombre]) }}">

          <!-- Preparado para la lógica de online, away, offline -->
          <div class="profile-status status-online" title="{{ __('Conectado') }}"></div>
        </div>

        <!-- INFO -->
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
              <p>
                {{ __('Miembro desde :fecha', [
                    'fecha' => \Carbon\Carbon::parse($user->created_at)
                                ->locale(app()->getLocale())
                                ->translatedFormat('F Y')
                ]) }}
              </p>
            </div>

            <!-- Aplicamos la privacidad a la fecha de nacimiento -->
            @if($user->fecha_nacimiento && $mostrarFecha)
            <div class="meta-item">
              <i class="fa-solid fa-cake-candles" style="color:var(--terra)"></i>
              <p>{{ $user->fecha_nacimiento->format('d/m/Y') }}</p>
            </div>
            @endif
          </div>
        </div>

        <!-- ETIQUETA DERECHA -->
        <div class="profile-right-area" style="text-align: right;">
          <div class="profile-type">
            <i class="fa-solid fa-user"></i> {{ __('Usuario') }}
          </div>

          <!-- BOTÓN DE MENSAJE en perfil -->
          @auth
            @if(Auth::id() !== $user->id)
              <br>
              <button class="btn-primary"
                      onclick="startChatWith({{ $user->id }})"
                      style="margin-top: 5px;">
                <i class="fa-solid fa-comment"></i> {{ __('Mensaje') }}
              </button>
            @endif
          @endauth
        </div>

      </div>
    </div>
  </div>

  <!--  ESTADÍSTICAS (3 Elementos)  -->
  <div class="profile-sections">
    <div class="profile-stats">
      <div class="stat-item">
        <div class="stat-item-num">0</div>
        <div class="stat-item-lbl">{{ __('Publicaciones') }}</div>
      </div>
      <div class="stat-item">
        <div class="stat-item-num">0</div>
        <div class="stat-item-lbl">{{ __('Donaciones') }}</div>
      </div>
      <div class="stat-item">
        <div class="stat-item-num">0</div>
        <div class="stat-item-lbl">{{ __('Valoraciones enviadas') }}</div>
      </div>
    </div>

    <!--  SECCIÓN MIXTA: VALORACIONES O ANIMALES  -->
    <div class="profile-card" style="margin-bottom: 32px;">
      <div class="profile-tabs" style="border-bottom:none; margin-bottom: 16px;">
        <button class="profile-tab active" onclick="switchTab(this,'tab-valoraciones')">
          <i class="fa-solid fa-star"></i> {{ __('Valoraciones') }}
        </button>
        <button class="profile-tab" onclick="switchTab(this,'tab-animales')">
          <i class="fa-solid fa-heart"></i> {{ __('Mis animales') }}
        </button>
      </div>

      <div id="tab-valoraciones" class="profile-tab-content">
        @auth
        @if(Auth::id() !== $user->id)
        <div class="review-form-container">
          <h4>{{ __('Dejar una valoración') }}</h4>
          <form action="{{ route('profile.rate', $user->id) }}" method="POST" id="rateForm">
            @csrf

            <!-- Estrellas (más grandes para facilitar el clic) -->
            <div class="star-rating" id="starRating"
                 style="font-size: 2rem; cursor: pointer; display: inline-flex; gap: 5px;">
              <i class="fa-regular fa-star" data-index="0"></i>
              <i class="fa-regular fa-star" data-index="1"></i>
              <i class="fa-regular fa-star" data-index="2"></i>
              <i class="fa-regular fa-star" data-index="3"></i>
              <i class="fa-regular fa-star" data-index="4"></i>
            </div>
            <input type="hidden" name="puntuacion" id="ratingInput">

            <textarea name="comentario"
                      placeholder="{{ __('Deja un comentario (opcional)...') }}"
                      class="edit-form-textarea"
                      style="min-height: 60px; margin-bottom: 10px;"></textarea>

            <button class="btn-p btn-s" type="submit">{{ __('Enviar valoración') }}</button>
          </form>
        </div>

        <!-- Notificación centrada de sobrescritura (oculta por defecto) -->
        <div id="overwriteWarning"
             style="display:none; position:fixed; top:50%; left:50%;
                    transform:translate(-50%,-50%); background:white; padding:24px;
                    border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.3);
                    z-index:9999; text-align:center; max-width:400px;">
          <div style="font-size:2rem; color:var(--terra); margin-bottom:10px;">
            <i class="fa-solid fa-triangle-exclamation"></i>
          </div>
          <h3 style="margin-top:0;">{{ __('Actualizar valoración') }}</h3>
          <p>{{ __('Ya tienes una valoración en este perfil. Si decides enviar esta, la antigua se borrará y se sustituirá por la nueva.') }}</p>
          <div style="display:flex; gap:10px; justify-content:center; margin-top:20px;">
            <button type="button" class="btn-s"
                    onclick="document.getElementById('overwriteWarning').style.display='none'">
              {{ __('Cancelar') }}
            </button>
            <button type="button" class="btn-p" onclick="submitRatingAnyway()">
              {{ __('Sí, reemplazar') }}
            </button>
          </div>
        </div>
        @endif
        @endauth

        <!-- Listado de valoraciones -->
        <div class="review-list">
          @forelse($user->ratings as $rating)
          <div class="review-item">
            <div class="review-header">
              <strong>{{ $rating->voter->username }}</strong>
              <span class="review-stars">
                @php
                  $entera = floor($rating->puntuacion);
                  $media  = ($rating->puntuacion - $entera) >= 0.5 ? 1 : 0;
                  $vacia  = 5 - $entera - $media;
                @endphp
                @for($i = 0; $i < $entera; $i++)
                  <i class="fa-solid fa-star" style="color:var(--terra)"></i>
                @endfor
                @if($media)
                  <i class="fa-solid fa-star-half-stroke" style="color:var(--terra)"></i>
                @endif
                @for($i = 0; $i < $vacia; $i++)
                  <i class="fa-regular fa-star" style="color:var(--terra)"></i>
                @endfor
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

      <div id="tab-animales" class="profile-tab-content" style="display:none;">
        <div class="empty-state">
          <div class="empty-state-ico">🐶</div>
          <div class="empty-state-title">{{ __('Todavía no tienes animales registrados') }}</div>
          <p class="empty-state-desc">{{ __('Área en construcción...') }}</p>
        </div>
      </div>
    </div>

    <!-- SECCIÓN INFERIOR: PUBLICACIONES -->
    <div class="profile-card">
      <div class="edit-section-title" style="margin-bottom: 24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; width:100%;">
          <span>
            <i class="fa-solid fa-paw" style="color:var(--terra)"></i>
            {{ __('Publicaciones') }}
          </span>
          <span class="badge"
                style="font-size:0.8rem; background:var(--terra); color:white;
                       padding:4px 12px; border-radius:20px;">
            {{ $user->posts->count() }}
          </span>
        </div>
      </div>

      <div class="posts-grid"
           style="display:grid; grid-template-columns:repeat(auto-fill, minmax(250px,1fr)); gap:20px;">
        @forelse($user->posts as $post)
          <a href="{{ route('posts.show', $post->id) }}"
             class="post-card-link"
             style="text-decoration:none; color:inherit;">
            <div class="post-card"
                 style="border:1px solid #eee; border-radius:12px; overflow:hidden;
                        background:white; transition:transform 0.2s; height:100%;">
              @if($post->imagen_url)
                <img src="{{ $post->imagen_url }}"
                     alt="{{ $post->titulo }}"
                     style="width:100%; height:160px; object-fit:cover;">
              @endif

              <div style="padding:15px;">
                <span style="font-size:0.7rem; font-weight:700; text-transform:uppercase; color:var(--terra);">
                  {{ $post->category->name ?? __('General') }}
                </span>

                <h4 style="margin:8px 0; font-size:1.1rem; color:var(--dark);">
                  {{ $post->titulo }}
                </h4>
                <p style="font-size:0.9rem; color:#666;">
                  {{ Str::limit($post->contenido, 80) }}
                </p>

                <div style="display:flex; justify-content:space-between; margin-top:10px;
                            padding-top:10px; border-top:1px solid #f5f5f5;">
                  <small style="color:#999;">{{ $post->created_at->diffForHumans() }}</small>
                  <span style="color:var(--terra); font-weight:600; font-size:0.85rem;">
                    {{ __('Leer más →') }}
                  </span>
                </div>
              </div>
            </div>
          </a>
        @empty
          <div class="empty-state" style="grid-column: 1 / -1;">
            <div class="empty-state-ico">🐾</div>
            <div class="empty-state-title">{{ __('Aún no hay publicaciones') }}</div>
            <p class="empty-state-desc">{{ __('Cuando este usuario publique algo, aparecerá aquí.') }}</p>
          </div>
        @endforelse
      </div>
    </div>

  </div>
</div>

<script>
  /* Control de pestañas */
  function switchTab(btn, tabId) {
    document.querySelectorAll('.profile-tabs .profile-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-valoraciones').style.display = 'none';
    document.getElementById('tab-animales').style.display    = 'none';
    btn.classList.add('active');
    document.getElementById(tabId).style.display = '';
  }

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
      const rect   = star.getBoundingClientRect();
      const isHalf = (e.clientX - rect.left) < rect.width / 2;
      return parseInt(star.dataset.index) + (isHalf ? 0.5 : 1);
    }

    function paintStars(value, mode) {
      stars.forEach((s, idx) => {
        s.classList.remove(
          'fa-solid', 'fa-regular',
          'fa-star', 'fa-star-half-stroke',
          'star-lit', 'star-preview'
        );
        if (value >= idx + 1) {
          s.classList.add('fa-solid', 'fa-star');
          if (mode === 'selected') s.classList.add('star-lit');
          if (mode === 'preview')  s.classList.add('star-preview');
        } else if (value >= idx + 0.5) {
          s.classList.add('fa-solid', 'fa-star-half-stroke');
          if (mode === 'selected') s.classList.add('star-lit');
          if (mode === 'preview')  s.classList.add('star-preview');
        } else {
          s.classList.add('fa-regular', 'fa-star');
        }
      });
    }

    starContainer.addEventListener('mousemove', function (e) {
      const star = e.target.closest('i[data-index]');
      if (!star) return;
      paintStars(valueFromEvent(star, e), 'preview');
    });

    starContainer.addEventListener('mouseleave', function () {
      if (selectedValue) {
        paintStars(selectedValue, 'selected');
      } else {
        paintStars(0, 'reset');
      }
    });

    starContainer.addEventListener('click', function (e) {
      const star = e.target.closest('i[data-index]');
      if (!star) return;
      selectedValue     = valueFromEvent(star, e);
      ratingInput.value = selectedValue;
      paintStars(selectedValue, 'selected');
    });

    if (rateForm) {
      rateForm.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!selectedValue) {
          if (typeof showToast === 'function') {
            showToast('{{ __('Debes marcar al menos media estrella para valorar.') }}', 'error');
          }
          return;
        }

        const hasRated = {{ auth()->check() && $user->ratings->where('voter_id', Auth::id())->count() > 0 ? 'true' : 'false' }};

        if (hasRated) {
          document.getElementById('overwriteWarning').style.display = 'block';
        } else {
          rateForm.submit();
        }
      });
    }
  });

  function submitRatingAnyway() {
    document.getElementById('overwriteWarning').style.display = 'none';
    document.getElementById('rateForm').submit();
  }
</script>
@endsection