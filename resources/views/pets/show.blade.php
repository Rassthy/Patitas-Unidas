@extends('layouts.app')

@section('content')
<div class="private-area">

  {{-- Cabecera --}}
  <div class="private-header">
    <a href="{{ route('mis-mascotas.index') }}" class="btn-s">
      <i class="fa-solid fa-arrow-left"></i> Mis mascotas
    </a>
    <div style="display:flex;gap:8px;">
      <a href="{{ route('mis-mascotas.edit', $pet) }}" class="btn-s">
        <i class="fa-solid fa-pen"></i> Editar
      </a>
      <form method="POST" action="{{ route('mis-mascotas.destroy', $pet) }}" onsubmit="return confirm('¿Eliminar a {{ $pet->nombre }}?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn-s" style="color:var(--terra)">
          <i class="fa-solid fa-trash"></i> Eliminar
        </button>
      </form>
    </div>
  </div>

  {{-- Perfil de la mascota --}}
  <div class="pet-profile">
    <div class="pet-profile__photo">
      @if($pet->foto)
        <img src="{{ Storage::url($pet->foto) }}" alt="{{ $pet->nombre }}">
      @else
        <div class="pet-card__no-photo pet-card__no-photo--lg"><i class="fa-solid fa-paw"></i></div>
      @endif
    </div>
    <div class="pet-profile__info">
      <h2>{{ $pet->nombre }}</h2>
      <p class="pet-card__meta">
        @if($pet->especie)<span><i class="fa-solid fa-tag"></i> {{ $pet->especie }}</span>@endif
        @if($pet->raza)<span><i class="fa-solid fa-dna"></i> {{ $pet->raza }}</span>@endif
        @if($pet->edad !== null)<span><i class="fa-solid fa-cake-candles"></i> {{ $pet->edad }} años</span>@endif
      </p>
      @if($pet->descripcion)<p class="pet-profile__desc">{{ $pet->descripcion }}</p>@endif
    </div>
  </div>

  <div class="pet-sections">

    {{-- ================== VACUNAS ================== --}}
    <section class="pet-section">
      <div class="pet-section__head">
        <h3><i class="fa-solid fa-syringe"></i> Vacunas</h3>
      </div>

      {{-- Formulario nueva vacuna --}}
      <form method="POST" action="{{ route('pets.vaccines.store', $pet) }}" class="inline-form">
        @csrf
        <input type="text" name="nombre_vacuna" placeholder="Nombre de la vacuna" required maxlength="100" class="form-input @error('nombre_vacuna') is-error @enderror">
        <input type="date" name="fecha_administracion" required class="form-input @error('fecha_administracion') is-error @enderror" title="Fecha administración">
        <input type="date" name="proxima_dosis" class="form-input @error('proxima_dosis') is-error @enderror" title="Próxima dosis (opcional)">
        <button type="submit" class="btn-sm"><i class="fa-solid fa-plus"></i> Añadir</button>
      </form>
      @error('nombre_vacuna')<span class="form-error">{{ $message }}</span>@enderror
      @error('fecha_administracion')<span class="form-error">{{ $message }}</span>@enderror
      @error('proxima_dosis')<span class="form-error">{{ $message }}</span>@enderror

      {{-- Lista de vacunas --}}
      @if($pet->vaccines->isEmpty())
        <p class="empty-inline">Sin vacunas registradas aún.</p>
      @else
        <table class="pet-table">
          <thead>
            <tr>
              <th>Vacuna</th>
              <th>Administrada</th>
              <th>Próxima dosis</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($pet->vaccines as $v)
            <tr>
              <td>{{ $v->nombre_vacuna }}</td>
              <td>{{ $v->fecha_administracion->format('d/m/Y') }}</td>
              <td>
                @if($v->proxima_dosis)
                  <span class="{{ $v->proxima_dosis->isPast() ? 'badge-danger' : 'badge-ok' }}">
                    {{ $v->proxima_dosis->format('d/m/Y') }}
                  </span>
                @else
                  <span class="muted">—</span>
                @endif
              </td>
              <td>
                <form method="POST" action="{{ route('pets.vaccines.destroy', [$pet, $v]) }}">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-outline btn-outline--danger" title="Eliminar" onclick="return confirm('¿Eliminar vacuna?')">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </section>

    {{-- ================== RECORDATORIOS ================== --}}
    <section class="pet-section">
      <div class="pet-section__head">
        <h3><i class="fa-solid fa-bell"></i> Recordatorios</h3>
      </div>

      {{-- Formulario nuevo recordatorio --}}
      <form method="POST" action="{{ route('pets.reminders.store', $pet) }}" class="inline-form inline-form--col">
        @csrf
        <div class="form-row">
          <input type="text" name="titulo" placeholder="Título del recordatorio" required maxlength="100" class="form-input @error('titulo') is-error @enderror">
          <input type="datetime-local" name="fecha_alarma" required class="form-input @error('fecha_alarma') is-error @enderror" title="Fecha y hora">
        </div>
        <div class="form-row">
          <textarea name="mensaje" placeholder="Mensaje (opcional)" rows="2" class="form-input @error('mensaje') is-error @enderror"></textarea>
          <button type="submit" class="btn-sm" style="align-self:flex-end"><i class="fa-solid fa-plus"></i> Añadir</button>
        </div>
      </form>
      @error('titulo')<span class="form-error">{{ $message }}</span>@enderror
      @error('fecha_alarma')<span class="form-error">{{ $message }}</span>@enderror

      {{-- Lista de recordatorios --}}
      @if($pet->reminders->isEmpty())
        <p class="empty-inline">Sin recordatorios registrados aún.</p>
      @else
        <div class="reminders-list">
          @foreach($pet->reminders as $r)
          <div class="reminder-item {{ $r->fecha_alarma->isPast() ? 'reminder-item--past' : '' }}">
            <div class="reminder-item__info">
              <strong>{{ $r->titulo }}</strong>
              <span class="muted">{{ $r->fecha_alarma->format('d/m/Y H:i') }}</span>
              @if($r->mensaje)<p>{{ $r->mensaje }}</p>@endif
            </div>
            <form method="POST" action="{{ route('pets.reminders.destroy', [$pet, $r]) }}">
              @csrf @method('DELETE')
              <button type="submit" class="btn-outline btn-outline--danger" title="Eliminar" onclick="return confirm('¿Eliminar recordatorio?')">
                <i class="fa-solid fa-trash"></i>
              </button>
            </form>
          </div>
          @endforeach
        </div>
      @endif
    </section>

  </div>
</div>
@endsection
