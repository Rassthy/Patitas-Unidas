@extends('layouts.app')

@section('content')
<div class="private-area">
  <div class="private-header">
    <a href="{{ route('mis-mascotas.show', $pet) }}" class="btn-s">
      <i class="fa-solid fa-arrow-left"></i> Volver
    </a>
    <h1>Editar — {{ $pet->nombre }}</h1>
  </div>

  <form method="POST" action="{{ route('mis-mascotas.update', $pet) }}" enctype="multipart/form-data" class="pet-form">
    @csrf @method('PUT')

    <div class="form-group">
      <label for="nombre">Nombre <span class="req">*</span></label>
      <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $pet->nombre) }}" required maxlength="100" class="form-input @error('nombre') is-error @enderror">
      @error('nombre')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="especie">Especie</label>
        <input type="text" id="especie" name="especie" value="{{ old('especie', $pet->especie) }}" maxlength="50" class="form-input @error('especie') is-error @enderror">
        @error('especie')<span class="form-error">{{ $message }}</span>@enderror
      </div>
      <div class="form-group">
        <label for="raza">Raza</label>
        <input type="text" id="raza" name="raza" value="{{ old('raza', $pet->raza) }}" maxlength="50" class="form-input @error('raza') is-error @enderror">
        @error('raza')<span class="form-error">{{ $message }}</span>@enderror
      </div>
      <div class="form-group form-group--sm">
        <label for="edad">Edad (años)</label>
        <input type="number" id="edad" name="edad" value="{{ old('edad', $pet->edad) }}" min="0" max="100" class="form-input @error('edad') is-error @enderror">
        @error('edad')<span class="form-error">{{ $message }}</span>@enderror
      </div>
    </div>

    <div class="form-group">
      <label for="descripcion">Descripción</label>
      <textarea id="descripcion" name="descripcion" rows="4" class="form-input @error('descripcion') is-error @enderror">{{ old('descripcion', $pet->descripcion) }}</textarea>
      @error('descripcion')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-group">
      <label for="foto">Foto</label>
      @if($pet->foto)
        <div class="pet-current-photo">
          <img src="{{ Storage::url($pet->foto) }}" alt="{{ $pet->nombre }}">
          <span class="muted">Sube una nueva para reemplazarla</span>
        </div>
      @endif
      <input type="file" id="foto" name="foto" accept="image/*" class="form-input @error('foto') is-error @enderror">
      @error('foto')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-actions">
      <a href="{{ route('mis-mascotas.show', $pet) }}" class="btn-s">Cancelar</a>
      <button type="submit" class="btn-p">Guardar cambios</button>
    </div>
  </form>
</div>
@endsection
