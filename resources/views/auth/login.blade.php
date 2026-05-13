<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SIMRS Khanza">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <title>SIMRS Khanza - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        });
    }
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-blue-100 dark:from-gray-900 dark:via-blue-900 dark:to-gray-900 min-h-screen flex items-center justify-center p-4">
    <div x-data="loginForm()" class="w-full max-w-md">
        <div class="rounded-2xl shadow-2xl p-8 border" style="background-color:color-mix(in srgb, var(--bg-elevated) 80%, transparent);border-color:var(--border)">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/><circle cx="12" cy="12" r="10"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold" style="color:var(--text-primary)">SIMRS Khanza</h1>
                <p class="text-sm mt-1" style="color:var(--text-muted)">Hospital Management System</p>
            </div>

            <form @submit.prevent="doLogin" class="space-y-4">
                <div>
                    <label class="form-label">ID User</label>
                    <input type="text" x-model="id_user" class="form-input" placeholder="admin" required autocomplete="off">
                </div>
                <div>
                    <label class="form-label">Password</label>
                    <input type="password" x-model="password" class="form-input" placeholder="********" required>
                </div>

                <template x-if="error">
                    <div class="rounded px-3 py-2 text-sm" style="background-color:rgba(220,38,38,0.15);border:1px solid rgba(220,38,38,0.3);color:var(--accent-red)" x-text="error"></div>
                </template>

                <button type="submit" class="btn btn-primary w-full py-2.5" x-text="loading ? 'Loading...' : 'Sign In'"></button>
            </form>
        </div>
        <p class="text-center text-xs mt-4" style="color:var(--text-muted)">SIMRS Khanza Web Edition v1.0</p>
    </div>

    <script>
    function loginForm() {
        return {
            id_user: 'admin',
            password: 'admin123',
            loading: false,
            error: null,
            async doLogin() {
                this.loading = true;
                this.error = null;
                try {
                    await this.$store.auth.login(this.id_user, this.password);
                    window.location.href = '/dashboard';
                } catch (e) {
                    this.error = e.message || 'Login failed';
                } finally {
                    this.loading = false;
                }
            }
        };
    }
    </script>
</body>
</html>
