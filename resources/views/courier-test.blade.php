<!DOCTYPE html>
<html>
<head>
    <title>courier-test.blade</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial; max-width: 900px; margin: 24px auto; padding: 0 16px; }
        .wrap { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .card { padding: 16px; border: 1px solid #eee; border-radius: 12px; }
        .muted { color:#666; font-size: 13px; }
        .ok { color: #0a7; }
        .err { color: #c22; }
        ul { padding-left: 16px; }
        code { background: #f7f7f7; padding: 2px 4px; border-radius: 6px; }
        button { padding: 8px 12px; cursor: pointer; }
    </style>
</head>
<body>
    <h2>courier-test.blade — Subscribe & receive events</h2>

    <div class="wrap">
        <div class="card">
            <h3>Connection</h3>
            <p class="muted">Reads <code>courier_token</code> and <code>courier_id</code> from <code>localStorage</code> (set by <b>test-pusher.blade</b>), then subscribes to <code>private</code> channel <code>courier.{id}</code>.</p>
            <p><b>Courier ID:</b> <span id="courierIdView">—</span></p>
            <p><b>Status:</b> <span id="connStatus">Not connected</span></p>
            <button id="connectBtn">Connect now</button>
            <button id="clearBtn">Clear logs</button>
            <div class="muted" style="margin-top:8px">Tip: open DevTools Console for Pusher logs.</div>
        </div>

        <div class="card">
            <h3>Incoming Events</h3>
            <ul id="events"></ul>
        </div>
    </div>

    <!-- Pusher + Echo (CDN) -->
    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script>
    // Verbose logs for troubleshooting
    Pusher.logToConsole = true;

    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const courierId = localStorage.getItem('courier_id');
    const token = localStorage.getItem('courier_token');

    document.getElementById('courierIdView').textContent = courierId || '—';

    const connStatus = document.getElementById('connStatus');
    const eventsEl = document.getElementById('events');

    function logEvent(text, cls) {
        const li = document.createElement('li');
        if (cls) li.className = cls;
        li.textContent = text;
        eventsEl.prepend(li);
    }

    function ensurePrereqs() {
        if (!token) { logEvent('Missing courier_token in localStorage. Go to /test-pusher first.', 'err'); return false; }
        if (!courierId) { logEvent('Missing courier_id in localStorage. Go to /test-pusher first.', 'err'); return false; }
        return true;
    }

    document.getElementById('connectBtn').addEventListener('click', () => {
        if (!ensurePrereqs()) return;

        connStatus.textContent = 'Connecting...';

        // Required by Echo
        window.Pusher = Pusher;

        // Use a custom authorizer so we can send the Bearer token without extra libs
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: "{{ env('PUSHER_APP_KEY') }}",
            cluster: "{{ env('PUSHER_APP_CLUSTER', 'mt1') }}",
            forceTLS: true,
            enabledTransports: ['ws', 'wss'],
            // We explicitly call the auth endpoint and pass the Bearer token
            authorizer: (channel, options) => {
                return {
                    authorize: (socketId, callback) => {
                        fetch('/broadcasting/auth', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Authorization': 'Bearer ' + token
                            },
                            body: JSON.stringify({
                                socket_id: socketId,
                                channel_name: channel.name
                            })
                        })
                        .then(async (r) => {
                            if (!r.ok) throw new Error('Auth ' + r.status);
                            const data = await r.json();
                            callback(false, data);
                        })
                        .catch((err) => {
                            console.error('AUTH_ERROR', err);
                            callback(true, err);
                        });
                    }
                };
            }
        });

        // Helpful connection lifecycle logs
        const p = window.Echo.connector.pusher;
        p.connection.bind('connected', () => { connStatus.textContent = 'Connected'; logEvent('Pusher connected', 'ok'); });
        p.connection.bind('unavailable', () => { connStatus.textContent = 'Unavailable'; logEvent('Pusher unavailable (network)', 'err'); });
        p.connection.bind('failed', () => { connStatus.textContent = 'Failed'; logEvent('Pusher failed to connect', 'err'); });
        p.connection.bind('disconnected', () => { connStatus.textContent = 'Disconnected'; logEvent('Pusher disconnected', 'err'); });

     <!-- ... باقي الكود كما هو ... -->

// Subscribe to the courier's public channel
const channelName = `courier_zee`;

window.Echo.channel(channelName) // قناة عامة
    .listen('.order.assigned', (e) => {
        logEvent(`order.assigned → track:${e.track_number} | status:${e.status}`, 'ok');
        console.log('EVENT_PAYLOAD', e);
    })
    .error((err) => {
        logEvent('Channel subscription error', 'err');
        console.error('CHANNEL_ERROR', err);
    });

    }); // ← قفل الـ connectBtn event listener هنا

document.getElementById('clearBtn').addEventListener('click', () => {
    eventsEl.innerHTML = '';
});

</script>
</body>
</html>
