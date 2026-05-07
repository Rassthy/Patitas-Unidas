// Funciones de interfaz de usuario para la manipulación del DOM

// DOM Cache
const DOM = {
  postsGrid: null,
  paCount: null,
  paTitle: null,
  chatPanel: null,
  mainHeader: null,
  mainContent: null,
  sidebarChatBtn: null,
  cpList: null,
  toast: null,
  toastMsg: null,
  init() {
    this.postsGrid     = document.getElementById('postsGrid');
    this.paCount       = document.getElementById('paCount');
    this.paTitle       = document.getElementById('paTitle');
    this.chatPanel     = document.getElementById('chatPanel');
    this.mainHeader    = document.getElementById('mainHeader');
    this.mainContent   = document.getElementById('mainContent');
    this.sidebarChatBtn= document.getElementById('sidebarChatBtn');
    this.cpList        = document.getElementById('cpList');
    this.toast         = document.getElementById('toast');
    this.toastMsg      = document.getElementById('toastMsg');
  }
};

// Sección de navegacion
let _navBtns, _sections, _sbBtns;
function _getNavBtns()  { return _navBtns  || (_navBtns  = document.querySelectorAll('.nav-btn')); }
function _getSections() { return _sections || (_sections = document.querySelectorAll('.section')); }
function _getSbBtns()   { return _sbBtns   || (_sbBtns   = document.querySelectorAll('.sb-btn')); }

function setNav(btn, section) {
  _getNavBtns().forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  showSection(section);
}

function showSection(name) {
  _getSections().forEach(s => s.classList.remove('active'));
  document.getElementById('sec-'+name).classList.add('active');
  const map = { bienvenida:0, principal:1, faq:2 };
  const btns = _getNavBtns();
  btns.forEach((b,i) => b.classList.toggle('active', i === map[name]));
  _getSbBtns().forEach(b => b.classList.remove('active'));
  if (name === 'bienvenida') _getSbBtns()[0].classList.add('active');
  if (name === 'principal')  _getSbBtns()[1].classList.add('active');
  window.scrollTo(0,0);
}

function clearNavSelection() {
  _getNavBtns().forEach(b => b.classList.remove('active'));
  _getSbBtns().forEach(b => b.classList.remove('active'));
}

document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const requested = params.get('tab') || window.location.hash.replace('#', '');
  if (requested && document.getElementById('sec-' + requested)) {
    showSection(requested);
    return;
  }
  if (document.querySelector('.profile-container') || !document.getElementById('sec-bienvenida')) {
    clearNavSelection();
  }
});

// Gestión de categorías
let _catTabs;
function _getCatTabs() { return _catTabs || (_catTabs = document.querySelectorAll('.cat-tab')); }

function setCategory(btn, id) {
  _getCatTabs().forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  currentCat = id;
  loadPosts();
  const titles = {
    0: t('📋 Todas las publicaciones'),
    1: t('🏠 Adoptar mascota'),
    2: t('🔍 Mascota perdida o robada'),
    3: t('❤️ Apoyar animales')
  };
  DOM.paTitle.textContent = titles[id];
}

// Gestión de modal
async function openPostModal(id) {
  try {
    const response = await fetch(`/posts/${id}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    });
    const data = await response.json();
    const post = data.post;
    if (!post) return;

    currentPost = post;
    currentGalIdx = 0;

    updateGallery();
    document.getElementById('modalImg').onclick = () => {
      if (!currentPost.images || !currentPost.images.length) return;
      const urls = currentPost.images.map(img => `/storage/${img.url}`);
      openLightbox(urls, currentGalIdx);
    };
    document.getElementById('modalImg').style.cursor = 'zoom-in';

    const dots = document.getElementById('galDots');
    dots.innerHTML = (post.images || []).map((_, i) =>
      `<button class="gal-dot ${i===0?'act':''}" onclick="setGalIdx(${i})"></button>`
    ).join('');
    const showArr = post.images && post.images.length > 1;
    document.querySelectorAll('.gal-arr').forEach(a => a.style.display = showArr ? '' : 'none');

    const ct = catInfo[post.category_id];
    const catEl = document.getElementById('modalCatTag');
    catEl.textContent = ct.label;
    catEl.className = 'modal-cat-tag ' + ct.cls;

    document.getElementById('modalTitle').textContent = post.titulo;
    document.getElementById('modalDesc').textContent = post.descripcion;

    const meta = document.getElementById('modalMeta');
    meta.innerHTML = `
      <span class="modal-meta-i"><i class="fa-solid fa-location-dot"></i>${post.ciudad}, ${post.provincia}</span>
      <span class="modal-meta-i"><i class="fa-solid fa-calendar"></i>${new Date(post.created_at).toLocaleDateString('es-ES')}</span>
      <span class="modal-meta-i" id="modalLikeCount"><i class="fa-regular fa-heart"></i>${post.likes_count || 0} likes</span>
      ${post.animal_especie ? `<span class="modal-meta-i"><i class="fa-solid fa-paw"></i>${post.animal_especie}</span>` : ''}
    `;

    const animalBox = document.getElementById('modalAnimalBox');
    if (post.animal_nombre || post.animal_especie || post.animal_raza) {
      animalBox.style.display = 'flex';
      animalBox.innerHTML = `
        ${post.animal_nombre  ? `<div class="ai-item"><div class="ai-lbl">${t('Nombre')}</div><div class="ai-val">${post.animal_nombre}</div></div>` : ''}
        ${post.animal_especie ? `<div class="ai-item"><div class="ai-lbl">${t('Especie')}</div><div class="ai-val">${post.animal_especie}</div></div>` : ''}
        ${post.animal_raza    ? `<div class="ai-item"><div class="ai-lbl">${t('Raza')}</div><div class="ai-val">${post.animal_raza}</div></div>` : ''}
      `;
    } else {
      animalBox.style.display = 'none';
    }

    document.getElementById('modalAuthor').innerHTML = `
      <img src="${post.author.foto_perfil ? `/storage/${post.author.foto_perfil}` : `/img/defaults/foto_perfil_generica.png`}" ...>
      <div>
        <div class="modal-author-name">${post.author.username}</div>
        <div class="modal-author-role">${
          post.author.tipo === 'protectora'   ? t('🏥 Protectora verificada') :
          post.author.tipo === 'organizacion' ? t('🌟 Organización') :
                                                t('👤 Usuario')
        }</div>
      </div>
      <div class="modal-author-btns">
        <button class="btn-outline" onclick="startChatWith(${post.author.id})">
          <i class="fa-solid fa-comment"></i> ${t('Mensaje')}
        </button>
        <button class="btn-outline" onclick="window.location.href='/profile/${post.author.username}'">
          <i class="fa-solid fa-user"></i> ${t('Perfil')}
        </button>
      </div>
    `;

    loadComments(id);

    const likeBtn = document.getElementById('likeBtn');
    likeBtn.classList.toggle('liked', post.liked_by_user);

    const reportBtn = document.getElementById('reportPostBtn');
    if (reportBtn) {
      if (post.author.id === window.AUTH_USER_ID) {
        reportBtn.style.display = 'none';
      } else {
        reportBtn.style.display = '';
        reportBtn.onclick = () => openReportModal('post', post.id, post.author.id);
      }
    }
    if (reportBtn) reportBtn.onclick = () =>
      openReportModal('post', post.id, post.author.id);

    document.getElementById('postOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';

  } catch (error) {
    console.error('Error loading post:', error);
    showToast(t('Error al cargar la publicación'));
  }
}

function closePostModal(e) {
  if (e && e.target !== document.getElementById('postOverlay')) return;
  document.getElementById('postOverlay').classList.remove('open');
  document.body.style.overflow = '';
}

function updateGallery() {
  const modalImg = document.getElementById('modalImg');
  if (!currentPost || !currentPost.images || !currentPost.images.length) {
    modalImg.src = '/img/defaults/post_default.png';
    document.querySelectorAll('.gal-arr').forEach(a => a.style.display = 'none');
    document.getElementById('galDots').innerHTML = '';
    return;
  }
  const img = currentPost.images[currentGalIdx];
  modalImg.src = `/storage/${img.url}`;
  document.querySelectorAll('.gal-dot').forEach((d,i) => d.classList.toggle('act', i === currentGalIdx));
}

let lightboxImages = [];
let lightboxIdx = 0;

function openLightbox(images, startIdx = 0) {
  lightboxImages = images;
  lightboxIdx = startIdx;
  renderLightbox();
  const overlay = document.getElementById('lightboxOverlay');
  overlay.style.pointerEvents = 'all';
  overlay.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeLightbox() {
  const overlay = document.getElementById('lightboxOverlay');
  overlay.style.pointerEvents = 'none';
  overlay.classList.remove('open');
  document.body.style.overflow = '';
}

function lightboxNav(dir) {
  lightboxIdx = (lightboxIdx + dir + lightboxImages.length) % lightboxImages.length;
  renderLightbox();
}

function renderLightbox() {
  document.getElementById('lightboxImg').src = lightboxImages[lightboxIdx];
  document.getElementById('lightboxCounter').textContent =
    lightboxImages.length > 1 ? `${lightboxIdx + 1} / ${lightboxImages.length}` : '';
  document.querySelectorAll('.lb-arr').forEach(a =>
    a.style.display = lightboxImages.length > 1 ? '' : 'none');
}

function galNav(dir) {
  if (!currentPost) return;
  currentGalIdx = (currentGalIdx + dir + currentPost.images.length) % currentPost.images.length;
  updateGallery();
}

function setGalIdx(i) {
  currentGalIdx = i;
  updateGallery();
}

// Login Modal
let _loginOverlay, _lmTabs, _loginForm, _registerForm;
function _initLoginEls() {
  if (_loginOverlay) return;
  _loginOverlay = document.getElementById('loginOverlay');
  _lmTabs       = document.querySelectorAll('.lm-tab');
  _loginForm    = document.getElementById('loginForm');
  _registerForm = document.getElementById('registerForm');
}

function openLoginModal() {
  _initLoginEls();
  _loginOverlay.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeLoginModal(e) {
  _initLoginEls();
  if (e && e.target !== _loginOverlay) return;
  _loginOverlay.classList.remove('open');
  document.body.style.overflow = '';
}

function setLoginTab(btn, tab) {
  _initLoginEls();
  _lmTabs.forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  _loginForm.classList.toggle('hidden', tab !== 'login');
  _registerForm.classList.toggle('hidden', tab !== 'register');
}

function setRegisterTipo(tipo) {
  const usuarioBtn = document.getElementById('tipoUsuarioBtn');
  const orgBtn = document.getElementById('tipoOrgBtn');
  const formUsuario = document.getElementById('registerFormUsuario');
  const formOrg = document.getElementById('registerFormOrg');

  if (tipo === 'usuario') {
    usuarioBtn.style.background = 'var(--terra)';
    usuarioBtn.style.color = '#fff';
    orgBtn.style.background = 'transparent';
    orgBtn.style.color = 'var(--terra)';
    formUsuario.style.display = '';
    formOrg.style.display = 'none';
  } else {
    orgBtn.style.background = 'var(--terra)';
    orgBtn.style.color = '#fff';
    usuarioBtn.style.background = 'transparent';
    usuarioBtn.style.color = 'var(--terra)';
    formUsuario.style.display = 'none';
    formOrg.style.display = '';
  }
}

function togglePassword(btn) {
    const input = btn.closest('div').querySelector('input');
    const icon  = btn.querySelector('i');
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    icon.classList.toggle('fa-eye',      !isHidden);
    icon.classList.toggle('fa-eye-slash', isHidden);
}

// Panel de chat
function toggleChatPanel() {
  chatPanelOpen = !chatPanelOpen;
  DOM.chatPanel.classList.toggle('open', chatPanelOpen);
  DOM.mainHeader.classList.toggle('chat-open', chatPanelOpen);
  DOM.mainContent.classList.toggle('chat-open', chatPanelOpen);
  DOM.sidebarChatBtn.classList.toggle('chat-on', chatPanelOpen);
}

function renderChatPanel() {
  const list = document.getElementById('cpList');
  if (!list) return;

  if (!CHATS.length) {
    list.innerHTML = `<div style="text-align:center;padding:30px;color:var(--muted);font-size:0.85rem;">${t('No tienes conversaciones aún 🐾')}</div>`;
    return;
  }

  list.innerHTML = CHATS.map(c => `
    <div class="cp-item" onclick="openFullChatWith(${c.id})">
      <div class="cp-av-circle" style="${c.foto ? `background-image:url(${c.foto});background-size:cover;` : ''}">
        ${c.foto ? '' : c.nombre.substring(0,2).toUpperCase()}
      </div>
      <div class="cp-info">
        <div class="cp-name">${c.nombre}</div>
        <div class="cp-prev">${c.last_msg || t('Sin mensajes aún')}</div>
      </div>
      <div class="cp-meta">
        <span class="cp-time">${c.last_time || ''}</span>
        ${c.unread ? `<span class="cp-badge">${c.unread}</span>` : ''}
      </div>
    </div>
  `).join('');
}

function renderFcList() {
  const list = document.getElementById('fcList');
  if (!list) return;

  if (!CHATS.length) {
    list.innerHTML = `<div style="text-align:center;padding:30px;color:var(--muted);font-size:0.85rem;">${t('No tienes conversaciones aún 🐾')}</div>`;
    return;
  }

  list.innerHTML = CHATS.map(c => `
    <div class="cp-item" id="fci-${c.id}" onclick="openFcChat(${c.id})">
      <div class="cp-av-circle" style="${c.foto ? `background-image:url(${c.foto});background-size:cover;` : ''}">
        ${c.foto ? '' : c.nombre.substring(0,2).toUpperCase()}
      </div>
      <div class="cp-info">
        <div class="cp-name">${c.nombre}</div>
        <div class="cp-prev">${c.last_msg || t('Sin mensajes aún')}</div>
      </div>
      <div class="cp-meta">
        <span class="cp-time">${c.last_time || ''}</span>
        ${c.unread ? `<span class="cp-badge" id="fci-badge-${c.id}">${c.unread}</span>` : ''}
      </div>
    </div>
  `).join('');
}

function openNewMessageModal() {
  const chatModal = document.getElementById('fullChatModal');
  if (!chatModal.classList.contains('open')) openFullChat();
  const overlay = document.getElementById('newMsgOverlay');
  overlay.style.opacity = '1';
  overlay.style.pointerEvents = 'all';
  document.getElementById('newMsgUserInput').value = '';
  document.getElementById('newMsgUserResults').innerHTML = '';
  setTimeout(() => document.getElementById('newMsgUserInput')?.focus(), 150);
}

function closeNewMessageModal() {
  const overlay = document.getElementById('newMsgOverlay');
  overlay.style.opacity = '0';
  overlay.style.pointerEvents = 'none';
}

let searchUsersTimer;
async function searchUsersForChat(query) {
  clearTimeout(searchUsersTimer);
  if (query.length < 2) {
    document.getElementById('newMsgUserResults').innerHTML = '';
    return;
  }
  searchUsersTimer = setTimeout(async () => {
    try {
      const response = await fetch(`/users/search?q=${encodeURIComponent(query)}`);
      const data = await response.json();
      const results = document.getElementById('newMsgUserResults');
      if (!data.users || !data.users.length) {
        results.innerHTML = `<div style="font-size:0.8rem;color:var(--muted);padding:4px;">${t('No se encontraron usuarios')}</div>`;
        return;
      }
      results.innerHTML = data.users.map(u => `
        <div onclick="startChatWith(${u.id}); closeNewMessageModal();"
            style="display:flex;align-items:center;gap:10px;padding:8px 10px;cursor:pointer;
                    border-radius:8px;transition:background 0.15s;"
            onmouseover="this.style.background='var(--soft)'"
            onmouseout="this.style.background='transparent'">
          <img src="${u.foto_perfil ? `/storage/${u.foto_perfil}` : '/img/defaults/foto_perfil_generica.png'}"
              style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;">
          <span style="font-size:0.85rem;color:var(--txt);font-weight:500;">${u.username}</span>
        </div>
      `).join('');
    } catch (error) {
      console.error('Error buscando usuarios:', error);
    }
  }, 300);
}

function openFullChat() {
  renderFcList();
  document.getElementById('chatOverlayEl').classList.add('open');
  document.getElementById('fullChatModal').classList.add('open');
  document.body.style.overflow = 'hidden';
  if (chatPanelOpen) toggleChatPanel();
}

function openFullChatWith(id) {
  openFullChat();
  setTimeout(() => openFcChat(id), 60);
}

function closeFullChat(e) {
  if (e && e.target !== document.getElementById('chatOverlayEl')) return;
  clearInterval(chatPollingInterval);
  activeChatId = null;
  closeNewMessageModal();
  document.getElementById('chatOverlayEl').classList.remove('open');
  document.getElementById('fullChatModal').classList.remove('open');
  document.body.style.overflow = '';
}

function previewFcFile(input) {
  const file = input.files[0];
  if (!file) return;
  const preview = document.getElementById('fcFilePreview');
  document.getElementById('fcFilePreviewName').textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + 'KB)';
  preview.style.display = 'flex';
}

function clearFcFile() {
  document.getElementById('fcFileInput').value = '';
  document.getElementById('fcFilePreview').style.display = 'none';
}

function toggleChatOptions(e) {
  e.stopPropagation();
  const dropdown = document.getElementById('chatOptionsDropdown');
  const isOpen = dropdown.style.opacity === '1';
  dropdown.style.opacity = isOpen ? '0' : '1';
  dropdown.style.pointerEvents = isOpen ? 'none' : 'all';
}

function viewChatUserProfile() {
  if (typeof toggleChatOptions === 'function') {
    toggleChatOptions({ stopPropagation: () => {} });
  }
  if (activeChatUsername) {
    window.location.href = `/profile/${activeChatUsername}`;
  } else {
    showToast(t('No se pudo encontrar el nombre de usuario para este perfil 🐾'));
  }
}

document.addEventListener('click', e => {
  const dropdown = document.getElementById('chatOptionsDropdown');
  if (dropdown && !dropdown.contains(e.target)) {
    dropdown.style.opacity = '0';
    dropdown.style.pointerEvents = 'none';
  }
});

// Notificaciones del toast
let toastTimer;
function showToast(msg) {
  DOM.toastMsg.textContent = msg;
  DOM.toast.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => DOM.toast.classList.remove('show'), 3000);
}

// FAQ
let _openFaqIdx = -1;
function toggleFaq(i) {
  if (_openFaqIdx >= 0 && _openFaqIdx !== i) {
    document.getElementById('faq-' + _openFaqIdx).classList.remove('open');
  }
  const item = document.getElementById('faq-' + i);
  const opening = !item.classList.contains('open');
  item.classList.toggle('open', opening);
  _openFaqIdx = opening ? i : -1;
}

async function loadComments(postId) {
  try {
    const response = await fetch(`/posts/${postId}/comments`);
    const data = await response.json();
    const commentsEl = document.getElementById('modalComments');

    if (!data.comments || !data.comments.length) {
      commentsEl.innerHTML = `<div style="text-align:center;padding:20px;color:var(--muted);font-size:0.85rem;">${t('Sé el primero en comentar 🐾')}</div>`;
      return;
    }

    const currentUserId = window.AUTH_USER_ID ?? null;

    const renderComment = (comment, isReply = false) => {
      const foto = comment.user?.foto_perfil
        ? `/storage/${comment.user.foto_perfil}`
        : `/img/defaults/foto_perfil_generica.png`;
      const username = comment.user?.username ?? 'Usuario';
      const fecha = new Date(comment.created_at);
      const fechaStr = fecha.toLocaleDateString('es-ES');
      const horaStr = fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
      const esPropio = currentUserId && comment.author_id === currentUserId;

      return `
        <div class="comment-item ${isReply ? 'comment-reply' : ''}" id="comment-${comment.id}"
             style="${isReply ? 'margin-left:44px;margin-top:8px;' : ''}">
          <img class="comment-av" src="${foto}" alt="${username}"
            style="cursor:pointer;"
            onclick="window.location.href='/profile/${username}'"
            onerror="this.src='/img/defaults/foto_perfil_generica.png'">
          <div class="comment-bubble">
            <div class="comment-name"
              style="cursor:pointer;"
              onclick="window.location.href='/profile/${username}'">${username}</div>
            <div class="comment-txt">${comment.comentario}</div>
            <div class="comment-foot" style="display:flex;align-items:center;gap:12px;margin-top:6px;flex-wrap:wrap;">
              <span class="comment-time">${fechaStr} ${horaStr}</span>
              <button onclick="toggleCommentLike(${comment.id}, this)"
                style="background:none;border:none;cursor:pointer;font-size:0.8rem;color:${comment.liked_by_user ? 'var(--terra)' : 'var(--muted)'};"
                data-liked="${comment.liked_by_user}">
                ${comment.liked_by_user ? '❤️' : '🤍'} <span>${comment.likes_count ?? 0}</span>
              </button>
              ${!isReply ? `
                <button onclick="toggleReplyInput(${comment.id}, '${username}', ${postId})"
                  style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:0.75rem;padding:0;">
                  ${t('💬 Responder')}
                </button>` : ''}
              ${esPropio ? `
                <button onclick="deleteComment(${comment.id}, ${postId})"
                  style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:0.75rem;padding:0;margin-left:auto;">
                  ${t('🗑️ Eliminar')}
                </button>` : ''}
              ${!esPropio ? `
                <button onclick="openReportModal('post_comentario', ${comment.id}, ${comment.author_id})"
                  style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:0.75rem;padding:0;margin-left:auto;">
                  ${t('🚨 Reportar')}
                </button>` : ''}
            </div>
          </div>
        </div>
        ${!isReply ? `<div id="reply-input-${comment.id}" style="display:none;"></div>` : ''}
      `;
    };

    commentsEl.innerHTML = data.comments.map(comment => {
      const repliesHtml = (comment.replies || []).map(r => renderComment(r, true)).join('');
      return renderComment(comment, false) + repliesHtml;
    }).join('');

  } catch (error) {
    console.error('Error loading comments:', error);
  }
}

function toggleReplyInput(commentId, username, postId) {
  if (!window.AUTH_USER_ID) { showToast(t('Inicia sesión para responder 🐾')); return; }

  const container = document.getElementById(`reply-input-${commentId}`);
  if (!container) return;

  if (container.style.display !== 'none') {
    container.style.display = 'none';
    container.innerHTML = '';
    return;
  }

  container.style.display = 'block';
  container.innerHTML = `
    <div style="margin-left:44px;margin-top:6px;margin-bottom:10px;display:flex;gap:8px;align-items:flex-start;">
      <div style="flex:1;background:var(--bg-secondary);border-radius:10px;padding:8px 12px;border:1px solid var(--border);">
        <span style="font-size:0.75rem;color:var(--terra);font-weight:600;">@${username}</span>
        <textarea id="reply-text-${commentId}"
          placeholder="${t('Escribe tu respuesta...')}"
          rows="2"
          style="width:100%;background:none;border:none;outline:none;resize:none;font-size:0.85rem;color:var(--text);margin-top:4px;font-family:inherit;"
        ></textarea>
      </div>
      <div style="display:flex;flex-direction:column;gap:4px;">
        <button onclick="submitReply(${commentId}, ${postId})"
          style="background:var(--terra);color:#fff;border:none;border-radius:8px;padding:6px 12px;cursor:pointer;font-size:0.8rem;white-space:nowrap;">
          ${t('Enviar')}
        </button>
        <button onclick="toggleReplyInput(${commentId}, '${username}', ${postId})"
          style="background:none;border:1px solid var(--border);border-radius:8px;padding:6px 12px;cursor:pointer;font-size:0.8rem;color:var(--muted);">
          ${t('Cancelar')}
        </button>
      </div>
    </div>
  `;

  document.getElementById(`reply-text-${commentId}`).focus();
}

async function submitReply(parentCommentId, postId) {
  const textarea = document.getElementById(`reply-text-${parentCommentId}`);
  const texto = textarea?.value.trim();
  if (!texto) { showToast(t('Escribe algo antes de responder 💬')); return; }

  try {
    const response = await fetch(`/posts/${postId}/comments`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        comentario: texto,
        parent_comment_id: parentCommentId
      })
    });

    if (response.ok) {
      loadComments(postId);
      showToast(t('Respuesta enviada 💬'));
    } else {
      showToast(t('Error al enviar respuesta'));
    }
  } catch (error) {
    showToast(t('Error de conexión'));
  }
}

// ELIMINAR COMENTARIO
window.deleteComment = async function(commentId, postId) {
    // Al recibir explicitamente (commentId, postId), ya no se confunde.
    
    try {
        const res = await fetch(`/comments/${commentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        if (res.ok) {
            if (typeof showToast === 'function') showToast(window.i18n['Comentario eliminado 🗑️'] || 'Comentario eliminado');
            
            // MAGIA NINJA: Buscamos el comentario en pantalla y lo borramos visualmente
            const divComentario = document.getElementById('comment-' + commentId);
            if (divComentario) {
                divComentario.style.transition = 'all 0.3s ease';
                divComentario.style.opacity = '0';
                divComentario.style.transform = 'scale(0.95)';
                setTimeout(() => divComentario.remove(), 300); // 300ms para que dé tiempo a la animación
            }
        } else if (res.status === 404) {
            if (typeof showToast === 'function') showToast('El comentario ya no existe', 'error');
            // Si da 404 (ya estaba borrado), lo quitamos visualmente de la pantalla también
            const divComentario = document.getElementById('comment-' + commentId);
            if (divComentario) divComentario.remove();
        } else {
            if (typeof showToast === 'function') showToast(window.i18n['Error al eliminar comentario'] || 'Error al eliminar', 'error');
        }
    } catch (error) {
        if (typeof showToast === 'function') showToast(window.i18n['Error de conexión'] || 'Error de conexión', 'error');
    }
};

async function toggleLike(postId) {
  try {
    const response = await fetch(`/posts/${postId}/like`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json',
      },
    });

    if (response.ok) {
      const data = await response.json();
      const likeBtn = document.getElementById('likeBtn');
      const likeIcon = likeBtn.querySelector('i');
      const likeCount = document.getElementById('modalLikeCount');
      likeBtn.classList.toggle('liked', data.liked);
      likeIcon.className = data.liked ? 'fa-solid fa-heart' : 'fa-regular fa-heart';
      likeCount.innerHTML = `<i class="fa-regular fa-heart"></i>${data.likes_count} likes`;
      showToast(data.liked ? t('Like añadido ❤️') : t('Like quitado 💔'));
    } else {
      showToast(t('Error al procesar like'));
    }
  } catch (error) {
    console.error('Error:', error);
    showToast(t('Error de conexión'));
  }
}

async function toggleCommentLike(commentId, btn) {
  if (!window.AUTH_USER_ID) { showToast(t('Inicia sesión para dar like 🐾')); return; }
  try {
    const response = await fetch(`/comments/${commentId}/like`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json',
      }
    });
    if (response.ok) {
      const data = await response.json();
      btn.dataset.liked = data.liked;
      btn.style.color = data.liked ? 'var(--terra)' : 'var(--muted)';
      btn.innerHTML = `${data.liked ? '❤️' : '🤍'} <span>${data.likes_count}</span>`;
    }
  } catch (error) {
    showToast(t('Error de conexión'));
  }
}

function openNewPostModal() {
  document.getElementById('newPostOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeNewPostModal(e) {
  if (e && e.target !== document.getElementById('newPostOverlay')) return;
  document.getElementById('newPostOverlay').classList.remove('open');
  document.body.style.overflow = '';
  document.getElementById('newPostForm').reset();
  document.getElementById('imagePreviewContainer').style.display = 'none';
  document.getElementById('imagePreviewList').innerHTML = '';
}

// ========== NOTIFICACIONES ==========
let notifOpen = false;

function openNotificationsPanel() {
  if (!window.AUTH_USER_ID) { openLoginModal(); return; }
  notifOpen = !notifOpen;
  const dropdown = document.getElementById('notifDropdown');
  dropdown.style.pointerEvents = notifOpen ? 'all' : 'none';
  dropdown.classList.toggle('open', notifOpen);
  if (notifOpen) loadNotifications();
}

document.addEventListener('click', e => {
  if (!notifOpen) return;
  const dropdown = document.getElementById('notifDropdown');
  const btn = e.target.closest('.hdr-icon-btn, .sb-btn');
  if (!dropdown.contains(e.target) && !btn) {
    notifOpen = false;
    dropdown.style.pointerEvents = 'none';
    dropdown.classList.remove('open');
  }
});

async function loadNotifications() {
  try {
    const response = await fetch('/notifications');
    const data = await response.json();
    const list = document.getElementById('notifList');
    const badge = document.getElementById('notifBadge');

    if (!data.notifications || !data.notifications.length) {
      list.innerHTML = `<div style="text-align:center;padding:40px 20px;color:var(--muted);font-size:0.85rem;">${t('Sin notificaciones por ahora 🐾')}</div>`;
      badge.style.display = 'none';
      return;
    }

    const unread = data.notifications.filter(n => !n.leida).length;
    badge.textContent = unread;
    badge.style.display = unread ? '' : 'none';

    list.innerHTML = data.notifications.map(n => {
      const icons = {
        like: '❤️',
        mensaje: '✉️',
        comentario_post: '💬',
        recordatorio_mascota: '🐾',
        reporte: '🚨',
        rating: '⭐',
        sistema: '🔔'
      };
      const icon = icons[n.tipo] ?? '🔔';
      const fecha = new Date(n.created_at);
      const fechaStr = fecha.toLocaleDateString('es-ES');
      const horaStr = fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });

      return `
        <div onclick="markNotificationRead(${n.id}, this)"
             style="display:flex;gap:12px;align-items:flex-start;padding:12px 16px;cursor:pointer;
                    border-bottom:1px solid var(--border);
                    background:${n.leida ? 'transparent' : 'var(--soft)'}
                    transition:background 0.2s;">
          <span style="font-size:1.4rem;line-height:1;">${icon}</span>
          <div style="flex:1;min-width:0;">
            <div style="font-size:0.85rem;color:var(--text);line-height:1.4;">${n.mensaje ?? n.tipo}</div>
            <div style="font-size:0.75rem;color:var(--muted);margin-top:4px;">${fechaStr} ${horaStr}</div>
          </div>
          ${!n.leida ? `<span style="width:8px;height:8px;border-radius:50%;background:var(--terra);flex-shrink:0;margin-top:4px;"></span>` : ''}
        </div>
      `;
    }).join('');

    const notifDot = document.getElementById('notifDot');
    if (notifDot) {
      notifDot.style.display = unread > 0 ? '' : 'none';
    }
  } catch (error) {
    console.error('Error cargando notificaciones:', error);
  }
}

async function markNotificationRead(id, el) {
  try {
    await fetch(`/notifications/${id}`, {
      method: 'PUT',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ leida: true })
    });
    el.style.background = 'transparent';
    const dot = el.querySelector('span[style*="border-radius:50%"]');
    if (dot) dot.remove();
    const badge = document.getElementById('notifBadge');
    const current = parseInt(badge.textContent) || 0;
    if (current > 0) {
      badge.textContent = current - 1;
      if (current - 1 === 0) badge.style.display = 'none';
    }
  } catch (error) {
    console.error('Error marcando notificación:', error);
  }
}

async function markAllNotificationsRead() {
  try {
    const response = await fetch('/notifications');
    const data = await response.json();
    const unread = data.notifications.filter(n => !n.leida);
    await Promise.all(unread.map(n =>
      fetch(`/notifications/${n.id}`, {
        method: 'PUT',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ leida: true })
      })
    ));
    loadNotifications();
    showToast(t('Todas las notificaciones leídas ✅'));
  } catch (error) {
    showToast(t('Error de conexión'));
  }
}

// ========== REPORTES ==========
let reportData = { tipo: null, entidadId: null, reportedUserId: null };

function openReportModal(tipo, entidadId, reportedUserId = null) {
  if (!window.AUTH_USER_ID) { openLoginModal(); return; }
  reportData = { tipo, entidadId, reportedUserId };
  document.getElementById('reportMotivo').value = '';

  const overlay = document.getElementById('reportOverlay');
  overlay.classList.add('open');
  overlay.style.pointerEvents = 'all';
  overlay.style.opacity = '1';

  const chatModal = document.getElementById('fullChatModal');
  const postModal = document.getElementById('postOverlay');
  if (chatModal) chatModal.style.pointerEvents = 'none';
  if (postModal) postModal.style.pointerEvents = 'none';
  document.body.style.overflow = 'hidden';
}

function closeReportModal() {
  const overlay = document.getElementById('reportOverlay');
  overlay.classList.remove('open');
  overlay.style.pointerEvents = 'none';
  overlay.style.opacity = '0';

  const chatModal = document.getElementById('fullChatModal');
  const postModal = document.getElementById('postOverlay');
  if (chatModal) chatModal.style.pointerEvents = '';
  if (postModal) postModal.style.pointerEvents = '';
  document.body.style.overflow = '';
}

async function submitReport() {
  const motivo = document.getElementById('reportMotivo').value;
  if (!motivo) { showToast(t('Selecciona un motivo para el reporte')); return; }

  try {
    const response = await fetch('/reports', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        tipo_entidad:      reportData.tipo,
        entidad_id:        reportData.entidadId,
        reported_user_id:  reportData.reportedUserId,
        motivo:            motivo,
      })
    });

    if (response.ok) {
      closeReportModal();
      showToast(t('Reporte enviado correctamente ✅'));
    } else {
      showToast(t('Error al enviar el reporte'));
    }
  } catch (error) {
    showToast(t('Error de conexión'));
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const postId = urlParams.get('open_post');

  if (postId) {
    if (typeof showSection === 'function') showSection('principal');
    if (typeof openPostModal === 'function') setTimeout(() => openPostModal(postId), 300);
    window.history.replaceState({}, document.title, "/");
  }
});