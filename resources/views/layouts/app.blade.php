<!DOCTYPE html>
<html lang="es">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PatitasUnidas</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
  <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sections.css') }}">
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
  <link rel="stylesheet" href="{{ asset('css/donations.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pets.css') }}">
</head>

<body>

@include('layouts.sidebar')
@include('layouts.header')
@include('components.chat-panel')

@if (session('success') || session('error'))
  <div class="flash-messages">
    @if (session('success'))
      <div class="flash flash-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="flash flash-error">{{ session('error') }}</div>
    @endif
  </div>
@endif

<main id="mainContent">
    @yield('content')
</main>

@include('components.modals')
@include('components.login-modal')
@include('components.toast')

<div class="pet-overlay" id="confirmModalOverlay" style="z-index: 9999;">
  <div class="pet-modal" style="max-width: 400px; text-align: center; padding: 30px;">
    <div style="font-size: 3rem; margin-bottom: 15px;">⚠️</div>
    <h3 id="confirmModalTitle" style="font-family: 'Fraunces', serif; margin-bottom: 10px;">¿Estás seguro?</h3>
    <p id="confirmModalMsg" style="color: var(--muted); font-size: 0.9rem; line-height: 1.5; margin-bottom: 25px;"></p>
    <div style="display: flex; gap: 10px; justify-content: center;">
      <button class="btn-s" onclick="closeConfirmModal()" style="padding: 10px 20px;">Cancelar</button>
      <button id="confirmModalBtn" class="btn-p" style="padding: 10px 20px; background: #c0392b; border-color: #c0392b;">Eliminar</button>
    </div>
  </div>
</div>

<!-- Auth user para JS -->
<script>
  window.AUTH_USER_ID = {{ Auth::check() ? Auth::id() : 'null' }};

<!-- Traducciones para JS -->

  @php
    $i18n = [
      'Error al cargar publicaciones'                => __('Error al cargar publicaciones'),
      'publicacion'                                  => __('publicacion'),
      'publicaciones'                                => __('publicaciones'),
      'No hay publicaciones en esta zona todavía.'   => __('No hay publicaciones en esta zona todavía.'),
      'Saber más'                                    => __('Saber más'),
      'Escribe un comentario antes de enviar 💬'     => __('Escribe un comentario antes de enviar 💬'),
      'Comentario enviado ✉️'                        => __('Comentario enviado ✉️'),
      'Error al enviar comentario'                   => __('Error al enviar comentario'),
      'Error al cargar la conversación 🐾'           => __('Error al cargar la conversación 🐾'),
      'Sé el primero en escribir 🐾'                 => __('Sé el primero en escribir 🐾'),
      '🔒 Inicio de la conversación'                 => __('🔒 Inicio de la conversación'),
      'Error al enviar mensaje'                      => __('Error al enviar mensaje'),
      'No puedes chatear contigo mismo 😄'           => __('No puedes chatear contigo mismo 😄'),
      'Error al iniciar chat'                        => __('Error al iniciar chat'),
      '¡Bienvenido de vuelta a PatitasUnidas! 🐾'   => __('¡Bienvenido de vuelta a PatitasUnidas! 🐾'),
      '¡Cuenta creada! Bienvenido a PatitasUnidas 🐾🎉' => __('¡Cuenta creada! Bienvenido a PatitasUnidas 🐾🎉'),
      'Máximo 10 imágenes permitidas'                => __('Máximo 10 imágenes permitidas'),
      'Publicación creada correctamente! 🎉'         => __('Publicación creada correctamente! 🎉'),
      'Error al crear publicación. Inténtalo de nuevo.' => __('Error al crear publicación. Inténtalo de nuevo.'),
      'Error de conexión. Verifica tu conexión a internet.' => __('Error de conexión. Verifica tu conexión a internet.'),
      '📋 Todas las publicaciones'                   => __('📋 Todas las publicaciones'),
      '🏠 Adoptar mascota'                           => __('🏠 Adoptar mascota'),
      '🔍 Mascota perdida o robada'                  => __('🔍 Mascota perdida o robada'),
      '❤️ Apoyar animales'                           => __('❤️ Apoyar animales'),
      'Error al cargar la publicación'               => __('Error al cargar la publicación'),
      '🏥 Protectora verificada'                     => __('🏥 Protectora verificada'),
      '🌟 Organización'                              => __('🌟 Organización'),
      '👤 Usuario'                                   => __('👤 Usuario'),
      'Nombre'                                       => __('Nombre'),
      'Especie'                                      => __('Especie'),
      'Raza'                                         => __('Raza'),
      'Mensaje'                                      => __('Mensaje'),
      'Perfil'                                       => __('Perfil'),
      'No tienes conversaciones aún 🐾'              => __('No tienes conversaciones aún 🐾'),
      'Sin mensajes aún'                             => __('Sin mensajes aún'),
      'No se encontraron usuarios'                   => __('No se encontraron usuarios'),
      'No se pudo encontrar el nombre de usuario para este perfil 🐾' => __('No se pudo encontrar el nombre de usuario para este perfil 🐾'),
      'Sé el primero en comentar 🐾'                 => __('Sé el primero en comentar 🐾'),
      'Inicia sesión para responder 🐾'              => __('Inicia sesión para responder 🐾'),
      'Escribe tu respuesta...'                      => __('Escribe tu respuesta...'),
      'Enviar'                                       => __('Enviar'),
      'Escribe algo antes de responder 💬'           => __('Escribe algo antes de responder 💬'),
      'Respuesta enviada 💬'                         => __('Respuesta enviada 💬'),
      'Error al enviar respuesta'                    => __('Error al enviar respuesta'),
      '💬 Responder'                                 => __('💬 Responder'),
      '🗑️ Eliminar'                                  => __('🗑️ Eliminar'),
      '🚨 Reportar'                                  => __('🚨 Reportar'),
      '¿Eliminar comentario?'                        => __('¿Eliminar comentario?'),
      'Eliminar'                                     => __('Eliminar'),
      'Cancelar'                                     => __('Cancelar'),
      'Comentario eliminado 🗑️'                      => __('Comentario eliminado 🗑️'),
      'Error al eliminar comentario'                 => __('Error al eliminar comentario'),
      'Like añadido ❤️'                              => __('Like añadido ❤️'),
      'Like quitado 💔'                              => __('Like quitado 💔'),
      'Error al procesar like'                       => __('Error al procesar like'),
      'Inicia sesión para dar like 🐾'               => __('Inicia sesión para dar like 🐾'),
      'Sin notificaciones por ahora 🐾'              => __('Sin notificaciones por ahora 🐾'),
      'Todas las notificaciones leídas ✅'           => __('Todas las notificaciones leídas ✅'),
      'Selecciona un motivo para el reporte'         => __('Selecciona un motivo para el reporte'),
      'Reporte enviado correctamente ✅'             => __('Reporte enviado correctamente ✅'),
      'Error al enviar el reporte'                   => __('Error al enviar el reporte'),
      'Error de conexión'                            => __('Error de conexión'),
      'Error'                                        => __('Error'),
      '¡Donación completada! Gracias por tu apoyo ❤️' => __('¡Donación completada! Gracias por tu apoyo ❤️'),
      'Algo fue mal con el pago. Inténtalo de nuevo.' => __('Algo fue mal con el pago. Inténtalo de nuevo.'),
      'faq_q1' => __('faq_q1'), 'faq_a1' => __('faq_a1'),
      'faq_q2' => __('faq_q2'), 'faq_a2' => __('faq_a2'),
      'faq_q3' => __('faq_q3'), 'faq_a3' => __('faq_a3'),
      'faq_q4' => __('faq_q4'), 'faq_a4' => __('faq_a4'),
      'faq_q5' => __('faq_q5'), 'faq_a5' => __('faq_a5'),
      'faq_q6' => __('faq_q6'), 'faq_a6' => __('faq_a6'),
      'Debes marcar al menos media estrella para valorar.' => __('Debes marcar al menos media estrella para valorar.'),
      '🐾 Añadir mascota'                                              => __('🐾 Añadir mascota'),
      '✏️ Editar mascota'                                              => __('✏️ Editar mascota'),
      'Guardando...'                                                   => __('Guardando...'),
      'Error al cargar la mascota'                                     => __('Error al cargar la mascota'),
      'Solo se permiten hasta 5 fotos.'                                => __('Solo se permiten hasta 5 fotos.'),
      '✅ Mascota actualizada'                                         => __('✅ Mascota actualizada'),
      '🐾 ¡Mascota añadida correctamente!'                            => __('🐾 ¡Mascota añadida correctamente!'),
      'Error de conexión con el servidor'                              => __('Error de conexión con el servidor'),
      '¿Eliminar mascota?'                                             => __('¿Eliminar mascota?'),
      'Esta acción borrará todos los datos de tu mascota de forma permanente.' => __('Esta acción borrará todos los datos de tu mascota de forma permanente.'),
      'Cancelar'                                                       => __('Cancelar'),
      'Eliminar'                                                       => __('Eliminar'),
      'Mascota eliminada correctamente'                                => __('Mascota eliminada correctamente'),
      'Error al conectar con el servidor'                              => __('Error al conectar con el servidor'),
      'Sin descripción.'                                               => __('Sin descripción.'),
      'Editar'                                                         => __('Editar'),
      'Añadir recordatorio'                                            => __('Añadir recordatorio'),
      'Sin vacunas registradas.'                                       => __('Sin vacunas registradas.'),
      'Sin recordatorios.'                                             => __('Sin recordatorios.'),
      '🔔 Recordatorio añadido correctamente'                          => __('🔔 Recordatorio añadido correctamente'),
      'Error de conexión'                                              => __('Error de conexión'),
      'Recordatorio eliminado 🗑️'                                     => __('Recordatorio eliminado 🗑️'),
      'Error al eliminar'                                              => __('Error al eliminar'),
      'Guardar cambios'                                                => __('Guardar cambios'),
      'Guardar recordatorio'                                           => __('Guardar recordatorio'),
      'año'                                                            => __('año'),
      'años'                                                           => __('años'),
      '+ Otro'                                                         => __('+ Otro'),
    ];
  @endphp
  window.i18n = {!! json_encode($i18n, JSON_UNESCAPED_UNICODE) !!};

  if (window.AUTH_USER_ID) {
    let lastNotificationId = null;

    // Preguntamos al servidor cada 15 segundos
    setInterval(async () => {
      try {
        const res = await fetch('/notifications/check');
        if (!res.ok) return;
        
        const data = await res.json();
        
        // Si hay una notificación nueva que no hemos mostrado todavía...
        if (data.latest && data.latest.id !== lastNotificationId) {
            lastNotificationId = data.latest.id;
            
            // Lanzamos el Toast emergente en la pantalla
            if (typeof showToast === 'function') {
                showToast('🔔 ' + data.latest.titulo);
            }
            
            // OPCIONAL: Si tienes una bolita roja en tu menú, puedes iluminarla aquí
            const bellBadge = document.getElementById('notification-badge'); // Cambia el ID por el tuyo
            if (bellBadge) {
                bellBadge.style.display = 'inline-block';
            }
        }
      } catch (error) {
      }
    }, 15000);
  }
</script>

<!-- JS -->
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/pets.js') }}?v=3"></script>

</body>
</html>