# Sistema de Autenticación - Guía de Testing

## Ejecutar los tests de autenticación

### Todos los tests
```bash
php artisan test tests/Feature/AuthTest.php
```

### Tests específicos
```bash
php artisan test tests/Feature/AuthTest.php --filter=test_successful_registration
php artisan test tests/Feature/AuthTest.php --filter=test_successful_login
php artisan test tests/Feature/AuthTest.php --filter=test_successful_logout
```

### Con salida verbose
```bash
php artisan test tests/Feature/AuthTest.php -v
```

## Qué se testea

### Registro
- ✅ Registro exitoso con datos válidos
- ✅ Fallo: Email duplicado
- ✅ Fallo: Username duplicado
- ✅ Fallo: DNI inválido
- ✅ Fallo: Teléfono inválido
- ✅ Fallo: Contraseña débil
- ✅ Fallo: Contraseñas no coinciden

### Login
- ✅ Login exitoso
- ✅ Fallo: Email incorrecto
- ✅ Fallo: Contraseña incorrecta

### Sesiones y Seguridad
- ✅ Logout exitoso
- ✅ Session regeneration en login
- ✅ Check auth endpoint

## Validaciones en Backend

### DNI/NIE
- Formato: `12345678A` (8 dígitos + letra mayúscula)
- O formato NIE: `X1234567L`, `Y1234567L`, `Z1234567L`

### Teléfono
- Acepta: `600000000`, `+34 600 000 000`, `34 600000000`, etc.
- Debe ser número español (6xx, 7xx, 9xx)
- La validación backend normaliza automáticamente

### Contraseña
- Mínimo 8 caracteres
- Debe incluir: mayúscula, minúscula, número
- Confirmación requerida en frontend

### Email
- Validación RFC + DNS

### Username
- Mínimo 3, máximo 50 caracteres
- Solo: letras, números, guiones, puntos, guiones bajos
- Único en la base de datos

### Nombre/Apellidos
- Solo letras, espacios, apóstrofes y guiones
- Máximo 100 caracteres

## Validación Frontend

El modal de login/registro incluye:
- Visualización clara de errores por campo
- Colores de error (borde rojo, fondo)
- Mensajes descriptivos
- Validación de coincidencia de contraseñas antes de enviar

## Rutas de Autenticación

| Ruta | Método | Descripción | Autenticación Requerida |
|------|--------|-------------|------------------------|
| `/register` | POST | Registrar nuevo usuario | No |
| `/login` | POST | Iniciar sesión | No |
| `/logout` | POST | Cerrar sesión | Sí |
| `/check-auth` | GET | Verificar estado de autenticación | No |

## Middleware de Protección

Para proteger una ruta, usa:

```php
Route::middleware('auth')->group(function () {
    Route::get('/mi-perfil', [ProfileController::class, 'show']);
    Route::get('/mis-mascotas', [PetController::class, 'index']);
    // ...
});
```

## Estado de la Sesión

Las sesiones se almacenan en la base de datos en la tabla `sessions`.

Para limpiar sesiones antiguas:
```bash
php artisan session:prune-stale-sessions
```

## Configuración CSRF

CSRF está habilitado globalmente. Asegúrate de que todos los formularios incluyan:
```blade
@csrf
```
