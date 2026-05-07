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
      <div class="modal-actions" style="display:flex;gap:12px;align-items:center;">
        <button class="btn-outline" id="likeBtn" onclick="toggleLike(currentPost.id)">
          <i class="fa-regular fa-heart"></i> {{ __('Like') }}
        </button>
        <button class="btn-outline" id="reportPostBtn" style="color:#e74c3c;border-color:#e74c3c;">
          <i class="fa-solid fa-flag"></i> {{ __('Reportar') }}
        </button>
      </div>
      <div class="modal-author" id="modalAuthor"></div>
      <div class="comments-section">
        <div class="modal-sec-title">
          <i class="fa-solid fa-comments" style="color:var(--terra)"></i> {{ __('Comentarios') }}
        </div>
        <div id="modalComments"></div>
        <div class="msg-section mt16">
          <div class="modal-sec-title">
            <i class="fa-solid fa-paper-plane" style="color:var(--terra)"></i> {{ __('Enviar un mensaje') }}
          </div>
          <div class="msg-form">
            <input class="msg-input" type="text"
                   placeholder="{{ __('Escribe tu mensaje sobre esta publicación...') }}"
                   id="msgInput">
            <button class="msg-send" onclick="sendMsg()">
              <i class="fa-solid fa-paper-plane"></i> {{ __('Enviar') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- NUEVA PUBLICACIÓN MODAL -->
<div class="overlay" id="newPostOverlay" onclick="closeNewPostModal(event)">
  <div class="modal" id="newPostModal">
    <div class="modal-header" style="text-align:center;">
      <h2 style="margin:0 auto;flex-grow:1;text-align:center;">{{ __('Crear nueva publicación') }}</h2>
      <button class="modal-close-btn" onclick="closeNewPostModal()"
              style="position:absolute;right:16px;top:16px;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="newPostForm" enctype="multipart/form-data">
      <div class="modal-body npp-body">

        <div class="form-group">
          <label for="postCategory">{{ __('Categoría *') }}</label>
          <select id="postCategory" name="category_id" required>
            <option value="">{{ __('Selecciona una categoría') }}</option>
            <option value="1">{{ __('🏠 Adoptar mascota') }}</option>
            <option value="2">{{ __('🔍 Mascota perdida o robada') }}</option>
            <option value="3">{{ __('❤️ Apoyar animales') }}</option>
          </select>
        </div>

        <div class="form-group">
          <label for="postTitle">{{ __('Título *') }}</label>
          <input type="text" id="postTitle" name="titulo"
                 placeholder="{{ __('Ej: Se busca perro dorado') }}"
                 required maxlength="200">
        </div>

        <div class="form-group">
          <label for="postDesc">{{ __('Descripción *') }}</label>
          <textarea id="postDesc" name="descripcion"
                    placeholder="{{ __('Cuéntanos más detalles sobre el animal o la situación...') }}"
                    rows="4" required></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="provincia">{{ __('Provincia *') }}</label>
            <input type="text" id="provincia" name="provincia"
                   placeholder="{{ __('Ej: Madrid') }}"
                   required maxlength="50">
          </div>
          <div class="form-group">
            <label for="ciudad">{{ __('Ciudad *') }}</label>
            <input type="text" id="ciudad" name="ciudad"
                   placeholder="{{ __('Ej: Madrid') }}"
                   required maxlength="100">
          </div>
        </div>

        <div class="form-divider">{{ __('Información del animal (opcional)') }}</div>

        <div class="form-row">
          <div class="form-group">
            <label for="animalNombre">{{ __('Nombre') }}</label>
            <input type="text" id="animalNombre" name="animal_nombre"
                   placeholder="{{ __('Ej: Firulais') }}" maxlength="100">
          </div>
          <div class="form-group">
            <label for="animalEspecie">{{ __('Especie') }}</label>
            <input type="text" id="animalEspecie" name="animal_especie"
                   placeholder="{{ __('Ej: Perro') }}" maxlength="50">
          </div>
          <div class="form-group">
            <label for="animalRaza">{{ __('Raza') }}</label>
            <input type="text" id="animalRaza" name="animal_raza"
                   placeholder="{{ __('Ej: Golden Retriever') }}" maxlength="50">
          </div>
        </div>

        <div class="form-group">
          <label for="postImages">{{ __('Imágenes') }}</label>
          <div class="file-input-wrapper">
            <input type="file" id="postImages" name="images[]"
                   multiple accept="image/*" onchange="previewImages(this)">
            <span class="file-label">{{ __('📁 Selecciona hasta 10 imágenes (máx. 2MB cada una)') }}</span>
          </div>
          <div id="imagePreviewContainer" style="margin-top:12px;display:none;">
            <div style="font-size:0.85rem;font-weight:600;margin-bottom:8px;color:var(--text-secondary);">
              {{ __('Archivos seleccionados:') }}
            </div>
            <div id="imagePreviewList" style="display:flex;flex-wrap:wrap;gap:8px;"></div>
          </div>
        </div>

        <input type="hidden" name="estado" value="activa">
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-outline" onclick="closeNewPostModal()">
          {{ __('Cancelar') }}
        </button>
        <button type="submit" class="btn-p">{{ __('Publicar') }}</button>
      </div>
    </form>
  </div>
</div>

<!-- CHAT COMPLETO (SUPERPUESTO/EMERGENTE) -->
<div class="overlay" id="chatOverlayEl" onclick="closeFullChat(event)"></div>

<!-- CHAT COMPLETO MODAL -->
<div class="full-chat" id="fullChatModal" style="position:fixed;">

  <!-- SIDEBAR: Lista de chats -->
  <div class="fc-sidebar">
    <div class="fc-sb-head">
      <h2>{{ __('Mensajería') }}</h2>
      <input class="cp-search" type="text" placeholder="{{ __('🔍  Buscar conversación...') }}">
    </div>
    <div class="fc-list" id="fcList"></div>
    <div class="fc-sb-foot">
      <button class="btn-new-chat" onclick="openNewMessageModal()">
        <i class="fa-solid fa-plus"></i> {{ __('Nuevo mensaje') }}
      </button>
    </div>
  </div>

  <!-- MODAL NUEVO MENSAJE -->
  <div id="newMsgOverlay" onclick="if(event.target===this)closeNewMessageModal()"
       style="display:flex;opacity:0;pointer-events:none;position:fixed;inset:0;z-index:10000;
              background:rgba(0,0,0,0.5);align-items:center;justify-content:center;transition:opacity 0.2s;">
    <div onclick="event.stopPropagation()"
         style="background:var(--cream);border-radius:14px;padding:24px;width:min(90vw,400px);
                box-shadow:var(--sh-l);">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <h3 style="margin:0;font-size:1rem;">{{ __('✉️ Nuevo mensaje') }}</h3>
        <button onclick="closeNewMessageModal()"
                style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:var(--muted);">✕</button>
      </div>
      <input id="newMsgUserInput" type="text"
             placeholder="{{ __('Buscar usuario por nombre...') }}"
             oninput="searchUsersForChat(this.value)"
             style="width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:8px;
                    font-size:0.85rem;background:var(--soft);color:var(--txt);outline:none;
                    box-sizing:border-box;">
      <div id="newMsgUserResults" style="margin-top:8px;max-height:200px;overflow-y:auto;"></div>
    </div>
  </div>

  <!-- Main: panel de mensajes -->
  <div class="fc-main">
    <div class="fc-main-head" id="fcMainHead">
      <div class="fc-main-head-info">
        <div class="fc-active-av" id="fcActiveAv">💬</div>
        <div>
          <div style="font-size:.88rem;font-weight:700;" id="fcActiveName">
            {{ __('Selecciona una conversación') }}
          </div>
          <div style="display:flex;align-items:center;gap:5px;font-size:.72rem;color:var(--muted);">
            <span class="online-dot" id="fcOnlineDot" style="background:var(--muted)"></span>
            <span id="fcActiveStatus">—</span>
          </div>
        </div>
      </div>
      <div style="display:flex;gap:8px;align-items:center;">
        <!-- Botón opciones con dropdown -->
        <div style="position:relative;">
          <button class="hdr-icon-btn" style="border-color:var(--border);"
                  onclick="toggleChatOptions(event)">
            <i class="fa-solid fa-ellipsis"></i>
          </button>
          <div id="chatOptionsDropdown"
               style="display:flex;flex-direction:column;position:absolute;top:calc(100% + 8px);right:0;
                      background:var(--cream);border:1px solid var(--border);border-radius:10px;
                      box-shadow:var(--sh-m);z-index:100;min-width:180px;overflow:hidden;
                      opacity:0;pointer-events:none;transition:opacity 0.2s ease;">
            <button onclick="viewChatUserProfile()"
                    style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:none;
                           border:none;cursor:pointer;font-size:0.85rem;color:var(--txt);
                           text-align:left;width:100%;">
              <i class="fa-solid fa-user" style="color:var(--terra);width:16px;"></i>
              {{ __('Ver perfil') }}
            </button>
            <div style="height:1px;background:var(--border);margin:0 10px;"></div>
            <button onclick="openReportModal('mensaje_chat', activeChatId, activeChatUserId)"
                    style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:none;
                           border:none;cursor:pointer;font-size:0.85rem;color:#e74c3c;
                           text-align:left;width:100%;">
              <i class="fa-solid fa-flag" style="width:16px;"></i>
              {{ __('Reportar usuario') }}
            </button>
          </div>
        </div>
        <!-- Botón cerrar -->
        <button class="hdr-icon-btn" style="border-color:var(--border);" onclick="closeFullChat()">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    </div>

    <div class="fc-messages" id="fcMessages">
      <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;
                  height:100%;color:var(--muted);gap:12px;">
        <div style="font-size:2.5rem;">💬</div>
        <p style="font-size:.85rem;">{{ __('Selecciona una conversación para empezar') }}</p>
      </div>
    </div>

    <div class="fc-input-wrap" id="fcInputWrap" style="display:none;flex-direction:column;gap:6px;">
      <!-- Preview del archivo seleccionado -->
      <div id="fcFilePreview"
           style="display:none;padding:6px 12px;background:var(--soft);border-radius:8px;
                  font-size:0.8rem;align-items:center;gap:8px;">
        <i class="fa-solid fa-paperclip" style="color:var(--terra);"></i>
        <span id="fcFilePreviewName" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
        <button onclick="clearFcFile()"
                style="background:none;border:none;cursor:pointer;color:var(--muted);">✕</button>
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
        <textarea class="fc-textarea" id="fcMsgInput" rows="1"
                  placeholder="{{ __('Escribe un mensaje...') }}"
                  onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendFcMsg()}"
                  oninput="autoResize(this)"></textarea>
        <button class="fc-send" onclick="sendFcMsg()">
          <i class="fa-solid fa-paper-plane"></i>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- DROPDOWN NOTIFICACIONES -->
<div id="notifDropdown"
     style="display:flex;opacity:0;pointer-events:none;position:fixed;top:60px;right:16px;
            width:360px;max-height:480px;background:var(--cream);border:1px solid var(--border);
            border-radius:14px;box-shadow:var(--sh-l);z-index:1000;overflow:hidden;flex-direction:column;">
  <div style="padding:14px 16px;border-bottom:1px solid var(--border);
              display:flex;align-items:center;justify-content:space-between;">
    <span style="font-weight:700;font-size:0.95rem;">{{ __('🔔 Notificaciones') }}</span>
    <button onclick="markAllNotificationsRead()"
            style="background:none;border:none;cursor:pointer;color:var(--terra);font-size:0.75rem;">
      {{ __('Marcar todas como leídas') }}
    </button>
  </div>
  <div id="notifList" style="overflow-y:auto;max-height:400px;">
    <div style="text-align:center;padding:40px 20px;color:var(--muted);font-size:0.85rem;">
      {{ __('Cargando notificaciones...') }}
    </div>
  </div>
</div>

<!-- LIGHTBOX -->
<div id="lightboxOverlay" onclick="if(event.target===this)closeLightbox()"
     style="display:flex;pointer-events:none;position:fixed;inset:0;z-index:9999;
            background:rgba(0,0,0,0.92);align-items:center;justify-content:center;">
  <button onclick="closeLightbox()"
          style="position:absolute;top:16px;right:20px;background:none;border:none;
                 color:#fff;font-size:1.8rem;cursor:pointer;">✕</button>
  <button class="lb-arr" onclick="lightboxNav(-1)"
          style="position:absolute;left:16px;background:rgba(255,255,255,0.15);border:none;
                 color:#fff;font-size:2rem;padding:10px 16px;border-radius:50%;cursor:pointer;">‹</button>
  <img id="lightboxImg" src=""
       style="max-width:90vw;max-height:90vh;object-fit:contain;border-radius:8px;">
  <button class="lb-arr" onclick="lightboxNav(1)"
          style="position:absolute;right:16px;background:rgba(255,255,255,0.15);border:none;
                 color:#fff;font-size:2rem;padding:10px 16px;border-radius:50%;cursor:pointer;">›</button>
  <div id="lightboxCounter"
       style="position:absolute;bottom:20px;color:#fff;font-size:0.9rem;opacity:0.7;"></div>
</div>

<!-- MODAL TÉRMINOS DE USO -->
<div id="termsModal"
     onclick="if(event.target===this)closeTermsModal()"
     style="display:flex;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,0.6);
            align-items:center;justify-content:center;padding:20px;">
  <div onclick="event.stopPropagation()"
       style="background:var(--cream);border-radius:16px;width:min(90vw,720px);
              max-height:85vh;display:flex;flex-direction:column;box-shadow:var(--sh-l);">

    <!-- Cabecera -->
    <div style="padding:20px 28px;border-bottom:1px solid var(--border);
                display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
      <h3 style="margin:0;font-size:1.1rem;">🐾 {{ __('Términos de uso') }}</h3>
      <button onclick="closeTermsModal()"
              style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:var(--muted);">✕</button>
    </div>

    <!-- Contenido en dos columnas -->
    <div style="padding:28px;overflow-y:auto;font-size:0.875rem;color:var(--txt);line-height:1.7;
                display:grid;grid-template-columns:1fr 1fr;gap:20px 32px;">

      <div>
        <strong>1. {{ __('Aceptación de los términos') }}</strong>
        <p style="margin:6px 0 0;">{{ __('Al registrarte en PatitasUnidas aceptas estos términos en su totalidad. Si no estás de acuerdo con alguna parte, te pedimos que no utilices la plataforma.') }}</p>
      </div>

      <div>
        <strong>2. {{ __('Uso de la plataforma') }}</strong>
        <p style="margin:6px 0 0;">{{ __('PatitasUnidas es una plataforma de ayuda animal. Queda prohibido publicar contenido falso, engañoso o con fines lucrativos no autorizados. El incumplimiento puede resultar en la suspensión de la cuenta.') }}</p>
      </div>

      <div>
        <strong>3. {{ __('Registro y verificación') }}</strong>
        <p style="margin:6px 0 0;">{{ __('Los datos proporcionados durante el registro deben ser verídicos. PatitasUnidas se reserva el derecho de verificar la identidad de los usuarios para garantizar la seguridad de los animales.') }}</p>
      </div>

      <div>
        <strong>4. {{ __('Protección de datos') }}</strong>
        <p style="margin:6px 0 0;">{{ __('Tus datos personales serán tratados conforme al Reglamento General de Protección de Datos (RGPD). No compartiremos tu información con terceros sin tu consentimiento expreso.') }}</p>
      </div>

      <div>
        <strong>5. {{ __('Publicaciones y contenido') }}</strong>
        <p style="margin:6px 0 0;">{{ __('Eres responsable del contenido que publicas. PatitasUnidas puede eliminar cualquier publicación que vulnere estas normas o ponga en riesgo el bienestar animal.') }}</p>
      </div>

      <div>
        <strong>6. {{ __('Adopciones') }}</strong>
        <p style="margin:6px 0 0;">{{ __('PatitasUnidas actúa como intermediario y no se hace responsable del resultado final de los procesos de adopción. Recomendamos seguir siempre los protocolos establecidos por las protectoras.') }}</p>
      </div>

      <div>
        <strong>7. {{ __('Modificaciones') }}</strong>
        <p style="margin:6px 0 0;">{{ __('PatitasUnidas se reserva el derecho de modificar estos términos en cualquier momento. Los usuarios serán notificados de cambios significativos a través de la plataforma.') }}</p>
      </div>

      <div>
        <strong>8. {{ __('Contacto') }}</strong>
        <p style="margin:6px 0 0;">{{ __('Para cualquier consulta relacionada con estos términos puedes contactarnos en') }}
          <a href="mailto:noreply.patitasunidas@gmail.com" style="color:var(--terra);">noreply.patitasunidas@gmail.com</a>.
        </p>
      </div>

    </div>

    <!-- Footer -->
    <div style="padding:16px 28px;border-top:1px solid var(--border);flex-shrink:0;text-align:right;">
      <button onclick="closeTermsModal()" class="btn-p">
        {{ __('Entendido') }}
      </button>
    </div>

  </div>
</div>

<!-- MODAL REPORTE -->
<div id="reportOverlay" onclick="if(event.target===this)closeReportModal()"
     style="display:flex;opacity:0;pointer-events:none;position:fixed;inset:0;z-index:10000;
            background:rgba(0,0,0,0.5);align-items:center;justify-content:center;transition:opacity 0.2s;">
  <div onclick="event.stopPropagation()"
       style="background:var(--cream);border-radius:14px;padding:24px;width:min(90vw,420px);
              box-shadow:var(--sh-l);position:relative;z-index:10001;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
      <h3 style="margin:0;font-size:1rem;">{{ __('🚨 Reportar contenido') }}</h3>
      <button onclick="closeReportModal()"
              style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:var(--muted);">✕</button>
    </div>
    <p style="font-size:0.85rem;color:var(--muted);margin-bottom:16px;">
      {{ __('Describe el motivo del reporte. Nuestro equipo lo revisará en menos de 24h.') }}
    </p>
    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px;">
      <label style="font-size:0.85rem;font-weight:600;">{{ __('Motivo *') }}</label>
      <textarea id="reportMotivo" rows="4"
                placeholder="{{ __('Explica por qué estás reportando este contenido...') }}"
                style="padding:10px;border:1px solid var(--border);border-radius:8px;
                       background:var(--cream);font-size:0.85rem;color:var(--txt);
                       resize:none;font-family:inherit;outline:none;"></textarea>
    </div>
    <div style="display:flex;gap:8px;justify-content:flex-end;">
      <button onclick="closeReportModal()"
              style="padding:8px 16px;border:1px solid var(--border);border-radius:8px;
                     background:none;cursor:pointer;font-size:0.85rem;color:var(--muted);">
        {{ __('Cancelar') }}
      </button>
      <button onclick="submitReport()"
              style="padding:8px 16px;border:none;border-radius:8px;background:var(--terra);
                     color:#fff;cursor:pointer;font-size:0.85rem;font-weight:600;transition:background 0.2s;">
        {{ __('Enviar reporte') }}
      </button>
    </div>
  </div>
</div>