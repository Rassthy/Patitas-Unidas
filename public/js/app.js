// LOGICA PRINCIPAL DE LA APP E INICIALIZACION

// ========== STATE ==========
let currentCat = 0;
let selectedFiles = new DataTransfer();
let currentPost = null;
let currentGalIdx = 0;
let likedPosts = new Set();
let chatPanelOpen = false;
let activeFcChat = null;
let CHATS = [];
let activeChatId = null;
let chatPollingInterval = null;

// Helper de traducción
const t = (key) => window.i18n?.[key] ?? key;

// ========== CARGA DE CHATS ==========
async function loadChats() {
  if (!window.AUTH_USER_ID) return;
  try {
    const response = await fetch('/chats');
    const data = await response.json();
    CHATS = data.chats || [];
    renderChatPanel();

    const chatDot = document.getElementById('chatDot');
    if (chatDot) {
      const totalUnread = CHATS.reduce((acc, c) => acc + (c.unread || 0), 0);
      chatDot.style.display = totalUnread > 0 ? '' : 'none';
    }
  } catch (error) {
    console.error('Error cargando chats:', error);
  }
}

// ========== CONSTANTES ==========
const catInfo = {
  1: { label: "🏠 Adoptar mascota", cls: "t1" },
  2: { label: "🔍 Perdida / Robada", cls: "t2" },
  3: { label: "❤️ Apoyar animales", cls: "t3" }
};

const catTitles = {
  1: "🏠 Adoptar mascota",
  2: "🔍 Mascota perdida o robada",
  3: "❤️ Apoyar animales"
};

// ========== CARGA DE PUBLICACIONES ==========
let allPosts = [];
let currentFilter = '';

async function loadPosts() {
  try {
    const params = new URLSearchParams();
    params.append('category_id', currentCat);
    if (currentFilter) params.append('provincia', currentFilter);

    const response = await fetch(`/posts?${params}`);
    const data = await response.json();
    allPosts = data.posts.data || [];
    renderPosts(currentCat, currentFilter);
  } catch (error) {
    console.error('Error loading posts:', error);
    showToast(t('Error al cargar publicaciones'));
  }
}

function filterPosts(provincia) {
  currentFilter = provincia;
  loadPosts();
}

function renderPosts(catId, filter = '') {
  if (!DOM.paCount || !DOM.postsGrid) return;

  const posts = allPosts.filter(p => !filter || p.provincia === filter);
  const word = posts.length !== 1 ? t('publicaciones') : t('publicacion');
  DOM.paCount.textContent = posts.length + ' ' + word;

  const getBadgeInfo = (catId, post) => {
    const id = catId === 0 ? post.category_id : catId;
    return {
      class: id === 1 ? 'pc-badge-adopt' : id === 2 ? 'pc-badge-lost' : 'pc-badge-support',
      icon: id === 1 ? '🏠' : id === 2 ? '🔍' : '❤️'
    };
  };

  DOM.postsGrid.innerHTML = posts.length ? posts.map(p => {
    const postBadge = getBadgeInfo(catId, p);

    const imgSrc = (p.images && p.images.length > 0)
      ? `/storage/${p.images[0].url}`
      : '/img/defaults/foto_post_generica.png';

    const autorNombre = p.author ? p.author.nombre : 'Usuario';
    const autorFoto = p.author && p.author.foto_perfil
      ? `/storage/${p.author.foto_perfil}`
      : '/img/defaults/foto_perfil_generica.png';

    const autorLabel = p.author ? `@${p.author.username}` : 'Anónimo';

    return `
      <div class="post-card" data-id="${p.id}">
        <div class="pc-img-wrap">
          <img class="pc-img"
            src="${imgSrc}"
            alt="${p.titulo}"
            loading="lazy">
          <span class="pc-badge ${postBadge.class}">
            ${postBadge.icon}${p.animal_especie ? ' ' + p.animal_especie : ''}
          </span>
        </div>
        <div class="pc-body">
          <div class="pc-loc">📍 ${p.ciudad}, ${p.provincia}</div>
          <h4>${p.titulo}</h4>
          <p>${p.descripcion}</p>
          <div class="pc-foot">
            <div class="pc-author">
              <img class="pc-author-av"
                   src="${autorFoto}"
                   alt="${autorNombre}"
                   loading="lazy">
              <span>${autorLabel}</span>
            </div>
            <button class="btn-sm" data-id="${p.id}">${t('Saber más')}</button>
          </div>
        </div>
      </div>
    `;
  }).join('') : `
    <div style="text-align:center;padding:60px 20px;color:var(--muted);">
      <div style="font-size:2.5rem;margin-bottom:12px">🐾</div>
      <p>${t('No hay publicaciones en esta zona todavía.')}</p>
    </div>`;
}

// ENVIAR MENSAJE
async function sendMsg() {
  const input = document.getElementById('msgInput');
  if (!input.value.trim()) { showToast(t('Escribe un comentario antes de enviar 💬')); return; }

  try {
    const response = await fetch(`/posts/${currentPost.id}/comments`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ comentario: input.value.trim() }),
    });

    if (response.ok) {
      input.value = '';
      loadComments(currentPost.id);
      showToast(t('Comentario enviado ✉️'));
    } else {
      showToast(t('Error al enviar comentario'));
    }
  } catch (error) {
    console.error('Error:', error);
    showToast(t('Error de conexión'));
  }
}

// FUNCIONES COMPLETAS DEL CHAT
let activeChatUserId = null;

async function openFcChat(id) {
  activeChatId = id;
  document.querySelectorAll('.fc-list .cp-item').forEach(i => i.classList.remove('selected'));
  const el = document.getElementById('fci-' + id);
  if (el) el.classList.add('selected');

  try {
    const response = await fetch(`/chats/${id}`);
    if (!response.ok) throw new Error('No se pudo obtener el chat');

    const data = await response.json();
    const chat = data.chat;

    activeChatUserId = chat.other_user_id ?? null;
    activeChatUsername = chat.other_username ?? chat.username ?? null;

    const avEl = document.getElementById('fcActiveAv');
    if (avEl) {
      if (chat.foto) {
        avEl.style.backgroundImage = `url(${chat.foto})`;
        avEl.style.backgroundSize = 'cover';
        avEl.textContent = '';
      } else {
        avEl.style.backgroundImage = '';
        avEl.textContent = chat.nombre ? chat.nombre.substring(0, 2).toUpperCase() : '??';
      }
    }

    document.getElementById('fcActiveName').textContent = chat.nombre;
    document.getElementById('fcActiveStatus').textContent = '';
    document.getElementById('fcInputWrap').style.display = 'flex';

    renderMessages(chat.messages);

    await loadChats();
    renderFcList();

    clearInterval(chatPollingInterval);
    chatPollingInterval = setInterval(() => pollMessages(id), 5000);

  } catch (error) {
    console.error('Error cargando chat:', error);
    showToast(t('Error al cargar la conversación 🐾'));
  }
}

function renderMessages(messages) {
  console.log('📨 Mensajes:', messages.map(m => ({ tipo: m.tipo, texto: m.texto, mine: m.mine })));

  const msgs = document.getElementById('fcMessages');
  if (!messages.length) {
    msgs.innerHTML = `<div style="text-align:center;padding:40px;color:var(--muted);font-size:0.85rem;">${t('Sé el primero en escribir 🐾')}</div>`;
    return;
  }

  msgs.innerHTML = `<div class="sys-msg">${t('🔒 Inicio de la conversación')}</div>` +
    messages.map(m => {
      const bubble = renderBubbleContent(m);
      return `
        <div class="bubble-wrap ${m.mine ? 'mine' : ''}">
          <div class="bubble ${m.mine ? 'mine' : 'theirs'}">${bubble}
            <span style="font-size:0.65rem;opacity:0.6;display:block;text-align:right;margin-top:2px;">${m.time}</span>
          </div>
        </div>
      `;
    }).join('');

  msgs.scrollTop = msgs.scrollHeight;
}

function renderBubbleContent(m) {
  const url = `/storage/${m.texto}`;
  switch (m.tipo) {
    case 'imagen':
      return `<img src="${url}" style="max-width:100%;border-radius:8px;cursor:zoom-in;display:block;" onclick="openLightbox(['${url}'], 0)">`;
    case 'video':
      return `<video controls style="max-width:100%;border-radius:8px;display:block;"><source src="${url}"></video>`;
    case 'pdf':
      return `<a href="${url}" target="_blank" style="display:flex;align-items:center;gap:8px;color:inherit;text-decoration:none;">
        <i class="fa-solid fa-file-pdf" style="font-size:1.5rem;color:var(--terra);"></i>
        <span style="font-size:0.8rem;">Ver PDF</span>
      </a>`;
    case 'archivo':
      const nombre = m.texto.split('/').pop();
      return `<a href="${url}" target="_blank" style="display:flex;align-items:center;gap:8px;color:inherit;text-decoration:none;">
        <i class="fa-solid fa-file-zipper" style="font-size:1.5rem;"></i>
        <span style="font-size:0.8rem;">${nombre}</span>
      </a>`;
    default:
      return escHtml(m.texto);
  }
}

async function pollMessages(chatId) {
  if (!activeChatId || activeChatId !== chatId) return;
  try {
    const response = await fetch(`/chats/${chatId}`);
    const data = await response.json();
    renderMessages(data.chat.messages);
  } catch { }
}

async function sendFcMsg() {
  if (!activeChatId) return;
  const inp = document.getElementById('fcMsgInput');
  const fileInput = document.getElementById('fcFileInput');
  const txt = inp.value.trim();
  const file = fileInput?.files?.[0];

  if (!txt && !file) return;

  const formData = new FormData();
  if (txt) formData.append('contenido', txt);
  if (file) formData.append('archivo', file);
  formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

  try {
    const response = await fetch(`/chats/${activeChatId}/messages`, {
      method: 'POST',
      body: formData
    });

    if (response.ok) {
      inp.value = '';
      inp.style.height = '';
      if (fileInput) { fileInput.value = ''; }
      document.getElementById('fcFilePreview').style.display = 'none';
      const data = await (await fetch(`/chats/${activeChatId}`)).json();
      renderMessages(data.chat.messages);
      loadChats();
    } else {
      showToast(t('Error al enviar mensaje'));
    }
  } catch (error) {
    showToast(t('Error de conexión'));
  }
}

function autoResize(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 90) + 'px';
}

function escHtml(str) {
  return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

async function startChatWith(userId) {
  if (!window.AUTH_USER_ID) { openLoginModal(); return; }
  if (userId === window.AUTH_USER_ID) { showToast(t('No puedes chatear contigo mismo 😄')); return; }

  try {
    const response = await fetch('/chats', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ user_id: userId, is_group: false })
    });

    const data = await response.json();
    closePostModal();
    await loadChats();
    openFullChat();
    setTimeout(() => openFcChat(data.chat.id), 100);
  } catch (error) {
    showToast(t('Error al iniciar chat'));
  }
}

// ========== FUNCIONES DE LOGIN ==========
function doLogin() {
  closeLoginModal();
  document.body.style.overflow = '';
  document.getElementById('headerLoginBtn').classList.add('hidden');
  document.getElementById('userChip').classList.remove('hidden');
  showToast(t('¡Bienvenido de vuelta a PatitasUnidas! 🐾'));
}

function doRegister() {
  closeLoginModal();
  document.body.style.overflow = '';
  document.getElementById('headerLoginBtn').classList.add('hidden');
  document.getElementById('userChip').classList.remove('hidden');
  showToast(t('¡Cuenta creada! Bienvenido a PatitasUnidas 🐾🎉'));
}

function openTermsModal() {
    document.getElementById('termsModal').classList.add('open');
}

function closeTermsModal() {
    document.getElementById('termsModal').classList.remove('open');
}

// ========== DATOS FAQ / INFO ==========
const faqData = [
  { q: t('faq_q1'), a: t('faq_a1') },
  { q: t('faq_q2'), a: t('faq_a2') },
  { q: t('faq_q3'), a: t('faq_a3') },
  { q: t('faq_q4'), a: t('faq_a4') },
  { q: t('faq_q5'), a: t('faq_a5') },
  { q: t('faq_q6'), a: t('faq_a6') },
];

function renderFaq() {
  const list = document.getElementById('faqList');
  if (!list) return;

  list.innerHTML = faqData.map((f, i) => `
    <div class="faq-item" id="faq-${i}">
      <div class="faq-q" onclick="toggleFaq(${i})">
        <span>${f.q}</span>
        <i class="fa-solid fa-chevron-down faq-arrow"></i>
      </div>
      <div class="faq-a">${f.a}</div>
    </div>
  `).join('');
}

// ========== INICIALIZACION ==========
DOM.init();
loadPosts();
renderFaq();
renderChatPanel();
loadChats();
if (window.AUTH_USER_ID) loadNotifications();

document.addEventListener('click', e => {
  const card = e.target.closest('.post-card[data-id]');
  if (card) { openPostModal(+card.dataset.id); return; }
  const smBtn = e.target.closest('.btn-sm[data-id]');
  if (smBtn) { e.stopPropagation(); openPostModal(+smBtn.dataset.id); }
});

function previewImages(input) {
  const container = document.getElementById('imagePreviewContainer');
  const list = document.getElementById('imagePreviewList');

  Array.from(input.files).forEach(file => {
    const yaExiste = Array.from(selectedFiles.files).some(f => f.name === file.name);
    if (!yaExiste) selectedFiles.items.add(file);
  });

  if (selectedFiles.files.length > 10) {
    showToast(t('Máximo 10 imágenes permitidas'));
    const dt = new DataTransfer();
    Array.from(selectedFiles.files).slice(0, 10).forEach(f => dt.items.add(f));
    selectedFiles = dt;
  }

  input.files = selectedFiles.files;

  if (selectedFiles.files.length > 0) {
    container.style.display = 'block';
    list.innerHTML = Array.from(selectedFiles.files).map((file, idx) => `
      <div id="prev-${idx}" style="display:flex;align-items:center;gap:6px;padding:6px 10px;background:var(--bg-secondary);border-radius:6px;font-size:0.8rem;color:var(--text);max-width:100%;">
        <i class="fa-solid fa-image" style="color:var(--terra);"></i>
        <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="${file.name}">${file.name}</span>
        <span style="color:var(--muted);font-size:0.75rem;">(${(file.size / 1024).toFixed(1)}KB)</span>
        <button type="button" onclick="removeFile(${idx})" style="margin-left:auto;background:none;border:none;cursor:pointer;color:var(--muted);font-size:1rem;">✕</button>
      </div>
    `).join('');
  } else {
    container.style.display = 'none';
    list.innerHTML = '';
  }
}

function removeFile(idx) {
  const dt = new DataTransfer();
  Array.from(selectedFiles.files)
    .filter((_, i) => i !== idx)
    .forEach(f => dt.items.add(f));
  selectedFiles = dt;

  document.getElementById('postImages').files = selectedFiles.files;
  previewImages({ files: new DataTransfer().files });

  const list = document.getElementById('imagePreviewList');
  const container = document.getElementById('imagePreviewContainer');
  if (selectedFiles.files.length === 0) {
    container.style.display = 'none';
    list.innerHTML = '';
  } else {
    container.style.display = 'block';
    list.innerHTML = Array.from(selectedFiles.files).map((file, i) => `
      <div style="display:flex;align-items:center;gap:6px;padding:6px 10px;background:var(--bg-secondary);border-radius:6px;font-size:0.8rem;color:var(--text);max-width:100%;">
        <i class="fa-solid fa-image" style="color:var(--terra);"></i>
        <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="${file.name}">${file.name}</span>
        <span style="color:var(--muted);font-size:0.75rem;">(${(file.size / 1024).toFixed(1)}KB)</span>
        <button type="button" onclick="removeFile(${i})" style="margin-left:auto;background:none;border:none;cursor:pointer;color:var(--muted);font-size:1rem;">✕</button>
      </div>
    `).join('');
  }
}

document.getElementById('newPostForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);

  try {
    const response = await fetch('/posts', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: formData,
    });

    const data = await response.json();

    if (response.ok) {
      showToast(t('Publicación creada correctamente! 🎉'));
      closeNewPostModal();
      document.getElementById('newPostForm').reset();
      selectedFiles = new DataTransfer();
      document.getElementById('imagePreviewContainer').style.display = 'none';
      loadPosts();
    } else {
      if (data.errors && typeof data.errors === 'object') {
        const firstError = Object.values(data.errors)[0];
        const errorMsg = Array.isArray(firstError) ? firstError[0] : firstError;
        showToast(t('Error') + ': ' + errorMsg);
      } else if (data.message) {
        showToast(t('Error') + ': ' + data.message);
      } else {
        showToast(t('Error al crear publicación. Inténtalo de nuevo.'));
      }
    }
  } catch (error) {
    console.error('Error:', error);
    showToast(t('Error de conexión. Verifica tu conexión a internet.'));
  }
});

const _postOverlay = document.getElementById('postOverlay');
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    closePostModal();
    closeFullChat();
    closeLoginModal();
    document.body.style.overflow = '';
  }
  if (_postOverlay.classList.contains('open')) {
    if (e.key === 'ArrowLeft') galNav(-1);
    if (e.key === 'ArrowRight') galNav(1);
  }
});

(function () {
  function initSearch(searchEl, listId) {
    if (!searchEl) return;
    let searchTimer;
    searchEl.addEventListener('input', () => {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(() => {
        const q = searchEl.value.toLowerCase().trim();
        document.querySelectorAll(`#${listId} .cp-item`).forEach(item => {
          const name = item.querySelector('.cp-name')?.textContent.toLowerCase() ?? '';
          item.style.display = (!q || name.includes(q)) ? '' : 'none';
        });
      }, 150);
    });
  }

  initSearch(document.querySelector('#chatPanel .cp-search'), 'cpList');

  document.addEventListener('click', e => {
    if (e.target.closest('.btn-open-full, #sidebarChatBtn')) {
      setTimeout(() => {
        initSearch(document.querySelector('#fullChatModal .cp-search'), 'fcList');
      }, 100);
    }
  });
})();

document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const postIdToOpen = urlParams.get('open_post');
  if (postIdToOpen) {
    if (typeof openPostModal === 'function') {
      openPostModal(postIdToOpen);
    }
  }
});