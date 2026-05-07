<!-- PRINCIPAL -->
<section id="sec-principal" class="section">
  <div class="princ-hdr">
    <h2>{{ __('Publicaciones de la comunidad') }}</h2>
    <p>{{ __('Explora lo que usuarios, protectoras y organizaciones están compartiendo ahora mismo.') }}</p>
  </div>
  <div class="cat-tabs">
    <button class="cat-tab active c0" onclick="setCategory(this, 0)">
      📋 {{ __('Todas las publicaciones') }}
    </button>
    <button class="cat-tab c1" onclick="setCategory(this, 1)">
      🏠 {{ __('Adoptar mascota') }}
    </button>
    <button class="cat-tab c2" onclick="setCategory(this, 2)">
      🔍 {{ __('Mascota perdida o robada') }}
    </button>
    <button class="cat-tab c3" onclick="setCategory(this, 3)">
      ❤️ {{ __('Apoyar animales') }}
    </button>
  </div>
  <div class="posts-area">
    <div class="pa-header">
      <div class="pa-title">
        <span id="paTitle">📋 {{ __('Todas las publicaciones') }}</span>
        <span class="pa-count" id="paCount">0 {{ __('publicaciones') }}</span>
      </div>
      <div class="pa-filters">
        <select class="pa-select" onchange="filterPosts(this.value)">
          <option value="">{{ __('Todas las provincias') }}</option>
          <option>Madrid</option>
          <option>Barcelona</option>
          <option>Valencia</option>
          <option>Sevilla</option>
          <option>Málaga</option>
          <option>Zaragoza</option>
          <option>Bilbao</option>
        </select>
        <select class="pa-select" onchange="sortPosts(this.value)">
          <option value="recientes">{{ __('Más recientes') }}</option>
          <option value="populares">{{ __('Más populares') }}</option>
          <option value="antiguas">{{ __('Más antiguas') }}</option>
        </select>
        <button class="new-post-btn" onclick="openNewPostModal()">
          <i class="fa-solid fa-plus"></i> {{ __('Publicar') }}
        </button>
      </div>
    </div>
    <div class="posts-grid" id="postsGrid"></div>
  </div>
</section>