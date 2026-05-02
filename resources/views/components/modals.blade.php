<!-- POST / PUBLICACIÓN MODAL -->
<div class="overlay" id="postOverlay" onclick="closePostModal(event)">
  <div class="modal" id="postModal">
    <div class="modal-gallery">
      <img class="modal-gal-img" id="modalImg" src="" alt="">
      <button class="gal-arr l" onclick="galNav(-1)"><i class="fa-solid fa-chevron-left"></i></button>
      <button class="gal-arr r" onclick="galNav(1)"><i class="fa-solid fa-chevron-right"></i></button>
      <div class="gal-nav" id="galDots"></div>
      <button class="modal-close-btn" onclick="closePostModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <span class="modal-cat-tag" id="modalCatTag"></span>
      <h2 class="modal-title" id="modalTitle"></h2>
      <div class="modal-meta" id="modalMeta"></div>
      <p class="modal-desc" id="modalDesc"></p>
      <div class="animal-box" id="modalAnimalBox"></div>
      <div class="modal-actions">
        <button class="btn-outline" id="likeBtn" onclick="toggleLike(currentPost.id)">
          <i class="fa-regular fa-heart"></i> Like
        </button>
      </div>
      <div class="modal-author" id="modalAuthor"></div>
      <div class="comments-section">
        <div class="modal-sec-title"><i class="fa-solid fa-comments" style="color:var(--terra)"></i> Comentarios</div>
        <div id="modalComments"></div>
        <div class="msg-section mt16">
          <div class="modal-sec-title"><i class="fa-solid fa-paper-plane" style="color:var(--terra)"></i> Enviar un
            mensaje</div>
          <div class="msg-form">
            <input class="msg-input" type="text" placeholder="Escribe tu mensaje sobre esta publicación..."
              id="msgInput">
            <button class="msg-send" onclick="sendMsg()"><i class="fa-solid fa-paper-plane"></i> Enviar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- NUEVA PUBLICACIÓN MODAL -->
<div class="overlay" id="newPostOverlay" onclick="closeNewPostModal(event)">
  <div class="modal" id="newPostModal">
    <div class="modal-header" style="text-align: center;">
      <h2 style="margin: 0 auto; flex-grow: 1; text-align: center;">Crear nueva publicación</h2>
      <button class="modal-close-btn" onclick="closeNewPostModal()" style="position: absolute; right: 16px; top: 16px;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <form id="newPostForm" enctype="multipart/form-data">
      <div class="modal-body npp-body">
        <div class="form-group">
          <label for="postCategory">Categoría *</label>
          <select id="postCategory" name="category_id" required>
            <option value="">Selecciona una categoría</option>
            <option value="1">🏠 Adoptar mascota</option>
            <option value="2">🔍 Mascota perdida o robada</option>
            <option value="3">❤️ Apoyar animales</option>
          </select>
        </div>
        <div class="form-group">
          <label for="postTitle">Título *</label>
          <input type="text" id="postTitle" name="titulo" placeholder="Ej: Se busca perro dorado" required maxlength="200">
        </div>
        <div class="form-group">
          <label for="postDesc">Descripción *</label>
          <textarea id="postDesc" name="descripcion" placeholder="Cuéntanos más detalles sobre el animal o la situación..." rows="4" required></textarea>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="provincia">Provincia *</label>
            <input type="text" id="provincia" name="provincia" placeholder="Ej: Madrid" required maxlength="50">
          </div>
          <div class="form-group">
            <label for="ciudad">Ciudad *</label>
            <input type="text" id="ciudad" name="ciudad" placeholder="Ej: Madrid" required maxlength="100">
          </div>
        </div>
        <div class="form-divider">Información del animal (opcional)</div>
        <div class="form-row">
          <div class="form-group">
            <label for="animalNombre">Nombre</label>
            <input type="text" id="animalNombre" name="animal_nombre" placeholder="Ej: Firulais" maxlength="100">
          </div>
          <div class="form-group">
            <label for="animalEspecie">Especie</label>
            <input type="text" id="animalEspecie" name="animal_especie" placeholder="Ej: Perro" maxlength="50">
          </div>
          <div class="form-group">
            <label for="animalRaza">Raza</label>
            <input type="text" id="animalRaza" name="animal_raza" placeholder="Ej: Golden Retriever" maxlength="50">
          </div>
        </div>
        <div class="form-group">
          <label for="postImages">Imágenes</label>
          <div class="file-input-wrapper">
            <input type="file" id="postImages" name="images[]" multiple accept="image/*" onchange="previewImages(this)">
            <span class="file-label">📁 Selecciona hasta 10 imágenes (máx. 2MB cada una)</span>
          </div>
          <div id="imagePreviewContainer" style="margin-top: 12px; display: none;">
            <div style="font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--text-secondary);">Archivos seleccionados:</div>
            <div id="imagePreviewList" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
          </div>
        </div>
        <input type="hidden" name="estado" value="activa">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-outline" onclick="closeNewPostModal()">Cancelar</button>
        <button type="submit" class="btn-p">Publicar</button>
      </div>
    </form>
  </div>
</div>

<!-- CHAT COMPLETO (SUPERPUESTO/EMERGENTE) -->
<div class="overlay" id="chatOverlayEl" onclick="closeFullChat(event)"></div>

<!-- CHAT COMPLETO MODAL -->
<div class="full-chat" id="fullChatModal" style="position:fixed;">
  <button class="fc-close-btn" onclick="closeFullChat()"><i class="fa-solid fa-xmark"></i></button>

  <!-- SIDEBAR: Lista de chats -->
  <div class="fc-sidebar">
    <div class="fc-sb-head">
      <h2>Mensajería</h2>
      <input class="cp-search" type="text" placeholder="🔍  Buscar conversación...">
    </div>
    <div class="fc-list" id="fcList"></div>
    <div class="fc-sb-foot">
      <button class="btn-new-chat" onclick="showToast('Nuevo chat disponible próximamente 🐾')">
        <i class="fa-solid fa-plus"></i> Nuevo chat / Grupo
      </button>
    </div>
  </div>

  <!-- Main: panel de mensajes -->
  <div class="fc-main">
    <div class="fc-main-head" id="fcMainHead">
      <div class="fc-main-head-info">
        <div class="fc-active-av" id="fcActiveAv">💬</div>
        <div>
          <div style="font-size:.88rem;font-weight:700;" id="fcActiveName">Selecciona una conversación</div>
          <div style="display:flex;align-items:center;gap:5px;font-size:.72rem;color:var(--muted);">
            <span class="online-dot" id="fcOnlineDot" style="background:var(--muted)"></span>
            <span id="fcActiveStatus">—</span>
          </div>
        </div>
      </div>
      <div style="display:flex;gap:8px;">
        <button class="hdr-icon-btn" style="border-color:var(--border);"
          onclick="showToast('Videollamada: próximamente 🐾')"><i class="fa-solid fa-video"></i></button>
        <button class="hdr-icon-btn" style="border-color:var(--border);"
          onclick="showToast('Más opciones próximamente 🐾')"><i class="fa-solid fa-ellipsis"></i></button>
      </div>
    </div>
    <div class="fc-messages" id="fcMessages">
      <div
        style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:var(--muted);gap:12px;">
        <div style="font-size:2.5rem;">💬</div>
        <p style="font-size:.85rem;">Selecciona una conversación para empezar</p>
      </div>
    </div>
    <div class="fc-input-wrap" id="fcInputWrap" style="display:none;flex-direction:column;gap:6px;">
    <!-- Preview del archivo seleccionado -->
    <div id="fcFilePreview" style="display:none;padding:6px 12px;background:var(--soft);border-radius:8px;font-size:0.8rem;display:none;align-items:center;gap:8px;">
        <i class="fa-solid fa-paperclip" style="color:var(--terra);"></i>
        <span id="fcFilePreviewName" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
        <button onclick="clearFcFile()" style="background:none;border:none;cursor:pointer;color:var(--muted);">✕</button>
      </div>
      <div style="display:flex;gap:8px;align-items:flex-end;width:100%;">
        <!-- Input file oculto -->
        <input type="file" id="fcFileInput" style="display:none;"
              accept="image/*,video/*,.pdf,.doc,.docx,.zip,.rar"
              onchange="previewFcFile(this)">
        <button class="hdr-icon-btn" style="border-color:var(--border);flex-shrink:0;"
                onclick="document.getElementById('fcFileInput').click()">
          <i class="fa-solid fa-paperclip"></i>
        </button>
        <textarea class="fc-textarea" id="fcMsgInput" rows="1" placeholder="Escribe un mensaje..."
          onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendFcMsg()}"
          oninput="autoResize(this)"></textarea>
        <button class="fc-send" onclick="sendFcMsg()"><i class="fa-solid fa-paper-plane"></i></button>
      </div>
    </div>
  </div>
</div>

<!-- DROPDOWN NOTIFICACIONES -->
<div id="notifDropdown"
     style="display:flex;opacity:0;pointer-events:none;position:fixed;top:60px;right:16px;width:360px;max-height:480px;
            background:var(--cream);
            border:1px solid var(--border);
            border-radius:14px;
            box-shadow:var(--sh-l);
            z-index:1000;overflow:hidden;flex-direction:column;">
  <div style="padding:14px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
    <span style="font-weight:700;font-size:0.95rem;">🔔 Notificaciones</span>
    <button onclick="markAllNotificationsRead()"
      style="background:none;border:none;cursor:pointer;color:var(--terra);font-size:0.75rem;">
      Marcar todas como leídas
    </button>
  </div>
  <div id="notifList" style="overflow-y:auto;max-height:400px;">
    <div style="text-align:center;padding:40px 20px;color:var(--muted);font-size:0.85rem;">
      Cargando notificaciones...
    </div>
  </div>
</div>

<!-- LIGHTBOX -->
<div id="lightboxOverlay" onclick="if(event.target===this)closeLightbox()"
    style="display:flex;pointer-events:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.92);align-items:center;justify-content:center;">
  <button onclick="closeLightbox()" style="position:absolute;top:16px;right:20px;background:none;border:none;color:#fff;font-size:1.8rem;cursor:pointer;">✕</button>
  <button class="lb-arr" onclick="lightboxNav(-1)" style="position:absolute;left:16px;background:rgba(255,255,255,0.15);border:none;color:#fff;font-size:2rem;padding:10px 16px;border-radius:50%;cursor:pointer;">‹</button>
  <img id="lightboxImg" src="" style="max-width:90vw;max-height:90vh;object-fit:contain;border-radius:8px;">
  <button class="lb-arr" onclick="lightboxNav(1)" style="position:absolute;right:16px;background:rgba(255,255,255,0.15);border:none;color:#fff;font-size:2rem;padding:10px 16px;border-radius:50%;cursor:pointer;">›</button>
  <div id="lightboxCounter" style="position:absolute;bottom:20px;color:#fff;font-size:0.9rem;opacity:0.7;"></div>
</div>