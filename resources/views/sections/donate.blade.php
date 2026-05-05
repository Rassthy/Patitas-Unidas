@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div id="donations-container">

  {{-- HERO --}}
  <div class="don-hero">
    <div class="don-hero-inner">
      <div class="don-hero-badge">❤️ PatitasUnidas</div>
      <h1 class="don-hero-title">Cada euro salva una <em>patita</em></h1>
      <p class="don-hero-desc">
        Con tu aportación ayudamos a sufragar gastos veterinarios, pienso y el
        mantenimiento de la plataforma para que más animales encuentren un hogar.
      </p>
      <div class="don-hero-stats">
        <div class="don-hstat">
          <span class="don-hstat-num">{{ number_format($totalDonated ?? 0, 2) }} €</span>
          <span class="don-hstat-lbl">recaudados en total</span>
        </div>
        <div class="don-hstat-sep"></div>
        <div class="don-hstat">
          <span class="don-hstat-num">{{ $totalDonors ?? 0 }}</span>
          <span class="don-hstat-lbl">donantes</span>
        </div>
        <div class="don-hstat-sep"></div>
        <div class="don-hstat">
          <span class="don-hstat-num">{{ $animalsHelped ?? 0 }}</span>
          <span class="don-hstat-lbl">animales ayudados</span>
        </div>
      </div>
    </div>
    <div class="don-hero-img-wrap">
      <img class="don-hero-img"
           src="https://images.unsplash.com/photo-1601758125946-6ec2ef64daf8?w=600&q=80"
           alt="Animales felices"
           onerror="this.src='https://picsum.photos/seed/pets99/600/450'">
    </div>
  </div>

  {{-- GRID PRINCIPAL --}}
  <div class="don-grid">

    {{-- COLUMNA IZQUIERDA: FORMULARIO --}}
    <div class="don-form-col">

      {{-- SELECTOR DE CANTIDAD --}}
      <div class="don-card" id="don-form-card">
        <div class="don-card-head">
          <i class="fa-solid fa-hand-holding-heart" style="color:var(--terra)"></i>
          <h2>Elige tu aportación</h2>
        </div>

        {{-- Chips de cantidad predefinida --}}
        <div class="don-amount-chips">
          <button class="don-chip" data-val="3">3 €</button>
          <button class="don-chip active" data-val="5">5 €</button>
          <button class="don-chip" data-val="10">10 €</button>
          <button class="don-chip" data-val="20">20 €</button>
          <button class="don-chip" data-val="50">50 €</button>
        </div>

        {{-- Input personalizado --}}
        <div class="don-custom-wrap">
          <label class="don-custom-label" for="amount">O introduce una cantidad personalizada</label>
          <div class="don-custom-input-wrap">
            <span class="don-euro">€</span>
            <input class="don-amount-input" type="number" id="amount" value="5" min="1" max="9999" step="1">
          </div>
        </div>

        {{-- Resumen --}}
        <div class="don-summary">
          <span>Vas a donar</span>
          <span class="don-summary-val" id="don-summary-val">5,00 €</span>
        </div>

        {{-- Botón PayPal --}}
        <div class="don-paypal-wrap">
          <div class="don-paypal-label">
            <i class="fa-solid fa-lock"></i> Pago seguro gestionado por PayPal
          </div>
          <div id="paypal-button"></div>
        </div>

        {{-- Flash de éxito --}}
        @if(session('donation_success'))
        <div class="don-success-banner" id="don-success">
          <i class="fa-solid fa-circle-check"></i>
          <div>
            <strong>¡Gracias por tu donación!</strong>
            <p>Tu aportación de {{ session('donation_amount') }} € ha sido registrada correctamente. ❤️</p>
          </div>
        </div>
        @endif
      </div>

      {{-- USOS DEL DINERO --}}
      <div class="don-card don-card--soft">
        <div class="don-card-head">
          <i class="fa-solid fa-circle-info" style="color:var(--terra)"></i>
          <h2>¿A qué va destinado?</h2>
        </div>
        <div class="don-uses">
          <div class="don-use-item">
            <div class="don-use-ico">🏥</div>
            <div>
              <div class="don-use-title">Gastos veterinarios</div>
              <div class="don-use-desc">Operaciones, vacunas y tratamientos de animales rescatados.</div>
            </div>
          </div>
          <div class="don-use-item">
            <div class="don-use-ico">🍖</div>
            <div>
              <div class="don-use-title">Alimentación</div>
              <div class="don-use-desc">Pienso y suministros para colonias y protectoras sin recursos.</div>
            </div>
          </div>
          <div class="don-use-item">
            <div class="don-use-ico">💻</div>
            <div>
              <div class="don-use-title">Plataforma</div>
              <div class="don-use-desc">Servidores y desarrollo para que PatitasUnidas siga creciendo.</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- COLUMNA DERECHA: HISTORIAL --}}
    <div class="don-history-col">

      {{-- ÚLTIMAS DONACIONES (globales, anonimizadas) --}}
      <div class="don-card">
        <div class="don-card-head">
          <i class="fa-solid fa-clock-rotate-left" style="color:var(--terra)"></i>
          <h2>Últimas donaciones</h2>
        </div>

        @if(isset($recentDonations) && $recentDonations->count())
          <div class="don-feed">
            @foreach($recentDonations as $d)
            <div class="don-feed-item">
              <div class="don-feed-av">
                {{ strtoupper(substr($d->user->nombre ?? 'A', 0, 1)) }}
              </div>
              <div class="don-feed-info">
                <span class="don-feed-name">
                  {{ $d->user->nombre ?? 'Anónimo' }}
                </span>
                <span class="don-feed-time">
                  {{ $d->created_at->diffForHumans() }}
                </span>
              </div>
              <div class="don-feed-amount">+{{ number_format($d->amount, 2) }} €</div>
            </div>
            @endforeach
          </div>
        @else
          <div class="don-empty">
            <div class="don-empty-ico">🐾</div>
            <p>Todavía no hay donaciones registradas.</p>
            <p style="font-size:.8rem;">¡Sé el primero en ayudar!</p>
          </div>
        @endif
      </div>

      {{-- MIS DONACIONES (si está autenticado) --}}
      @auth
      <div class="don-card">
        <div class="don-card-head">
          <i class="fa-solid fa-receipt" style="color:var(--terra)"></i>
          <h2>Mis donaciones</h2>
        </div>

        @if(isset($myDonations) && $myDonations->count())
          <div class="don-my-table-wrap">
            <table class="don-my-table">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Cantidad</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                @foreach($myDonations as $d)
                <tr>
                  <td>{{ $d->created_at->format('d/m/Y') }}</td>
                  <td class="don-td-amount">{{ number_format($d->amount, 2) }} €</td>
                  <td>
                    <span class="don-status don-status--{{ $d->status }}">
                      {{ $d->status === 'completed' ? '✓ Completada' : ucfirst($d->status) }}
                    </span>
                  </td>
                </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <td><strong>Total aportado</strong></td>
                  <td class="don-td-amount don-td-total" colspan="2">
                    {{ number_format($myDonations->sum('amount'), 2) }} €
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        @else
          <div class="don-empty">
            <div class="don-empty-ico">🐾</div>
            <p>Todavía no has realizado ninguna donación.</p>
            <p style="font-size:.8rem;">¡Sé el primero en ayudar!</p>
          </div>
        @endif
      </div>
      @endauth

    </div>
    {{-- /COLUMNA DERECHA --}}

  </div>
  {{-- /GRID --}}

</div>

{{-- SDK de PayPal (sandbox) --}}
<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=EUR"></script>

{{-- Donaciones logic --}}
<script src="{{ asset('js/donations.js') }}"></script>
@endsection