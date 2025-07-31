<!DOCTYPE html>
<html>
<head>
    <title>Courier Real-Time Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.3/dist/echo.iife.js"></script>
</head>
<body>
    <h1>📦 Courier Real-Time Notifications</h1>
    <h3>Latest Orders:</h3>
    <pre id="output">Waiting for assigned order...</pre>

    <script>
        Pusher.logToConsole = true;

        const courierId = 12; // replace with actual logged-in courier ID
        const token = '37|GxpJf0oWJ5MJC1TNXnMrkInUFe9blPrpJjsuONfZe3c3b155'; // real token

        // Initialize Echo
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '36aecfe536488a5d12d8',
            cluster: 'eu',
            forceTLS: true,
            authEndpoint: 'https://zee.zynk-adv.com/broadcasting/auth',
            auth: {
                headers: {
                    Authorization: 'Bearer ' + token,
                    Accept: 'application/json',
                }
            }
        });

        // Listen to order assigned event on private channel
        E<!DOCTYPE html>
<html>
<head>
    <title>Courier Real-Time Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.3/dist/echo.iife.js"></script>
</head>
<body>
    <h1>📦 Courier Real-Time Notifications</h1>
    <h3>Latest Orders:</h3>
    <pre id="output">Waiting for assigned order...</pre>

    <script>
        Pusher.logToConsole = true;

        const courierId = 12;
        const token = '37|GxpJf0oWJ5MJC1TNXnMrkInUFe9blPrpJjsuONfZe3c3b155';

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '36aecfe536488a5d12d8',
            cluster: 'eu',
            forceTLS: true,
            authEndpoint: 'https://zee.zynk-adv.com/broadcasting/auth',
            auth: {
                headers: {
                    Authorization: 'Bearer ' + token,
                    Accept: 'application/json',
                }
            }
        });

        // 🛠️ FIXED: was "cho.private(...)", now "Echo.private(...)"
        Echo.private(`courier.${courierId}`)
            .listen('.order.assigned', (e) => {
                document.getElementById('output').textContent = "📬 New Order Assigned!\nFetching latest orders...";

                // Delay slightly to ensure DB has saved the new order
                setTimeout(() => {
                    fetch('https://zee.zynk-adv.com/api/courier/orders', {
                        headers: {
                            Authorization: 'Bearer ' + token,
                            Accept: 'application/json',
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('output').textContent = "📦 Latest Orders:\n\n" + JSON.stringify(data, null, 2);
                        console.log('Latest orders:', data);
                    })
                    .catch(err => {
                        document.getElementById('output').textContent = "❌ Error fetching orders:\n" + err;
                        console.error('Error fetching orders:', err);
                    });
                }, 300); // small delay after event
            });
    </script>
</body>
</html>
