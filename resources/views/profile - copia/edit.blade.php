@extends('layouts.app')

@section('content')
<div class="profile-edit-container">
    <div class="profile-edit-card">
        <h1>Editar perfil</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Foto de perfil -->
            <div class="form-group">
                <label for="foto_perfil">Foto de perfil</label>
                <div class="current-image">
                    <img src="{{ $user->foto_perfil_url }}" alt="Foto actual" id="current-avatar">
                </div>
                <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*" onchange="previewImage(this, 'current-avatar')">
                <small>Formatos permitidos: JPG, PNG, GIF. Máximo 2MB.</small>
            </div>

            <!-- Banner -->
            <div class="form-group">
                <label for="banner">Banner</label>
                <div class="current-banner">
                    <div style="background-image: url('{{ $user->banner_url }}');" id="current-banner"></div>
                </div>
                <input type="file" id="banner" name="banner" accept="image/*" onchange="previewBanner(this, 'current-banner')">
                <small>Formatos permitidos: JPG, PNG, GIF. Máximo 4MB.</small>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" required value="{{ old('nombre', $user->nombre) }}" placeholder="Tu nombre">
                @error('nombre')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos *</label>
                <input type="text" id="apellidos" name="apellidos" required value="{{ old('apellidos', $user->apellidos) }}" placeholder="Tus apellidos">
                @error('apellidos')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Descripción -->
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4" placeholder="Cuéntanos sobre ti...">{{ old('descripcion', $user->descripcion) }}</textarea>
                @error('descripcion')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Fecha de nacimiento -->
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de nacimiento</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $user->fecha_nacimiento ? $user->fecha_nacimiento->format('Y-m-d') : '') }}">
                @error('fecha_nacimiento')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Provincia -->
            <div class="form-group">
                <label for="provincia">Provincia</label>
                <input type="text" id="provincia" name="provincia" value="{{ old('provincia', $user->provincia) }}" placeholder="Ej: Madrid">
                @error('provincia')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Ciudad -->
            <div class="form-group">
                <label for="ciudad">Ciudad</label>
                <input type="text" id="ciudad" name="ciudad" value="{{ old('ciudad', $user->ciudad) }}" placeholder="Ej: Madrid">
                @error('ciudad')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('profile.show') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection

<script>
function previewImage(input, imgId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(imgId).src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewBanner(input, bannerId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(bannerId).style.backgroundImage = `url(${e.target.result})`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<style>
.profile-edit-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.profile-edit-card {
    background: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.profile-edit-card h1 {
    color: var(--primary);
    margin-bottom: 30px;
    text-align: center;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-primary);
}

.form-group input[type="text"],
.form-group input[type="date"],
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.current-image img,
.current-banner div {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ddd;
}

.current-banner div {
    border-radius: 8px;
    background-size: cover;
    background-position: center;
}

.form-group input[type="file"] {
    margin-top: 10px;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: var(--text-secondary);
    font-size: 12px;
}

.error {
    color: #e74c3c;
    font-size: 14px;
    margin-top: 5px;
    display: block;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-primary {
    background: #6c757d;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert ul {
    margin: 0;
    padding-left: 20px;
}
</style>