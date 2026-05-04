<!-- CHAT SIDE PANEL -->
<div id="chatPanel">
  <div class="cp-inner">
    <div class="cp-head">
      <h3><i class="fa-solid fa-comment-dots" style="color:var(--terra);margin-right:6px"></i>Mensajes</h3>
      <input class="cp-search" type="text" placeholder="🔍  Buscar conversaciones...">
    </div>
    <div class="cp-list" id="cpList">
      <!-- generado por JS -->
    </div>
    <div class="cp-foot">
      <button class="btn-new-chat" onclick="openNewMessageModal()">
        <i class="fa-solid fa-plus"></i> Nuevo mensaje
      </button>
      <button class="btn-open-full" onclick="openFullChat()">
        <i class="fa-solid fa-expand"></i> Abrir mensajería completa
      </button>
    </div>
  </div>
</div>