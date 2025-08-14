<!DOCTYPE html>
<html>
<head>
    <title>test-pusher.blade</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial; max-width: 720px; margin: 24px auto; padding: 0 16px; }
        input { padding: 8px; margin: 6px 0; width: 100%; box-sizing: border-box; }
        button { padding: 10px 14px; cursor: pointer; }
        .card { padding: 16px; border: 1px solid #eee; border-radius: 12px; margin-top: 12px; }
        .muted { color:#666; font-size: 13px; }
        .ok { color: #0a7; }
        .err { color: #c22; }
    </style>
</head>
<body>
    <h2>test-pusher.blade — Login & store token</h2>

    <div class="card">
        <label>phone</label>
        <input id="phone" type="phone" value="01152680012" autocomplete="username">

        <label>Password</label>
        <input id="password" type="password" value="12345678" autocomplete="current-password">

        <button id="loginBtn">Login & Go to courier-test</button>
        <div id="status" class="muted"></div>
    </div>

    <div class="card">
        <p class="muted">This page will call <code>/api/login</code>, then save <code>courier_token</code> and <code>courier_id</code> to <code>localStorage</code>, and redirect to <code>/courier-test</code>.</p>
    </div>

<script>
document.getElementById('loginBtn').addEventListener('click', async () => {
    const status = document.getElementById('status');
    status.textContent = 'Logging in...';

    try {
        const res = await fetch('/api/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                phone: document.getElementById('phone').value,
                password: document.getElementById('password').value
            })
        });

        const data = await res.json();

        if (!res.ok) {
            status.innerHTML = `<span class="err">Login failed (${res.status}). Check your /api/login route.</span>`;
            console.error('LOGIN_ERROR_RESPONSE', data);
            return;
        }

        if (!data.token || !data.user || !data.user.id) {
            status.innerHTML = `<span class="err">Unexpected /api/login response. Expecting { token, user:{ id } }.</span>`;
            console.error('LOGIN_BAD_SHAPE', data);
            return;
        }

        localStorage.setItem('courier_token', data.token);
        localStorage.setItem('courier_id', String(data.user.id));

        status.innerHTML = `<span class="ok">Logged in as courier #${data.user.id}. Redirecting...</span>`;
        window.location.href = '/courier-test';
    } catch (e) {
        status.innerHTML = `<span class="err">Login request failed (network/JS error).</span>`;
        console.error('LOGIN_REQUEST_FAILED', e);
    }
});
</script>
</body>
</html>
