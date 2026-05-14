<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 480px; margin: 40px auto; background: #fff; border-radius: 12px; padding: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .logo { text-align: center; font-size: 28px; margin-bottom: 8px; }
        h2 { color: #2d6a4f; text-align: center; margin-top: 0; }
        p { color: #555; line-height: 1.6; }
        .codigo { display: block; text-align: center; font-size: 42px; font-weight: bold; letter-spacing: 10px; color: #2d6a4f; background: #e8f5e9; border-radius: 8px; padding: 20px; margin: 28px 0; }
        .footer { text-align: center; color: #aaa; font-size: 12px; margin-top: 32px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🐾</div>
        <h2>¡Hola, {{ $nombre }}!</h2>
        <p>Gracias por registrarte en <strong>PatitasUnidas</strong>. Para completar tu registro, introduce el siguiente código en la pantalla de verificación:</p>
        <span class="codigo">{{ $codigo }}</span>
        <p>Este código expira en <strong>10 minutos</strong>. Si no solicitaste este registro, puedes ignorar este correo.</p>
        <div class="footer">PatitasUnidas &mdash; Conectando patitas con hogares 🏠</div>
    </div>
</body>
</html>