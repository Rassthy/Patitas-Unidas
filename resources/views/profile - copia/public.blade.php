@extends('layouts.app')

@section('content')
<div class="profile-container">
    <div class="profile-banner" style="background-image: url('{{ $user->banner_url }}');">
        <div class="profile-banner-overlay">
            <div class="profile-avatar">
                <img src="{{ $user->foto_perfil_url }}" alt="Foto de perfil de {{ $user->nombre }}">
            </div>
        </div>
    </div>

    <div class="profile-content">
        <div class="profile-header">
            <h1>{{ $user->nombre }} {{ $user->apellidos }}</h1>
            <p class="profile-username">{{ '@' . $user->username }}</p>
            </div>

        <div class="profile-info">
            <div class="info-section">
                <h3>Acerca de {{ $user->nombre }}</h3>
                <div class="info-grid">
                    @if($user->provincia || $user->ciudad)
                    <div class="info-item">
                        <label>Ubicación:</label>
                        <span>{{ $user->ciudad ?? '' }} {{ $user->provincia ? '('.$user->provincia.')' : '' }}</span>
                    </div>
                    @endif
                    <div class="info-item">
                        <label>Miembro desde:</label>
                        <span>{{ $user->created_at->format('M Y') }}</span>
                    </div>
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