<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu correo</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #FDFBF7; color: #333333;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #FDFBF7; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden;">
                    
                    <tr>
                        <td align="center" style="padding: 40px 0 20px 0; background-color: #F9F6F0; border-bottom: 2px solid #E8D5C4;">
                            <img src="{{ $message->embed(public_path('img/defaults/LogoPU.png')) }}" alt="Patitas Unidas Logo" style="width: 120px; height: auto;">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="margin-top: 0; color: #1a1a1a; font-size: 24px;">¡Hola, {{ $nombre }}!</h2>
                            <p style="font-size: 16px; line-height: 1.6; color: #555555;">
                                Gracias por unirte a <strong>Patitas Unidas</strong>. Estamos muy felices de tenerte en nuestra comunidad.<br><br>
                                Para garantizar la seguridad de todos nuestros peludos, necesitamos verificar tu dirección de correo electrónico.
                            </p>

                            <div style="text-align: center; margin: 40px 0;">
                                <a href="{{ route('verificar.email.auto', ['id' => $userId, 'codigo' => $codigo]) }}" style="background-color: #D96C4A; color: #ffffff; padding: 15px 30px; text-decoration: none; font-size: 18px; font-weight: bold; border-radius: 50px; display: inline-block;">
                                    Verificar mi correo
                                </a>
                            </div>

                            <p style="font-size: 16px; line-height: 1.6; color: #555555; text-align: center;">
                                O si lo prefieres, puedes introducir manualmente este código de 6 dígitos en la página web:
                            </p>

                            <div style="text-align: center; margin: 20px 0;">
                                <span style="display: inline-block; background-color: #E8D5C4; color: #333333; font-size: 32px; font-weight: bold; padding: 15px 40px; border-radius: 10px; letter-spacing: 5px;">
                                    {{ $codigo }}
                                </span>
                            </div>

                            <p style="font-size: 14px; color: #888888; margin-top: 30px; text-align: center;">
                                Este enlace y código expirarán en 10 minutos.<br>
                                Si no has creado una cuenta en Patitas Unidas, ignora este mensaje.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #F9F6F0; padding: 20px; text-align: center; font-size: 12px; color: #999999;">
                            &copy; {{ date('Y') }} Patitas Unidas. Todos los derechos reservados.<br>
                            Conectando patas con corazones.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>