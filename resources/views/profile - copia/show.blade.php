@extends('layouts.app')

@section('content')
<div class="profile-container">
    <!-- Banner -->
    <div class="profile-banner" style="background-image: url('{{ $user->banner_url }}');">
        <div class="profile-banner-overlay">
            <div class="profile-avatar">
                <img src="{{ $user->foto_perfil_url }}" alt="Foto de perfil">
            </div>
        </div>
    </div>

    <!-- Información del perfil -->
    <div class="profile-content">
        <div class="profile-header">
            <h1>{{ $user->nombre }} {{ $user->apellidos }}</h1>
            <p class="profile-username">{{ '@' . $user->username }}</p>
            <a href="{{ route('profile.edit') }}" class="btn btn-primary">Editar perfil</a>
        </div>

        <div class="profile-info">
            <div class="info-section">
                <h3>Información básica</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Email:</label>
                        <span>{{ $user->email }}</span>
                    </div>
                    <div class="info-item">
                        <label>DNI/NIE:</label>
                        <span>{{ $user->dni_nie }}</span>
                    </div>
                    <div class="info-item">
                        <label>Teléfono:</label>
                        <span>{{ $user->telefono }}</span>
                    </div>
                    @if($user->fecha_nacimiento)
                    <div class="info-item">
                        <label>Fecha de nacimiento:</label>
                        <span>{{ $user->fecha_nacimiento->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    @if($user->provincia)
                    <div class="info-item">
                        <label>Provincia:</label>
                        <span>{{ $user->provincia }}</span>
                    </div>
                    @endif
                    @if($user->ciudad)
                    <div class="info-item">
                        <label>Ciudad:</label>
                        <span>{{ $user->ciudad }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if($user->descripcion)
            <div class="info-section">
                <h3>Descripción</h3>
                <p>{{ $user->descripcion }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection