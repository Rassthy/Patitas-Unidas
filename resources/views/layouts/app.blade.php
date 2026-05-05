<!DOCTYPE html>
<html lang="es">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PatitasUnidas</title>

  <!-- Fuentes -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <!-- Iconos -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- CSS -->
  <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
  <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sections.css') }}">
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
  <link rel="stylesheet" href="{{ asset('css/donations.css') }}">
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

<!-- Auth user para JS -->
<script>
  window.AUTH_USER_ID = {{ Auth::check() ? Auth::id() : 'null' }};
</script>

<!-- Traducciones para JS -->
<script>
  @php
    $i18n = [
      // ── app.js · publicaciones ──────────────────────────────────────────────
      'Error al cargar publicaciones'                => __('Error al cargar publicaciones'),
      'publicacion'                                  => __('publicacion'),
      'publicaciones'                                => __('publicaciones'),
      'No hay publicaciones en esta zona todavía.'   => __('No hay publicaciones en esta zona todavía.'),
      'Saber más'                                    => __('Saber más'),

      // ── app.js · comentarios ────────────────────────────────────────────────
      'Escribe un comentario antes de enviar 💬'     => __('Escribe un comentario antes de enviar 💬'),
      'Comentario enviado ✉️'                        => __('Comentario enviado ✉️'),
      'Error al enviar comentario'                   => __('Error al enviar comentario'),

      // ── app.js · chat ───────────────────────────────────────────────────────
      'Error al cargar la conversación 🐾'           => __('Error al cargar la conversación 🐾'),
      'Sé el primero en escribir 🐾'                 => __('Sé el primero en escribir 🐾'),
      '🔒 Inicio de la conversación'                 => __('🔒 Inicio de la conversación'),
      'Error al enviar mensaje'                      => __('Error al enviar mensaje'),
      'No puedes chatear contigo mismo 😄'           => __('No puedes chatear contigo mismo 😄'),
      'Error al iniciar chat'                        => __('Error al iniciar chat'),

      // ── app.js · login / registro ───────────────────────────────────────────
      '¡Bienvenido de vuelta a PatitasUnidas! 🐾'   => __('¡Bienvenido de vuelta a PatitasUnidas! 🐾'),
      '¡Cuenta creada! Bienvenido a PatitasUnidas 🐾🎉' => __('¡Cuenta creada! Bienvenido a PatitasUnidas 🐾🎉'),

      // ── app.js · imágenes ───────────────────────────────────────────────────
      'Máximo 10 imágenes permitidas'                => __('Máximo 10 imágenes permitidas'),

      // ── app.js · nueva publicación ──────────────────────────────────────────
      'Publicación creada correctamente! 🎉'         => __('Publicación creada correctamente! 🎉'),
      'Error al crear publicación. Inténtalo de nuevo.' => __('Error al crear publicación. Inténtalo de nuevo.'),
      'Error de conexión. Verifica tu conexión a internet.' => __('Error de conexión. Verifica tu conexión a internet.'),

      // ── app.js · FAQ ────────────────────────────────────────────────────────
      'faq_q1' => __('faq_q1'),
      'faq_a1' => __('faq_a1'),
      'faq_q2' => __('faq_q2'),
      'faq_a2' => __('faq_a2'),
      'faq_q3' => __('faq_q3'),
      'faq_a3' => __('faq_a3'),
      'faq_q4' => __('faq_q4'),
      'faq_a4' => __('faq_a4'),
      'faq_q5' => __('faq_q5'),
      'faq_a5' => __('faq_a5'),
      'faq_q6' => __('faq_q6'),
      'faq_a6' => __('faq_a6'),

      // ── ui.js · categorías ──────────────────────────────────────────────────
      '📋 Todas las publicaciones'                   => __('📋 Todas las publicaciones'),
      '🏠 Adoptar mascota'                           => __('🏠 Adoptar mascota'),
      '🔍 Mascota perdida o robada'                  => __('🔍 Mascota perdida o robada'),
      '❤️ Apoyar animales'                           => __('❤️ Apoyar animales'),

      // ── ui.js · modal publicación ───────────────────────────────────────────
      'Error al cargar la publicación'               => __('Error al cargar la publicación'),
      '🏥 Protectora verificada'                     => __('🏥 Protectora verificada'),
      '🌟 Organización'                              => __('🌟 Organización'),
      '👤 Usuario'                                   => __('👤 Usuario'),
      'Nombre'                                       => __('Nombre'),
      'Especie'                                      => __('Especie'),
      'Raza'                                         => __('Raza'),
      'Mensaje'                                      => __('Mensaje'),
      'Perfil'                                       => __('Perfil'),

      // ── ui.js · chat panel ──────────────────────────────────────────────────
      'No tienes conversaciones aún 🐾'              => __('No tienes conversaciones aún 🐾'),
      'Sin mensajes aún'                             => __('Sin mensajes aún'),
      'No se encontraron usuarios'                   => __('No se encontraron usuarios'),
      'No se pudo encontrar el nombre de usuario para este perfil 🐾' => __('No se pudo encontrar el nombre de usuario para este perfil 🐾'),

      // ── ui.js · comentarios ─────────────────────────────────────────────────
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

      // ── ui.js · eliminar comentario ─────────────────────────────────────────
      '¿Eliminar comentario?'                        => __('¿Eliminar comentario?'),
      'Eliminar'                                     => __('Eliminar'),
      'Cancelar'                                     => __('Cancelar'),
      'Comentario eliminado 🗑️'                      => __('Comentario eliminado 🗑️'),
      'Error al eliminar comentario'                 => __('Error al eliminar comentario'),

      // ── ui.js · likes ───────────────────────────────────────────────────────
      'Like añadido ❤️'                              => __('Like añadido ❤️'),
      'Like quitado 💔'                              => __('Like quitado 💔'),
      'Error al procesar like'                       => __('Error al procesar like'),
      'Inicia sesión para dar like 🐾'               => __('Inicia sesión para dar like 🐾'),

      // ── ui.js · notificaciones ──────────────────────────────────────────────
      'Sin notificaciones por ahora 🐾'              => __('Sin notificaciones por ahora 🐾'),
      'Todas las notificaciones leídas ✅'           => __('Todas las notificaciones leídas ✅'),

      // ── ui.js · reportes ────────────────────────────────────────────────────
      'Selecciona un motivo para el reporte'         => __('Selecciona un motivo para el reporte'),
      'Reporte enviado correctamente ✅'             => __('Reporte enviado correctamente ✅'),
      'Error al enviar el reporte'                   => __('Error al enviar el reporte'),

      // ── compartidas ─────────────────────────────────────────────────────────
      'Error de conexión'                            => __('Error de conexión'),
      'Error'                                        => __('Error'),

      // ── donations.js ────────────────────────────────────────────────────────
      '¡Donación completada! Gracias por tu apoyo ❤️' => __('¡Donación completada! Gracias por tu apoyo ❤️'),
      'Algo fue mal con el pago. Inténtalo de nuevo.' => __('Algo fue mal con el pago. Inténtalo de nuevo.'),
    ];
  @endphp
  window.i18n = {!! json_encode($i18n, JSON_UNESCAPED_UNICODE) !!};
</script>

<!-- JS -->
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>

</body>
</html>