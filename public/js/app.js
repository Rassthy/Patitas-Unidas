// LOGICA PRINCIPAL DE LA APP E INICIALIZACION

// ========== STATE ==========
let currentCat = 1;
let currentPost = null;
let currentGalIdx = 0;
let likedPosts = new Set();
let chatPanelOpen = false;
let activeFcChat = null;

// ========== DATOS: POSTS / PUBLICACIONES ==========
const postsData = {
  1: [
    {
      id:1, title:"Luna busca su hogar definitivo", cat:1,
      desc:"Luna es una Golden Retriever de 3 años, rescatada de la calle. Es muy cariñosa, juguetona y se lleva bien con niños y otros perros. Está esterilizada, vacunada y desparasitada. Lleva 8 meses en la protectora y merece una segunda oportunidad.",
      animal:"Luna", especie:"Perro", raza:"Golden Retriever", edad:"3 años",
      provincia:"Madrid", ciudad:"Vallecas",
      author:"Protectora Huellas", authorType:"protectora", authorImg:"https://i.pravatar.cc/40?img=12",
      images:["https://images.unsplash.com/photo-1552053831-71594a27632d?w=800&q=80","https://picsum.photos/seed/luna2/800/600","https://picsum.photos/seed/luna3/800/600"],
      likes:47, date:"hace 2 días"
    },
    {
      id:2, title:"Mochi, gatito siamés afectuoso", cat:1,
      desc:"Mochi tiene 1 año y es un gato siamés muy sociable. Le encanta estar con personas, ronronear y jugar. Fue encontrado abandonado y busca un hogar tranquilo. Vacunado y esterilizado.",
      animal:"Mochi", especie:"Gato", raza:"Siamés", edad:"1 año",
      provincia:"Barcelona", ciudad:"Gràcia",
      author:"@gatosbarcelona", authorType:"usuario", authorImg:"https://i.pravatar.cc/40?img=23",
      images:["https://images.unsplash.com/photo-1596854407944-bf87f6fdd49e?w=800&q=80","https://picsum.photos/seed/mochi2/800/600"],
      likes:62, date:"hace 3 días"
    }
  ],
  2: [
    {
      id:7, title:"URGENTE: Max desaparecido en El Retiro", cat:2,
      desc:"Max, pastor alemán de 5 años, desapareció el domingo por la tarde cerca del lago del Retiro en Madrid. Lleva collar azul con placa. Es muy asustadizo, no se acerquen bruscamente. Recompensa por cualquier información.",
      animal:"Max", especie:"Perro", raza:"Pastor Alemán", edad:"5 años",
      provincia:"Madrid", ciudad:"El Retiro",
      author:"@maxfamilymadrid", authorType:"usuario", authorImg:"https://i.pravatar.cc/40?img=11",
      images:["https://images.unsplash.com/photo-1589941013453-ec89f33b5e95?w=800&q=80","https://picsum.photos/seed/max2/800/600"],
      likes:203, date:"hace 1 día"
    }
  ],
  3: [
    {
      id:10, title:"Colonia de gatos en Vallecas necesita ayuda", cat:3,
      desc:"Llevamos años gestionando una colonia de 28 gatos en Vallecas (Madrid). Necesitamos ayuda para pienso, veterinario y mantenimiento del refugio temporal. Cualquier donación ayuda.",
      animal:null, especie:null, raza:null, edad:null,
      provincia:"Madrid", ciudad:"Vallecas",
      author:"@coloniasvallecas", authorType:"organizacion", authorImg:"https://i.pravatar.cc/40?img=77",
      images:["https://images.unsplash.com/photo-1548802673-380ab8ebc7b7?w=800&q=80","https://picsum.photos/seed/colony2/800/600"],
      likes:312, date:"hace 5 días"
    }
  ]
};

// ========== DATOS: CHATS ==========
const CHATS = [
  { id:1, name:'Protectora Huellas', av:'PH', preview:'¡Muchas gracias por tu interés en Luna! 🐕', time:'10:40', unread:2, online:true,
    msgs:[
      {mine:false, text:'¡Hola! Vi que te interesó Luna para adoptar.'},
      {mine:true,  text:'Sí, me parece un amor. ¿Cuándo puedo visitarla?'},
      {mine:false, text:'Este sábado a partir de las 11h. ¿Te viene bien?'},
      {mine:true,  text:'Perfecto, apuntado. ¿Cómo llego?'},
      {mine:false, text:'¡Muchas gracias por tu interés en Luna! 🐕 Te mando la dirección.'}
    ]},
  { id:2, name:'@gatosbarcelona', av:'GB', preview:'Sigue disponible para adopción 😸', time:'hace 1 h', unread:1, online:false,
    msgs:[
      {mine:false, text:'¡Hola! ¿Tienes alguna pregunta sobre Mochi?'},
      {mine:true,  text:'Sí, ¿se lleva bien con perros?'},
      {mine:false, text:'Sigue disponible para adopción 😸 Mochi es muy adaptable.'}
    ]}
];

// ========== CONSTANTES ==========
const catInfo = {
  1:{ label:"🏠 Adoptar mascota", cls:"t1" },
  2:{ label:"🔍 Perdida / Robada", cls:"t2" },
  3:{ label:"❤️ Apoyar animales", cls:"t3" }
};

const catTitles = {
  1:"🏠 Adoptar mascota",
  2:"🔍 Mascota perdida o robada",
  3:"❤️ Apoyar animales"
};

// ========== CARGA DE PUBLICACIONES ==========
const allPostsFlat = [...postsData[1],...postsData[2],...postsData[3]];

function filterPosts(provincia) {
  renderPosts(currentCat, provincia);
}

function renderPosts(catId, filter='') {
  let posts = postsData[catId] || [];
  if (filter) posts = posts.filter(p => p.provincia === filter);
  DOM.paCount.textContent = posts.length + ' publicacion' + (posts.length !== 1 ? 'es' : '');

  const badgeClass = catId === 1 ? 'pc-badge-adopt' : catId === 2 ? 'pc-badge-lost' : 'pc-badge-support';
  const catIcon    = catId === 1 ? '🏠' : catId === 2 ? '🔍' : '❤️';

  DOM.postsGrid.innerHTML = posts.length ? posts.map(p => `
    <div class="post-card" data-id="${p.id}">
      <div class="pc-img-wrap">
        <img class="pc-img" src="${p.images[0]}" alt="${p.title}" loading="lazy" onerror="this.src='https://picsum.photos/seed/${p.id}/400/300'">
        <span class="pc-badge ${badgeClass}">${catIcon}${p.especie ? ' ' + p.especie : ''}</span>
      </div>
      <div class="pc-body">
        <div class="pc-loc">📍 ${p.ciudad}, ${p.provincia}</div>
        <h4>${p.title}</h4>
        <p>${p.desc}</p>
        <div class="pc-foot">
          <div class="pc-author">
            <img class="pc-author-av" src="${p.authorImg}" alt="${p.author}" loading="lazy" onerror="this.src='https://i.pravatar.cc/40?img=1'">
            <span>${p.author.substring(0,16)}${p.author.length>16?'…':''}</span>
          </div>
          <button class="btn-sm" data-id="${p.id}">Saber más</button>
        </div>
      </div>
    </div>
  `).join('') : '<div style="text-align:center;padding:60px 20px;color:var(--muted);"><div style="font-size:2.5rem;margin-bottom:12px">🐾</div><p>No hay publicaciones en esta zona todavía.</p></div>';
}

// ========== ENVIAR MENSAJE ==========
function sendMsg() {
  const input = document.getElementById('msgInput');
  if (!input.value.trim()) { showToast('Escribe un mensaje antes de enviar 💬'); return; }
  input.value = '';
  showToast('Mensaje enviado correctamente ✉️');
}

// ========== FUNCIONES COMPLETAS DEL CHAT ==========
function openFcChat(id) {
  const chat = CHATS.find(c => c.id === id);
  if (!chat) return;
  activeFcChat = chat;

  document.querySelectorAll('.fc-list .cp-item, #fcList .cp-item').forEach(i => i.classList.remove('selected'));
  const el = document.getElementById('fci-'+id);
  if (el) el.classList.add('selected');

  // Borrar el aviso (icono) de "no leído"
  const badge = document.getElementById('fci-badge-'+id);
  if (badge) badge.remove();
  chat.unread = 0;
  renderChatPanel();

  document.getElementById('fcActiveAv').textContent = chat.av;
  document.getElementById('fcActiveName').textContent = chat.name;
  document.getElementById('fcActiveStatus').textContent = chat.online ? 'En línea' : 'Desconectado';
  document.getElementById('fcOnlineDot').style.background = chat.online ? 'var(--green-l)' : 'var(--muted)';
  document.getElementById('fcInputWrap').style.display = 'flex';

  const msgs = document.getElementById('fcMessages');
  msgs.innerHTML = `<div class="sys-msg">🔒 Inicio de la conversación</div>` +
    chat.msgs.map(m => `
      <div class="bubble-wrap ${m.mine ? 'mine' : ''}">
        <div class="bubble ${m.mine ? 'mine' : 'theirs'}">${escHtml(m.text)}</div>
      </div>
    `).join('');
  msgs.scrollTop = msgs.scrollHeight;
}

function sendFcMsg() {
  if (!activeFcChat) return;
  const inp = document.getElementById('fcMsgInput');
  const txt = inp.value.trim();
  if (!txt) return;
  activeFcChat.msgs.push({ mine:true, text:txt });
  const msgs = document.getElementById('fcMessages');
  msgs.innerHTML += `<div class="bubble-wrap mine"><div class="bubble mine">${escHtml(txt)}</div></div>`;
  inp.value = '';
  inp.style.height = '';
  msgs.scrollTop = msgs.scrollHeight;
}

function autoResize(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 90) + 'px';
}

function escHtml(str) {
  return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ========== FUNCIONES DE LOGIN ==========
function doLogin() {
  closeLoginModal();
  document.body.style.overflow = '';
  document.getElementById('headerLoginBtn').classList.add('hidden');
  document.getElementById('userChip').classList.remove('hidden');
  showToast('¡Bienvenido de vuelta a PatitasUnidas! 🐾');
}

function doRegister() {
  closeLoginModal();
  document.body.style.overflow = '';
  document.getElementById('headerLoginBtn').classList.add('hidden');
  document.getElementById('userChip').classList.remove('hidden');
  showToast('¡Cuenta creada! Bienvenido a PatitasUnidas 🐾🎉');
}

// ========== DATOS FAQ / INFO ==========
const faqData = [
  { q:"¿Es gratuito registrarse en PatitasUnidas?", a:"Sí, el registro es completamente gratuito. Pedimos DNI/NIE y teléfono para garantizar la seguridad de todos los usuarios y evitar perfiles falsos o fraudes en adopciones." },
  { q:"¿Cómo puedo publicar un animal en adopción?", a:"Una vez registrado y verificado tu perfil, puedes ir a la sección 'Principal', elegir 'Adoptar mascota' y pulsar el botón 'Publicar'. Podrás añadir fotos, descripción, ubicación y datos del animal." },
  { q:"¿Qué hago si encuentro a mi mascota perdida?", a:"Actualiza tu publicación indicando que ha sido encontrada para que la comunidad sepa que ya está a salvo. También puedes contactar con los administradores para cerrar el caso." },
  { q:"¿Cómo sé si una protectora está verificada?", a:"Los perfiles de protectoras y organizaciones pasan por un proceso de verificación adicional. Verás una insignia de verificación en su perfil. Siempre recomendamos visitar las instalaciones antes de una adopción." },
  { q:"¿Puedo hacer donaciones a través de la plataforma?", a:"Actualmente estamos integrando un sistema de donaciones seguro mediante pasarela de pago. Muy pronto podrás donar directamente a protectoras y causas desde la plataforma." },
  { q:"¿Es posible reportar contenido inapropiado?", a:"Sí. Cada publicación, comentario y perfil tiene un botón de reporte. Nuestro equipo revisa todos los reportes en menos de 24 horas. El sistema de insignias y valoraciones también ayuda a identificar usuarios de confianza." },
];

function renderFaq() {
  const list = document.getElementById('faqList');
  list.innerHTML = faqData.map((f,i) => `
    <div class="faq-item" id="faq-${i}">
      <div class="faq-q" onclick="toggleFaq(${i})">
        <span>${f.q}</span>
        <i class="fa-solid fa-chevron-down faq-arrow"></i>
      </div>
      <div class="faq-a">${f.a}</div>
    </div>
  `).join('');
}

// ========== INIT ==========
DOM.init();
renderPosts(1);
renderFaq();
renderChatPanel();

document.addEventListener('click', e => {
  const card = e.target.closest('.post-card[data-id]');
  if (card) { openPostModal(+card.dataset.id); return; }
  const smBtn = e.target.closest('.btn-sm[data-id]');
  if (smBtn) { e.stopPropagation(); openPostModal(+smBtn.dataset.id); }
});

// Arreglo para el codigo de ayer (REVISAR)
const _postOverlay = document.getElementById('postOverlay');
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    closePostModal();
    closeFullChat();
    closeLoginModal();
    document.body.style.overflow = '';
  }
  if (_postOverlay.classList.contains('open')) {
    if (e.key === 'ArrowLeft')  galNav(-1);
    if (e.key === 'ArrowRight') galNav(1);
  }
});

// Buscar en el panel del chat
(function() {
  const searchEl = document.querySelector('.cp-search');
  if (!searchEl) return;
  let searchTimer;
  searchEl.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
      const q = searchEl.value.toLowerCase().trim();
      document.querySelectorAll('#cpList .cp-item').forEach(item => {
        const name = item.querySelector('.cp-name').textContent.toLowerCase();
        item.style.display = (!q || name.includes(q)) ? '' : 'none';
      });
    }, 150);
  });
})();