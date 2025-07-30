<!DOCTYPE html>
<html>
<head>
    <title>Courier Real-Time Orders</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.3/dist/echo.iife.js"></script>
</head>
<body>
    <h1>Courier Real-Time Notifications</h1>
    <pre id="output"></pre>

    <script>
        Pusher.logToConsole = true;

        const courierId = {{ auth()->user()->id }};
        const token = '{{ auth()->user()->currentAccessToken()->plainTextToken ?? '' }}';

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '36aecfe536488a5d12d8', // Your Pusher key
            cluster: 'eu',              // Your Pusher cluster
            forceTLS: true,
            encrypted: true,
            authEndpoint: 'https://zee.zynk-adv.com/broadcasting/auth',
            auth: {
                headers: {
                    Authorization: 'Bearer {{ $token }}',
                    Accept: 'application/json',
                }
            }
        });

        Echo.private(`courier.${courierId}`)
            .listen('.order.assigned', (e) => {
                document.getElementById('output').textContent += '📦 Order Assigned: ' + JSON.stringify(e, null, 2) + "\n";
            });
    </script>
</body>
</html>
