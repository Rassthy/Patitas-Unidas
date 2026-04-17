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
    <div class="fc-input-wrap" id="fcInputWrap" style="display:none;">
      <button class="hdr-icon-btn" style="border-color:var(--border);"
        onclick="showToast('Adjuntar archivos: próximamente 🐾')"><i class="fa-solid fa-paperclip"></i></button>
      <textarea class="fc-textarea" id="fcMsgInput" rows="1" placeholder="Escribe un mensaje..."
        onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendFcMsg()}"
        oninput="autoResize(this)"></textarea>
      <button class="fc-send" onclick="sendFcMsg()"><i class="fa-solid fa-paper-plane"></i></button>
    </div>
  </div>
</div>