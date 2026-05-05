<meta name="csrf-token" content="{{ csrf_token() }}">

<h2>Apoya el proyecto ❤️</h2>

<input type="number" id="amount" value="5" min="1">

<div id="paypal-button"></div>

<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=EUR"></script>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

paypal.Buttons({
    createOrder: function () {
        const amount = document.getElementById('amount').value;

        return fetch('/donations/create-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({ amount })
        })
        .then(res => res.json())
        .then(data => data.id);
    },

    onApprove: function (data) {
        return fetch('/donations/capture-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({ orderID: data.orderID })
        })
        .then(res => res.json())
        .then(() => {
            alert('Donacion completada ❤️');
            location.reload();
        });
    }
}).render('#paypal-button');
</script>