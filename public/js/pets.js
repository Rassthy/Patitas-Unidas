// PETS.JS — Gestión de mascotas

// Estado
let petImagesFiles = new DataTransfer();
let selectedVaccines = new Set();
let customVaccines = [];
let removeImageIds = [];
let currentPetId = null;
let currentPetOwnerId = null;
let petReminderPetId = null;

// Vacunas predefinidas
const PREDEFINED_VACCINES = [
  'Rabia',
  'Polivalente',
  'Leucemia felina',
  'Leishmaniosis',
  'Bordetella bronchiseptica',
];

// Utilidades
const csrf = () => document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function escPet(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

// ── Modal Add/Edit ──────────────────────────────────

function openAddPetModal() {
  if (!window.AUTH_USER_ID) { openLoginModal(); return; }
  currentPetId = null;
  petImagesFiles = new DataTransfer();
  selectedVaccines = new Set();
  customVaccines = [];
  removeImageIds = [];

  document.getElementById('petModalTitle').textContent = '🐾 Añadir mascota';
  document.getElementById('petForm').reset();
  document.getElementById('petImagesInput').value = '';
  document.getElementById('petImgPreviewList').innerHTML = '';
  document.getElementById('petExistingImages').innerHTML = '';
  document.getElementById('petExistingImagesWrap').style.display = 'none';
  document.getElementById('petReminderSection').style.display = 'none';
  renderVaccineTags();

  document.getElementById('petModalOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function openEditPetModal(petId) {
  currentPetId = petId;
  petImagesFiles = new DataTransfer();
  removeImageIds = [];
  selectedVaccines = new Set();
  customVaccines = [];

  document.getElementById('petModalTitle').textContent = '✏️ Editar mascota';
  document.getElementById('petReminderSection').style.display = 'flex';

  fetch(`/pets/${petId}`)
    .then(r => r.json())
    .then(({ pet }) => {
      document.getElementById('petNombre').value       = pet.nombre || '';
      document.getElementById('petEspecie').value      = pet.especie || '';
      document.getElementById('petRaza').value         = pet.raza || '';
      document.getElementById('petEdad').value         = pet.edad ?? '';
      document.getElementById('petDescripcion').value  = pet.descripcion || '';

      // Existing images
      const wrap = document.getElementById('petExistingImagesWrap');
      const cont = document.getElementById('petExistingImages');
      if (pet.images && pet.images.length) {
        wrap.style.display = '';
        cont.innerHTML = pet.images.map(img => `
          <div class="pet-edit-img-thumb" id="ei-${img.id}" style="position:relative;display:inline-block;margin:4px;">
            <img src="${escPet(img.url)}" style="width:72px;height:72px;object-fit:cover;border-radius:8px;border:1.5px solid var(--border);">
            <button type="button" onclick="removeExistingPetImage(${img.id})"
                    style="position:absolute;top:-6px;right:-6px;width:18px;height:18px;border-radius:50%;
                           background:var(--terra);border:none;color:#fff;cursor:pointer;font-size:10px;
                           display:flex;align-items:center;justify-content:center;line-height:1;">✕</button>
          </div>`).join('');
      } else {
        wrap.style.display = 'none';
      }

      // Vaccines
      if (pet.vaccines) {
        pet.vaccines.forEach(v => {
          if (PREDEFINED_VACCINES.includes(v.nombre_vacuna)) {
            selectedVaccines.add(v.nombre_vacuna);
          } else {
            customVaccines.push(v.nombre_vacuna);
          }
        });
      }

      document.getElementById('petImgPreviewList').innerHTML = '';
      renderVaccineTags();
      document.getElementById('petModalOverlay').classList.add('open');
      document.body.style.overflow = 'hidden';
    })
    .catch(() => { if (typeof showToast === 'function') showToast('Error al cargar la mascota'); });
}

function closePetModal(e) {
  if (e && e.target !== document.getElementById('petModalOverlay')) return;
  document.getElementById('petModalOverlay').classList.remove('open');
  document.body.style.overflow = '';
}

// Vacunas
function renderVaccineTags() {
  const container = document.getElementById('vaccineTagsContainer');
  if (!container) return;

  const predefined = PREDEFINED_VACCINES.map(name => {
    const active = selectedVaccines.has(name);
    return `
      <button type="button" class="pet-vaccine-tag ${active ? 'active' : ''}" onclick="toggleVaccine('${name}')">
        ${active ? '✓ ' : ''}${name}
      </button>`;
  }).join('');

  const custom = customVaccines.map((name, idx) => `
    <span class="pet-vaccine-tag active" style="gap:6px;display:inline-flex;align-items:center;">
      ✓ ${escPet(name)}
      <button type="button" onclick="removeCustomVaccine(${idx})"
              style="background:none;border:none;color:rgba(255,255,255,.7);cursor:pointer;padding:0;font-size:.9rem;line-height:1;">✕</button>
    </span>`).join('');

  container.innerHTML = predefined + custom + `
    <button type="button" class="pet-vaccine-tag" id="btnOtroVaccine" onclick="addCustomVaccineInput()"
            style="background:var(--cream-d);color:var(--muted);border-color:var(--border);">
      + Otro
    </button>`;
}

function toggleVaccine(name) {
  if (selectedVaccines.has(name)) {
    selectedVaccines.delete(name);
  } else {
    selectedVaccines.add(name);
  }
  renderVaccineTags();
}

function addCustomVaccineInput() {
  const existing = document.getElementById('customVaccineInputWrap');
  if (existing) { existing.querySelector('input').focus(); return; }

  const wrap = document.createElement('div');
  wrap.id = 'customVaccineInputWrap';
  wrap.style.cssText = 'display:flex;gap:6px;align-items:center;margin-top:8px;width:100%;';
  wrap.innerHTML = `
    <input type="text" id="customVaccineInput" placeholder="Nombre de la vacuna..."
           style="flex:1;padding:7px 10px;border:1.5px solid var(--border);border-radius:var(--r-s);
                  font-family:'DM Sans',sans-serif;font-size:.82rem;outline:none;" 
           onkeydown="if(event.key==='Enter'){event.preventDefault();confirmCustomVaccine();}">
    <button type="button" onclick="confirmCustomVaccine()"
            style="padding:6px 14px;background:var(--terra);color:#fff;border:none;border-radius:var(--r-s);
                   cursor:pointer;font-size:.8rem;font-weight:600;">Añadir</button>
    <button type="button" onclick="this.closest('#customVaccineInputWrap').remove()"
            style="padding:6px;background:none;border:1px solid var(--border);border-radius:var(--r-s);cursor:pointer;color:var(--muted);">✕</button>`;

  const container = document.getElementById('vaccineTagsContainer');
  container.parentNode.insertBefore(wrap, container.nextSibling);
  wrap.querySelector('input').focus();
}

function confirmCustomVaccine() {
  const input = document.getElementById('customVaccineInput');
  const val = input ? input.value.trim() : '';
  if (!val) return;
  if (PREDEFINED_VACCINES.includes(val)) {
    selectedVaccines.add(val);
  } else {
    if (!customVaccines.includes(val)) customVaccines.push(val);
  }
  document.getElementById('customVaccineInputWrap')?.remove();
  renderVaccineTags();
}

function removeCustomVaccine(idx) {
  customVaccines.splice(idx, 1);
  renderVaccineTags();
}

// Imagenes
function handlePetImages(input) {
  const MAX_SIZE = 2 * 1024 * 1024; // 2MB
  let descartados = 0;

  Array.from(input.files).forEach(file => {
    const yaExiste = Array.from(petImagesFiles.files).some(f => f.name === file.name);
    
    if (!yaExiste) {
      if (file.size > MAX_SIZE) {
        descartados++;
      } else {
        petImagesFiles.items.add(file);
      }
    }
  });

  if (descartados > 0) {
    if (typeof showToast === 'function') {
      showToast('⚠️ Imágenes de más de 2 MB descartadas', 'error');
    } else {
      alert('Atención: No se permiten imágenes de más de 2 MB.');
    }
  }

  // Límite de 5 fotos
  if (petImagesFiles.files.length > 5) {
    const dt = new DataTransfer();
    Array.from(petImagesFiles.files).slice(0, 5).forEach(f => dt.items.add(f));
    petImagesFiles = dt;
    if (typeof showToast === 'function') showToast('Máximo 5 fotos.', 'error');
  }

  input.files = petImagesFiles.files;
  renderPetImagePreviews();
}

function renderPetImagePreviews() {
  const list = document.getElementById('petImgPreviewList');
  if (!list) return;
  list.innerHTML = Array.from(petImagesFiles.files).map((file, idx) => {
    const url = URL.createObjectURL(file);
    return `
      <div style="position:relative;display:inline-block;margin:4px;">
        <img src="${url}" style="width:72px;height:72px;object-fit:cover;border-radius:8px;border:1.5px solid var(--border);">
        <button type="button" onclick="removeNewPetImage(${idx})"
                style="position:absolute;top:-6px;right:-6px;width:18px;height:18px;border-radius:50%;
                       background:var(--terra);border:none;color:#fff;cursor:pointer;font-size:10px;
                       display:flex;align-items:center;justify-content:center;">✕</button>
      </div>`;
  }).join('');
}

function removeNewPetImage(idx) {
  const dt = new DataTransfer();
  Array.from(petImagesFiles.files).filter((_, i) => i !== idx).forEach(f => dt.items.add(f));
  petImagesFiles = dt;
  document.getElementById('petImagesInput').files = petImagesFiles.files;
  renderPetImagePreviews();
}

function removeExistingPetImage(imageId) {
  if (!removeImageIds.includes(imageId)) removeImageIds.push(imageId);
  const el = document.getElementById(`ei-${imageId}`);
  if (el) el.style.opacity = '0.3';
}

async function submitPetForm(e) {
  e.preventDefault();
  const btn = document.getElementById('petSubmitBtn');
  if (btn) { btn.disabled = true; btn.textContent = 'Guardando...'; }

  const formData = new FormData();
  formData.append('nombre', document.getElementById('petNombre').value);
  formData.append('especie', document.getElementById('petEspecie').value);
  formData.append('raza', document.getElementById('petRaza').value);
  formData.append('edad', document.getElementById('petEdad').value);
  formData.append('descripcion', document.getElementById('petDescripcion').value);

  const allVaccines = [...selectedVaccines, ...customVaccines];
  allVaccines.forEach(v => formData.append('vaccines[]', v));

  const isEdit = !!currentPetId;

  if (isEdit) {
    removeImageIds.forEach(id => formData.append('remove_images[]', id));
    Array.from(petImagesFiles.files).forEach(f => formData.append('new_images[]', f));
  } else {
    Array.from(petImagesFiles.files).forEach(f => formData.append('images[]', f));
  }

  const url = isEdit ? `/pets/${currentPetId}` : '/pets';

  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 
        'X-CSRF-TOKEN': csrf(),
        'Accept': 'application/json'
      },
      body: formData,
    });

    if (res.ok) {
      if (typeof showToast === 'function') {
        showToast(isEdit ? '✅ Mascota actualizada' : '🐾 ¡Mascota añadida correctamente!');
      }
      document.getElementById('petModalOverlay').classList.remove('open');
      document.body.style.overflow = '';
      setTimeout(() => window.location.reload(), 700);
    } 
    // <--- ESTE ES EL "ELSE" QUE GESTIONA LOS ERRORES DEL SERVIDOR (Como el 422)
    else {
      const data = await res.json();
      
      // Buscamos el error específico de Laravel. Si hay errores de validación, 
      // mostramos el primero que encontremos (ej: "La imagen es demasiado grande")
      let errorMsg = 'Inténtalo de nuevo.';
      if (data.errors) {
        errorMsg = Object.values(data.errors).flat()[0];
      } else if (data.message) {
        errorMsg = data.message;
      }
      
      if (typeof showToast === 'function') {
        showToast('Error: ' + errorMsg, 'error');
      }
    }
  } catch (error) {
    if (typeof showToast === 'function') showToast('Error de conexión con el servidor', 'error');
    console.error("Error en submitPetForm:", error);
  } finally {
    if (btn) { btn.disabled = false; btn.textContent = isEdit ? 'Guardar cambios' : 'Añadir mascota'; }
  }
}

// MODAL DE ALERTAS
window.mostrarAvisoPatitas = function(title, message, onConfirm) {
    // Si hay un aviso antiguo atascado, lo borramos
    let existente = document.getElementById('avisoPatitas');
    if (existente) existente.remove();

    // Creamos el fondo oscuro (Overlay)
    const overlay = document.createElement('div');
    overlay.id = 'avisoPatitas';
    overlay.style.cssText = 'position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.8); z-index:2147483647; display:flex; align-items:center; justify-content:center; opacity:0; transition:opacity 0.25s ease; margin:0; padding:0;';

    // Creamos la tarjeta blanca (Modal)
    const caja = document.createElement('div');
    caja.style.cssText = 'background:#fff; width:90%; max-width:380px; padding:30px; border-radius:20px; text-align:center; transform:translateY(20px); transition:transform 0.25s ease; box-shadow:0 10px 40px rgba(0,0,0,0.6);';

    // Contenido visual
    caja.innerHTML = `
        <div style="font-size:3.5rem; margin-bottom:10px;">⚠️</div>
        <h3 style="font-family:'Fraunces',serif; font-size:1.4rem; margin-bottom:10px; color:#1a1a1a;">${title}</h3>
        <p style="color:#666; font-size:0.95rem; line-height:1.5; margin-bottom:25px;">${message}</p>
        <div style="display:flex; gap:10px; justify-content:center;">
            <button id="btnAvisoCancelar" style="padding:10px 20px; border-radius:10px; border:1px solid #ddd; background:#f9f9f9; color:#444; font-weight:bold; cursor:pointer; width:50%;">Cancelar</button>
            <button id="btnAvisoEliminar" style="padding:10px 20px; border-radius:10px; border:none; background:#c0392b; color:#fff; font-weight:bold; cursor:pointer; width:50%;">Eliminar</button>
        </div>
    `;

    // Lo inyectamos en la página
    overlay.appendChild(caja);
    document.body.appendChild(overlay);

    // Activamos la animación de entrada
    setTimeout(() => {
        overlay.style.opacity = '1';
        caja.style.transform = 'translateY(0)';
    }, 10);

    // Funciones de los botones
    const cerrar = () => {
        overlay.style.opacity = '0';
        caja.style.transform = 'translateY(20px)';
        setTimeout(() => overlay.remove(), 250);
    };

    document.getElementById('btnAvisoCancelar').onclick = cerrar;
    document.getElementById('btnAvisoEliminar').onclick = () => {
        if (onConfirm) onConfirm();
        cerrar();
    };
};

// ELIMINAR MASCOTA
async function deletePet(petId) {
    // 1. Ocultamos la tarjeta del animal que estuviera abierta
    if (typeof closePetDetailModal === 'function') {
        closePetDetailModal();
    }

    // 2. Lanzamos el aviso nuevo
    mostrarAvisoPatitas(
        '¿Eliminar mascota?', 
        'Esta acción borrará todos los datos de tu mascota de forma permanente.', 
        async () => {
            try {
                const res = await fetch(`/pets/${petId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf() },
                });
                
                if (res.ok) {
                    if (typeof showToast === 'function') showToast('Mascota eliminada correctamente');
                    setTimeout(() => window.location.reload(), 600);
                }
            } catch (e) {
                if (typeof showToast === 'function') showToast('Error al conectar con el servidor', 'error');
            }
        }
    );
}

// Detalles de la mascota
let petDetailGalIdx = 0;
let petDetailImages = [];

function openPetDetailModal(petId) {
  petDetailGalIdx = 0;
  fetch(`/pets/${petId}`)
    .then(r => r.json())
    .then(({ pet }) => {
      currentPetId = pet.id;
      currentPetOwnerId = pet.user_id;
      petDetailImages = pet.images || [];
      renderPetDetailModal(pet);
      document.getElementById('petDetailOverlay').classList.add('open');
      document.body.style.overflow = 'hidden';
    })
    .catch(() => { if (typeof showToast === 'function') showToast('Error al cargar la mascota'); });
}

function renderPetDetailModal(pet) {
  const isOwner = window.AUTH_USER_ID && pet.user_id === window.AUTH_USER_ID;

  // ── GALERÍA ─────────────────────────────────────────
  const galImg = document.getElementById('petDetailGalImg');
  const galDots = document.getElementById('petDetailGalDots');
  const galArrows = document.querySelectorAll('.pet-detail-gal-arr');

  if (petDetailImages.length) {
    galImg.src = petDetailImages[0].url;
    galImg.style.display = '';
    
    // INTEGRACIÓN CON LIGHTBOX (Reutilizando tu lógica de posts/chat)
    galImg.style.cursor = 'zoom-in';
    galImg.onclick = () => {
      if (!petDetailImages || !petDetailImages.length) return;
      // Las URLs de mascotas ya vienen completas desde el controlador
      const urls = petDetailImages.map(img => img.url);
      if (typeof openLightbox === 'function') {
        openLightbox(urls, petDetailGalIdx);
      }
    };

    galDots.innerHTML = petDetailImages.map((_, i) =>
      `<button class="gal-dot ${i === 0 ? 'act' : ''}" onclick="petDetailNav(${i})"></button>`
    ).join('');
    galArrows.forEach(a => a.style.display = petDetailImages.length > 1 ? '' : 'none');
  } else {
    galImg.src = '/img/defaults/foto_perfil_generica.png';
    galImg.style.display = '';
    galImg.style.cursor = 'default';
    galImg.onclick = null;
    galDots.innerHTML = '';
    galArrows.forEach(a => a.style.display = 'none');
  }

  // ── INFORMACIÓN BÁSICA ──────────────────────────────
  document.getElementById('petDetailNombre').textContent = pet.nombre || '—';
  document.getElementById('petDetailMeta').innerHTML = [
    pet.especie ? `<span>🐾 ${escPet(pet.especie)}</span>` : '',
    pet.raza    ? `<span>🏷️ ${escPet(pet.raza)}</span>` : '',
    pet.edad    ? `<span>📅 ${pet.edad} año${pet.edad !== 1 ? 's' : ''}</span>` : '',
  ].filter(Boolean).join('');

  document.getElementById('petDetailDesc').textContent = pet.descripcion || 'Sin descripción.';

  // ── ACCIONES DEL DUEÑO ──────────────────────────────
  const ownerActions = document.getElementById('petDetailOwnerActions');
  if (isOwner) {
    ownerActions.style.display = 'flex';
    ownerActions.innerHTML = `
      <button class="btn-s" style="font-size:.8rem;padding:7px 14px;" onclick="closePetDetailModal();openEditPetModal(${pet.id})">
        <i class="fa-solid fa-pen"></i> Editar
      </button>
      <button class="btn-s" style="font-size:.8rem;padding:7px 14px;color:var(--terra);border-color:var(--terra);"
              onclick="closePetDetailModal();openAddReminderModal(${pet.id})">
        <i class="fa-solid fa-bell"></i> Añadir recordatorio
      </button>
      <button class="btn-s" style="font-size:.8rem;padding:7px 14px;color:#c0392b;border-color:#f5c6a8;"
              onclick="closePetDetailModal();deletePet(${pet.id})">
        <i class="fa-solid fa-trash"></i> Eliminar
      </button>`;
  } else {
    ownerActions.style.display = 'none';
  }

  // ── VACUNAS (Solo dueño) ───────────────────────────
  const vaccineSection = document.getElementById('petDetailVaccineSection');
  if (isOwner && pet.vaccines) {
    vaccineSection.style.display = '';
    const vList = document.getElementById('petDetailVaccineList');
    if (pet.vaccines.length) {
      vList.innerHTML = pet.vaccines.map(v => `
        <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;
                     background:#EBF5E2;color:var(--green);border-radius:50px;
                     font-size:.75rem;font-weight:600;border:1px solid #c3e6a8;">
          <i class="fa-solid fa-syringe"></i> ${escPet(v.nombre_vacuna)}
          ${v.fecha_administracion ? `<span style="opacity:.7;">· ${v.fecha_administracion}</span>` : ''}
        </span>`).join('');
    } else {
      vList.innerHTML = '<span style="color:var(--muted);font-size:.85rem;">Sin vacunas registradas.</span>';
    }
  } else {
    vaccineSection.style.display = 'none';
  }

  // ── RECORDATORIOS (Solo dueño) ──────────────────────
  const reminderSection = document.getElementById('petDetailReminderSection');
  if (isOwner && pet.reminders) {
    reminderSection.style.display = '';
    const rList = document.getElementById('petDetailReminderList');
    if (pet.reminders.length) {
      rList.innerHTML = pet.reminders.map(r => `
        <div style="display:flex;align-items:flex-start;gap:12px;padding:10px 14px;
                    background:var(--soft);border-radius:var(--r-s);margin-bottom:8px;">
          <span style="font-size:1.2rem;flex-shrink:0;">🔔</span>
          <div style="flex:1;min-width:0;">
            <div style="font-weight:600;font-size:.85rem;color:var(--dark);">${escPet(r.titulo)}</div>
            ${r.mensaje ? `<div style="font-size:.8rem;color:var(--muted);margin-top:2px;">${escPet(r.mensaje)}</div>` : ''}
            <div style="font-size:.75rem;color:var(--terra);margin-top:4px;font-weight:600;">📅 ${r.fecha_alarma}</div>
          </div>
          <button onclick="deleteReminder(${pet.id}, ${r.id})"
                  style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:.9rem;flex-shrink:0;">🗑️</button>
        </div>`).join('');
    } else {
      rList.innerHTML = '<span style="color:var(--muted);font-size:.85rem;">Sin recordatorios.</span>';
    }
  } else {
    reminderSection.style.display = 'none';
  }
}

function petDetailNav(idx) {
  if (!petDetailImages.length) return;
  petDetailGalIdx = idx;
  document.getElementById('petDetailGalImg').src = petDetailImages[idx].url;
  document.querySelectorAll('#petDetailGalDots .gal-dot').forEach((d, i) =>
    d.classList.toggle('act', i === idx));
}

function petDetailNavDir(dir) {
  if (!petDetailImages.length) return;
  petDetailGalIdx = (petDetailGalIdx + dir + petDetailImages.length) % petDetailImages.length;
  petDetailNav(petDetailGalIdx);
}

function closePetDetailModal(e) {
  if (e && e.target !== document.getElementById('petDetailOverlay')) return;
  document.getElementById('petDetailOverlay').classList.remove('open');
  document.body.style.overflow = '';
}

// Recordatorios
function openAddReminderModal(petId) {
  petReminderPetId = petId;
  document.getElementById('reminderForm').reset();
  document.getElementById('reminderModalOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeReminderModal(e) {
  if (e && e.target !== document.getElementById('reminderModalOverlay')) return;
  document.getElementById('reminderModalOverlay').classList.remove('open');
  document.body.style.overflow = '';
}

async function submitReminderForm(e) {
  e.preventDefault();
  if (!petReminderPetId) return;

  const btn = document.getElementById('reminderSubmitBtn');
  if (btn) { btn.disabled = true; btn.textContent = 'Guardando...'; }

  const titulo       = document.getElementById('reminderTitulo').value;
  const mensaje      = document.getElementById('reminderMensaje').value;
  const fecha_alarma = document.getElementById('reminderFecha').value;

  try {
    const res = await fetch(`/pets/${petReminderPetId}/reminders`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrf(),
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ titulo, mensaje, fecha_alarma }),
    });

    const data = await res.json();
    if (res.ok) {
      if (typeof showToast === 'function') showToast('🔔 Recordatorio añadido correctamente');
      document.getElementById('reminderModalOverlay').classList.remove('open');
      document.body.style.overflow = '';
    } else {
      const firstErr = data.errors ? Object.values(data.errors)[0]?.[0] : data.message;
      if (typeof showToast === 'function') showToast('Error: ' + (firstErr || 'Inténtalo de nuevo.'));
    }
  } catch {
    if (typeof showToast === 'function') showToast('Error de conexión');
  } finally {
    if (btn) { btn.disabled = false; btn.textContent = 'Guardar recordatorio'; }
  }
}

// ELIMINAR RECORDATORIO
async function deleteReminder(petId, reminderId) {
  // Usamos el modal personalizado en lugar del confirm nativo
  mostrarAvisoPatitas(
    '¿Eliminar recordatorio?', 
    'Este aviso dejará de sonar y se borrará de la lista permanentemente.', 
    async () => {
      try {
        const res = await fetch(`/pets/${petId}/reminders/${reminderId}`, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': csrf() }, //
        });

        if (res.ok) {
          if (typeof showToast === 'function') showToast('Recordatorio eliminado 🗑️'); //
          
          // Si estamos viendo el detalle de la mascota, refrescamos la lista sin recargar toda la página
          if (currentPetId) openPetDetailModal(currentPetId); 
        }
      } catch (error) {
        if (typeof showToast === 'function') showToast('Error al eliminar'); //
        console.error("Error al borrar recordatorio:", error);
      }
    }
  );
}

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    closePetDetailModal();
    closePetModal();
    closeReminderModal();
  }
});