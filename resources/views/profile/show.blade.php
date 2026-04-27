@extends('layouts.app')

@section('content')
<div class="profile-container">
    <!-- Banner -->
    <div class="profile-banner" style="background-image: url('{{ $user->banner ? asset('storage/' . $user->banner) : 'https://via.placeholder.com/1200x300/4CAF50/FFFFFF?text=Banner' }}');">
        <div class="profile-banner-overlay">
            <div class="profile-avatar">
                <img src="{{ $user->foto_perfil ? asset('storage/' . $user->foto_perfil) : 'https://via.placeholder.com/100x100/2196F3/FFFFFF?text=' . urlencode(substr($user->nombre, 0, 1) . substr($user->apellidos, 0, 1)) }}" alt="Foto de perfil">
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

<style>
.profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.profile-banner {
    height: 300px;
    background-size: cover;
    background-position: center;
    background-color: #f0f0f0;
    position: relative;
    border-radius: 8px;
    margin-bottom: 20px;
}

.profile-banner-overlay {
    position: absolute;
    bottom: -50px;
    left: 50px;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-content {
    margin-top: 60px;
}

.profile-header {
    text-align: center;
    margin-bottom: 30px;
}

.profile-header h1 {
    margin: 0;
    color: var(--text-primary);
}

.profile-username {
    color: var(--text-secondary);
    margin: 5px 0 15px 0;
}

.profile-info {
    background: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.info-section {
    margin-bottom: 30px;
}

.info-section h3 {
    color: var(--primary);
    margin-bottom: 15px;
    border-bottom: 2px solid var(--primary);
    padding-bottom: 5px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-item label {
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 5px;
}

.info-item span {
    color: var(--text-primary);
}

.info-section p {
    line-height: 1.6;
    color: var(--text-primary);
}
</style>