<!DOCTYPE html>
<html>
<head>
    <title>Courier Real-Time Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.3/dist/echo.iife.js"></script>
</head>
<body>
    <h1>Courier Real-Time Notifications</h1>
    <pre id="output"></pre>

    <script>
        Pusher.logToConsole = true;

        const courierId = 12; // ← Replace with actual courier user_id
        const token = '36|B5RfdmofhNYlxzFFf3Kx41Nf2kbd0QrJynuGmaJc76215adf';

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '36aecfe536488a5d12d8',
            cluster: 'eu',
            forceTLS: true,
            encrypted: true,
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
                document.getElementById('output').textContent += '📦 Order Assigned:\n' + JSON.stringify(e, null, 2) + "\n\n";
            });
    </script>
</body>
</html>
