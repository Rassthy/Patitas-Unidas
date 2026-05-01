<!-- PRINCIPAL -->
<section id="sec-principal" class="section">
  <div class="princ-hdr">
    <h2>Publicaciones de la comunidad</h2>
    <p>Explora lo que usuarios, protectoras y organizaciones están compartiendo ahora mismo.</p>
  </div>
  <div class="cat-tabs">
    <button class="cat-tab active c0" onclick="setCategory(this, 0)">
      📋 Todas las publicaciones
    </button>
    <button class="cat-tab c1" onclick="setCategory(this, 1)">
      🏠 Adoptar mascota
    </button>
    <button class="cat-tab c2" onclick="setCategory(this, 2)">
      🔍 Mascota perdida o robada
    </button>
    <button class="cat-tab c3" onclick="setCategory(this, 3)">
      ❤️ Apoyar animales
    </button>
  </div>
  <div class="posts-area">
    <div class="pa-header">
      <div class="pa-title">
        <span id="paTitle">📋 Todas las publicaciones</span>
        <span class="pa-count" id="paCount">0 publicaciones</span>
      </div>
      <div class="pa-filters">
        <select class="pa-select" onchange="filterPosts(this.value)">
          <option value="">Todas las provincias</option>
          <option>Madrid</option>
          <option>Barcelona</option>
          <option>Valencia</option>
          <option>Sevilla</option>
          <option>Málaga</option>
          <option>Zaragoza</option>
          <option>Bilbao</option>
        </select>
        <select class="pa-select">
          <option>Más recientes</option>
          <option>Más populares</option>
          <option>Más antiguas</option>
        </select>
        <button class="new-post-btn" onclick="openNewPostModal()">
          <i class="fa-solid fa-plus"></i> Publicar
        </button>
      </div>
    </div>
    <div class="posts-grid" id="postsGrid"></div>
  </div>
</section>