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
    <h3>Latest Assigned Order:</h3>
    <pre id="output">Waiting for assigned order...</pre>

    <script>
        Pusher.logToConsole = true;

        const courierId = 12; // Replace with the actual courier ID
        const token = '37|GxpJf0oWJ5MJC1TNXnMrkInUFe9blPrpJjsuONfZe3c3b155'; // Sanctum token

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

        Echo.private(`courier.${courierId}`)
            .listen('.order.assigned', (e) => {
                document.getElementById('output').textContent = "📬 New Order Assigned! Fetching...\n";

                setTimeout(() => {
                    fetch('https://zee.zynk-adv.com/api/courier/orders', {
                        headers: {
                            Authorization: 'Bearer ' + token,
                            Accept: 'application/json',
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        const order = data.order;
                        if (order) {
                            document.getElementById('output').textContent =
                                `✅ Order Assigned:\n\n` +
                                `🆔 ID: ${order.id}\n` +
                                `📦 Track #: ${order.track_number}\n` +
                                `🚚 Status: ${order.status}\n` +
                                `📅 Created: ${order.created_at}`;
                        } else {
                            document.getElementById('output').textContent = "⚠️ No orders found.";
                        }
                    })
                    .catch(err => {
                        document.getElementById('output').textContent = "❌ Error fetching order:\n" + err;
                    });
                }, 300);
            });
    </script>
</body>
</html>
