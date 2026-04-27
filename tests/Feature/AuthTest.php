<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Registro exitoso con datos válidos.
     */
    public function test_successful_registration(): void
    {
        $response = $this->post('/register', [
            'nombre' => 'Juan',
            'apellidos' => 'Pérez López',
            'username' => 'juanperez',
            'email' => 'juan@example.com',
            'dni_nie' => '12345678A',
            'telefono' => '600123456',
            'password' => 'TestPass123',
            'password_confirmation' => 'TestPass123',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Cuenta creada correctamente. ¡Bienvenido a PatitasUnidas!');

        $this->assertDatabaseHas('users', [
            'email' => 'juan@example.com',
            'username' => 'juanperez',
            'nombre' => 'Juan',
            'apellidos' => 'Pérez López',
            'dni_nie' => '12345678A',
        ]);

        $this->assertAuthenticated();
    }

    /**
     * Test: Fallo en registro - Email duplicado.
     */
    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/register', [
            'nombre' => 'Juan',
            'apellidos' => 'Pérez',
            'username' => 'juanperez2',
            'email' => 'test@example.com',
            'dni_nie' => '87654321B',
            'telefono' => '600123456',
            'password' => 'TestPass123',
            'password_confirmation' => 'TestPass123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test: Fallo en registro - Username duplicado.
     */
    public function test_registration_fails_with_duplicate_username(): void
    {
        User::factory()->create(['username' => 'juanperez']);

        $response = $this->post('/register', [
            'nombre' => 'Juan',
            'apellidos' => 'Pérez',
            'username' => 'juanperez',
            'email' => 'juan@example.com',
            'dni_nie' => '87654321B',
            'telefono' => '600123456',
            'password' => 'TestPass123',
            'password_confirmation' => 'TestPass123',
        ]);

        $response->assertSessionHasErrors(['username']);
        $this->assertGuest();
    }

    /**
     * Test: Fallo en registro - DNI inválido.
     */
    public function test_registration_fails_with_invalid_dni(): void
    {
        $response = $this->post('/register', [
            'nombre' => 'Juan',
            'apellidos' => 'Pérez',
            'username' => 'juanperez',
            'email' => 'juan@example.com',
            'dni_nie' => 'INVALID123',
            'telefono' => '600123456',
            'password' => 'TestPass123',
            'password_confirmation' => 'TestPass123',
        ]);

        $response->assertSessionHasErrors(['dni_nie']);
        $this->assertGuest();
    }

    /**
     * Test: Fallo en registro - Teléfono inválido.
     */
    public function test_registration_fails_with_invalid_phone(): void
    {
        $response = $this->post('/register', [
            'nombre' => 'Juan',
            'apellidos' => 'Pérez',
            'username' => 'juanperez',
            'email' => 'juan@example.com',
            'dni_nie' => '12345678A',
            'telefono' => '123', // Inválido
            'password' => 'TestPass123',
            'password_confirmation' => 'TestPass123',
        ]);

        $response->assertSessionHasErrors(['telefono']);
        $this->assertGuest();
    }

    /**
     * Test: Fallo en registro - Contraseña sin mayúscula.
     */
    public function test_registration_fails_with_weak_password(): void
    {
        $response = $this->post('/register', [
            'nombre' => 'Juan',
            'apellidos' => 'Pérez',
            'username' => 'juanperez',
            'email' => 'juan@example.com',
            'dni_nie' => '12345678A',
            'telefono' => '600123456',
            'password' => 'weakpass123', // Sin mayúsculas
            'password_confirmation' => 'weakpass123',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    /**
     * Test: Fallo en registro - Contraseñas no coinciden.
     */
    public function test_registration_fails_with_mismatched_passwords(): void
    {
        $response = $this->post('/register', [
            'nombre' => 'Juan',
            'apellidos' => 'Pérez',
            'username' => 'juanperez',
            'email' => 'juan@example.com',
            'dni_nie' => '12345678A',
            'telefono' => '600123456',
            'password' => 'TestPass123',
            'password_confirmation' => 'TestPass456',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    /**
     * Test: Login exitoso.
     */
    public function test_successful_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password_hash' => Hash::make('TestPass123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'TestPass123',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Sesión iniciada correctamente.');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: Login falla - Email incorrecto.
     */
    public function test_login_fails_with_wrong_email(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password_hash' => Hash::make('TestPass123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'TestPass123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test: Login falla - Contraseña incorrecta.
     */
    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password_hash' => Hash::make('TestPass123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'WrongPass123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test: Logout exitoso.
     */
    public function test_successful_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }

    /**
     * Test: Check auth - Usuario autenticado.
     */
    public function test_check_auth_authenticated(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'nombre' => 'Test',
            'apellidos' => 'User',
        ]);

        $response = $this->actingAs($user)->get('/check-auth');

        $response->assertJson([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'username' => 'testuser',
                'email' => 'test@example.com',
                'nombre' => 'Test',
                'apellidos' => 'User',
            ],
        ]);
    }

    /**
     * Test: Check auth - Usuario no autenticado.
     */
    public function test_check_auth_unauthenticated(): void
    {
        $response = $this->get('/check-auth');

        $response->assertJson([
            'authenticated' => false,
            'user' => null,
        ]);
    }

    /**
     * Test: Session regeneration en login.
     */
    public function test_session_regenerated_on_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password_hash' => Hash::make('TestPass123'),
        ]);

        $sessionBefore = $this->get('/')->cookie('XSRF-TOKEN');

        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'TestPass123',
        ]);

        $sessionAfter = $this->get('/')->cookie('XSRF-TOKEN');

        $this->assertNotEquals($sessionBefore, $sessionAfter);
    }
}
