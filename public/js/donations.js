// ============================================================================
// DONATIONS PAGE - JavaScript Logic
// ============================================================================

document.addEventListener('DOMContentLoaded', function() {
  initDonationForm();
  initPayPalButtons();
});

/**
 * Inicializar sincronización de chips y input de cantidad
 */
function initDonationForm() {
  const chips       = document.querySelectorAll('.don-chip');
  const amountInput = document.getElementById('amount');
  const summaryVal  = document.getElementById('don-summary-val');

  // Formatear a euros
  function fmtEur(n) {
    return parseFloat(n).toLocaleString('es-ES', { minimumFractionDigits: 2 }) + ' €';
  }

  // Sincronizar resumen
  function syncSummary() {
    summaryVal.textContent = fmtEur(amountInput.value || 0);
  }

  // Limpiar todos los chips
  function clearChips() {
    chips.forEach(c => c.classList.remove('active'));
  }

  // Listeners en chips
  chips.forEach(chip => {
    chip.addEventListener('click', () => {
      clearChips();
      chip.classList.add('active');
      amountInput.value = chip.dataset.val;
      syncSummary();
    });
  });

  // Listener en input personalizado
  amountInput.addEventListener('input', () => {
    clearChips();
    syncSummary();
  });

  // Sincronizar al cargar
  syncSummary();
}

/**
 * Inicializar botones de PayPal
 */
function initPayPalButtons() {
  if (typeof paypal === 'undefined') {
    console.error('PayPal SDK no cargado');
    return;
  }

  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  paypal.Buttons({
    style: {
      layout: 'vertical',
      color:  'gold',
      shape:  'pill',
      label:  'donate',
      height: 48
    },

    createOrder: function () {
      const amount = document.getElementById('amount').value;
      return fetch('/donations/create-order', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ amount })
      })
      .then(res => res.json())
      .then(data => {
        if (data.error) {
          throw new Error(data.error);
        }
        return data.id;
      });
    },

    onApprove: function (data) {
      return fetch('/donations/capture-order', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ orderID: data.orderID })
      })
      .then(res => res.json())
      .then(() => {
        if (typeof showToast === 'function') {
          showToast('¡Donación completada! Gracias por tu apoyo ❤️');
        }
        setTimeout(() => location.reload(), 1800);
      });
    },

    onError: function (err) {
      console.error('PayPal error', err);
      if (typeof showToast === 'function') {
        showToast('Algo fue mal con el pago. Inténtalo de nuevo.');
      }
    }
  }).render('#paypal-button');
}
