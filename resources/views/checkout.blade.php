<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Checkout</title>
</head>
<body>
    <h1>Demo Product — $10</h1>

    <form id="checkout-form">
        <label>Email (optional): <input type="email" name="email" id="email"></label>
        <button type="submit">Pay $10</button>
    </form>

    <script>
    document.getElementById('checkout-form').addEventListener('submit', async function(e){
        e.preventDefault();
        const email = document.getElementById('email').value;
        const res = await fetch("{{ route('stripe.create') }}", {
            method: 'POST',
            headers: {
                'Content-Type':'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ email })
        });
        const data = await res.json();
        if (data.url) {
            window.location = data.url; // redirect to Stripe Checkout
        } else {
            alert('Error creating session');
        }
    });
    </script>
</body>
</html>
