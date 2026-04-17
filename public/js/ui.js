// Funciones de interfaz de usuario para la manipulación del DOM (abrir modales, cambiar secciones)

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

// Sección de navegacin
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

// Gestión de categorías
let _catTabs;
function _getCatTabs() { return _catTabs || (_catTabs = document.querySelectorAll('.cat-tab')); }

function setCategory(btn, id) {
  _getCatTabs().forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  currentCat = id;
  renderPosts(id);
  DOM.paTitle.textContent = catTitles[id];
}

// Gestión de modal
function openPostModal(id) {
  const post = allPostsFlat.find(p => p.id === id);
  if (!post) return;
  currentPost = post;
  currentGalIdx = 0;

  updateGallery();
  const dots = document.getElementById('galDots');
  dots.innerHTML = post.images.map((_, i) => `<button class="gal-dot ${i===0?'act':''}" onclick="setGalIdx(${i})"></button>`).join('');
  const showArr = post.images.length > 1;
  document.querySelectorAll('.gal-arr').forEach(a => a.style.display = showArr ? '' : 'none');

  const ct = catInfo[post.cat];
  const catEl = document.getElementById('modalCatTag');
  catEl.textContent = ct.label;
  catEl.className = 'modal-cat-tag ' + ct.cls;

  document.getElementById('modalTitle').textContent = post.title;
  document.getElementById('modalDesc').textContent = post.desc;

  const meta = document.getElementById('modalMeta');
  meta.innerHTML = `
    <span class="modal-meta-i"><i class="fa-solid fa-location-dot"></i>${post.ciudad}, ${post.provincia}</span>
    <span class="modal-meta-i"><i class="fa-solid fa-calendar"></i>${post.date}</span>
    <span class="modal-meta-i"><i class="fa-regular fa-heart"></i>${post.likes} likes</span>
    ${post.especie ? `<span class="modal-meta-i"><i class="fa-solid fa-paw"></i>${post.especie}</span>` : ''}
  `;

  const animalBox = document.getElementById('modalAnimalBox');
  if (post.animal) {
    animalBox.style.display = 'flex';
    animalBox.innerHTML = `
      ${post.animal  ? `<div class="ai-item"><div class="ai-lbl">Nombre</div><div class="ai-val">${post.animal}</div></div>` : ''}
      ${post.especie ? `<div class="ai-item"><div class="ai-lbl">Especie</div><div class="ai-val">${post.especie}</div></div>` : ''}
      ${post.raza    ? `<div class="ai-item"><div class="ai-lbl">Raza</div><div class="ai-val">${post.raza}</div></div>` : ''}
      ${post.edad    ? `<div class="ai-item"><div class="ai-lbl">Edad</div><div class="ai-val">${post.edad}</div></div>` : ''}
    `;
  } else {
    animalBox.style.display = 'none';
  }

  document.getElementById('modalAuthor').innerHTML = `
    <img src="${post.authorImg}" alt="${post.author}" onerror="this.src='https://i.pravatar.cc/40?img=1'">
    <div>
      <div class="modal-author-name">${post.author}</div>
      <div class="modal-author-role">${post.authorType === 'protectora' ? '🏥 Protectora verificada' : post.authorType === 'organizacion' ? '🌟 Organización' : '👤 Usuario'}</div>
    </div>
    <div class="modal-author-btns">
      <button class="btn-outline" onclick="openFullChat();showToast('Chat iniciado con ${post.author} 💬')"><i class="fa-solid fa-comment"></i> Mensaje</button>
      <button class="btn-outline" onclick="showToast('Perfil de ${post.author} — disponible próximamente')"><i class="fa-solid fa-user"></i> Perfil</button>
    </div>
  `;

  document.getElementById('modalComments').innerHTML = `
    <div class="comment-item">
      <img class="comment-av" src="https://i.pravatar.cc/40?img=14" alt="">
      <div class="comment-bubble">
        <div class="comment-name">@mariap</div>
        <div class="comment-txt">¡Qué monada! Me encantaría adoptarle, ¿tienes más fotos?</div>
        <div class="comment-time">hace 5 horas</div>
      </div>
    </div>
    <div class="comment-item">
      <img class="comment-av" src="https://i.pravatar.cc/40?img=28" alt="">
      <div class="comment-bubble">
        <div class="comment-name">@carlosa</div>
        <div class="comment-txt">Pasé por la protectora el fin de semana, es un cielo. ¡Ojalá encuentre familia pronto!</div>
        <div class="comment-time">hace 2 horas</div>
      </div>
    </div>
  `;

  document.getElementById('postOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closePostModal(e) {
  if (e && e.target !== document.getElementById('postOverlay')) return;
  document.getElementById('postOverlay').classList.remove('open');
  document.body.style.overflow = '';
}

function updateGallery() {
  if (!currentPost) return;
  document.getElementById('modalImg').src = currentPost.images[currentGalIdx];
  document.querySelectorAll('.gal-dot').forEach((d,i) => d.classList.toggle('act', i === currentGalIdx));
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

// Panel de chat
function toggleChatPanel() {
  chatPanelOpen = !chatPanelOpen;
  DOM.chatPanel.classList.toggle('open', chatPanelOpen);
  DOM.mainHeader.classList.toggle('chat-open', chatPanelOpen);
  DOM.mainContent.classList.toggle('chat-open', chatPanelOpen);
  DOM.sidebarChatBtn.classList.toggle('chat-on', chatPanelOpen);
}

function renderChatPanel() {
  document.getElementById('cpList').innerHTML = CHATS.map(c => `
    <div class="cp-item" onclick="openFullChatWith(${c.id})">
      <div class="cp-av-circle">${c.av}</div>
      <div class="cp-info">
        <div class="cp-name">${c.name}</div>
        <div class="cp-prev">${c.preview}</div>
      </div>
      <div class="cp-meta">
        <span class="cp-time">${c.time}</span>
        ${c.unread ? `<span class="cp-badge">${c.unread}</span>` : ''}
      </div>
    </div>
  `).join('');
}

// Modal del chat completo
function renderFcList() {
  document.getElementById('fcList').innerHTML = CHATS.map(c => `
    <div class="cp-item" id="fci-${c.id}" onclick="openFcChat(${c.id})">
      <div class="cp-av-circle">${c.av}</div>
      <div class="cp-info">
        <div class="cp-name">${c.name}</div>
        <div class="cp-prev">${c.preview}</div>
      </div>
      <div class="cp-meta">
        <span class="cp-time">${c.time}</span>
        ${c.unread ? `<span class="cp-badge" id="fci-badge-${c.id}">${c.unread}</span>` : ''}
      </div>
    </div>
  `).join('');
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
  document.getElementById('chatOverlayEl').classList.remove('open');
  document.getElementById('fullChatModal').classList.remove('open');
  document.body.style.overflow = '';
}

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