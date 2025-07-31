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
        Echo.private(`courier.${courierId}`)
            .listen('.order.assigned', (e) => {
                document.getElementById('output').textContent = "📬 New Order Assigned!\nFetching latest orders...";

                // Fetch the courier's latest orders
                    setTimeout(() => {
                    fetch('https://zee.zynk-adv.com/api/courier/orders', {
                        headers: {
                            Authorization: 'Bearer ' + token,
                            Accept: 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('output').textContent = "📦 Latest Orders:\n\n" + JSON.stringify(data, null, 2);
                    })
                    .catch(err => {
                        document.getElementById('output').textContent = "❌ Error fetching orders: " + err;
                    });
                }, 300); // wait 300ms after the event

                            });
    </script>
</body>
</html>
