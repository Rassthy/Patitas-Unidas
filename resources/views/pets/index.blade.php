@extends('layouts.app')

@section('content')
<div class="private-area">
  <div class="private-header">
    <h1><i class="fa-solid fa-paw"></i> Mis Mascotas</h1>
    <a href="{{ route('mis-mascotas.create') }}" class="btn-p">
      <i class="fa-solid fa-plus"></i> Añadir mascota
    </a>
  </div>

  @if($pets->isEmpty())
    <div class="empty-state">
      <i class="fa-solid fa-dog"></i>
      <p>Aún no tienes mascotas registradas.</p>
      <a href="{{ route('mis-mascotas.create') }}" class="btn-p">Añadir mi primera mascota</a>
    </div>
  @else
    <div class="pets-grid">
      @foreach($pets as $pet)
        <div class="pet-card">
          <div class="pet-card__photo">
            @if($pet->foto)
              <img src="{{ Storage::url($pet->foto) }}" alt="{{ $pet->nombre }}">
            @else
              <div class="pet-card__no-photo"><i class="fa-solid fa-paw"></i></div>
            @endif
          </div>
          <div class="pet-card__body">
            <h3 class="pet-card__name">{{ $pet->nombre }}</h3>
            <p class="pet-card__meta">
              @if($pet->especie)<span>{{ $pet->especie }}</span>@endif
              @if($pet->raza)<span>· {{ $pet->raza }}</span>@endif
              @if($pet->edad !== null)<span>· {{ $pet->edad }} años</span>@endif
            </p>
            @if($pet->descripcion)
              <p class="pet-card__desc">{{ Str::limit($pet->descripcion, 80) }}</p>
            @endif
          </div>
          <div class="pet-card__actions">
            <a href="{{ route('mis-mascotas.show', $pet) }}" class="btn-outline">
              <i class="fa-solid fa-eye"></i> Ver
            </a>
            <a href="{{ route('mis-mascotas.edit', $pet) }}" class="btn-outline">
              <i class="fa-solid fa-pen"></i> Editar
            </a>
            <form method="POST" action="{{ route('mis-mascotas.destroy', $pet) }}" onsubmit="return confirm('¿Eliminar a {{ $pet->nombre }}?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn-outline btn-outline--danger">
                <i class="fa-solid fa-trash"></i>
              </button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
