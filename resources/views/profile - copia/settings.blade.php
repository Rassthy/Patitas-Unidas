@extends('layouts.app')

@section('content')
<div class="profile-edit-container">
    <div class="profile-edit-card">
        <h1>Preferencias de la cuenta</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('profile.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3>Apariencia e Idioma</h3>
                
                <div class="form-group">
                    <label for="tema">Tema de la plataforma</label>
                    <select id="tema" name="settings[tema]">
                        <option value="claro" {{ ($user->user_settings['tema'] ?? '') == 'claro' ? 'selected' : '' }}>Modo Claro</option>
                        <option value="oscuro" {{ ($user->user_settings['tema'] ?? '') == 'oscuro' ? 'selected' : '' }}>Modo Oscuro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="idioma">Idioma</label>
                    <select id="idioma" name="settings[idioma]">
                        <option value="es" {{ ($user->user_settings['idioma'] ?? '') == 'es' ? 'selected' : '' }}>Español</option>
                        <option value="en" {{ ($user->user_settings['idioma'] ?? '') == 'en' ? 'selected' : '' }}>Inglés</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar preferencias</button>
            </div>
        </form>
    </div>
</div>
@endsection