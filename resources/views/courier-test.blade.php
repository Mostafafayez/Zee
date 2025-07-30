<!DOCTYPE html>
<html>
<head>
    <title>Courier Real-Time Orders</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.3/dist/echo.iife.js"></script>
</head>
<body>
    <h1>Courier Order Notifications</h1>
    <pre id="output"></pre>

    <script>
        Pusher.logToConsole = true;

        // Replace with actual values
        const courierId = {{ auth()->user()->id }};
        const token = '{{ auth()->user()->currentAccessToken()->plainTextToken ?? '' }}';

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ config("broadcasting.connections.pusher.key") }}',
            cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
            forceTLS: true,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    Authorization: 'Bearer {{ auth()->user()->currentAccessToken()->plainTextToken ?? '' }}',
                    Accept: 'application/json',
                }
            }
        });

        Echo.private(`courier.${courierId}`)
            .listen('.order.assigned', (e) => {
                document.getElementById('output').textContent += 'Order Assigned: ' + JSON.stringify(e) + "\n";
            });
    </script>
</body>
</html>
