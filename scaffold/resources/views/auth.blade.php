<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login & Register | Laravel + Vue</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <style>
        :root { color-scheme: light; }
        body { font-family: Inter, Arial, sans-serif; background: #f5f7fb; margin: 0; }
        .wrapper { max-width: 920px; margin: 36px auto; padding: 0 16px; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; box-shadow: 0 6px 22px rgba(31,41,55,.08); overflow: hidden; }
        .header { padding: 18px 22px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; }
        .title { margin: 0; font-size: 20px; }
        .tabs { display: flex; gap: 10px; }
        .tab { border: 1px solid #d1d5db; background: #fff; color: #111827; padding: 8px 12px; border-radius: 8px; cursor: pointer; }
        .tab.active { background: #2563eb; color: #fff; border-color: #2563eb; }
        .body { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
        .pane { padding: 22px; }
        .pane + .pane { border-left: 1px solid #e5e7eb; background: #f9fafb; }
        .field { margin-bottom: 12px; }
        label { display: block; margin-bottom: 6px; font-size: 14px; color: #374151; }
        input { width: 100%; box-sizing: border-box; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; }
        .btn { background: #2563eb; color: #fff; border: 0; border-radius: 8px; padding: 10px 14px; cursor: pointer; }
        .btn.alt { background: #111827; }
        .muted { color: #6b7280; font-size: 14px; }
        .ok { color: #166534; }
        .error { color: #b91c1c; font-size: 14px; }
        @media (max-width: 800px) { .body { grid-template-columns: 1fr; } .pane + .pane { border-left: 0; border-top: 1px solid #e5e7eb; } }
    </style>
</head>
<body>
<div id="app" class="wrapper">
    <div class="card">
        <div class="header">
            <h1 class="title">Simple Vue Login / Register</h1>
            <div class="tabs">
                <button class="tab" :class="{ active: mode === 'login' }" @click="mode = 'login'">Login</button>
                <button class="tab" :class="{ active: mode === 'register' }" @click="mode = 'register'">Register</button>
            </div>
        </div>

        <div class="body">
            <div class="pane">
                <p class="muted" v-if="mode === 'login'">Sign in to test session-based authentication.</p>
                <p class="muted" v-else>Create an account and get automatically logged in.</p>

                <div v-if="mode === 'register'">
                    <div class="field">
                        <label>Name</label>
                        <input v-model="registerForm.name" type="text" placeholder="Jane Doe">
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input v-model="registerForm.email" type="email" placeholder="jane@example.com">
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <input v-model="registerForm.password" type="password" placeholder="Minimum 8 characters">
                    </div>
                    <div class="field">
                        <label>Confirm password</label>
                        <input v-model="registerForm.password_confirmation" type="password" placeholder="Repeat password">
                    </div>
                    <button class="btn" @click="submitRegister">Create account</button>
                </div>

                <div v-else>
                    <div class="field">
                        <label>Email</label>
                        <input v-model="loginForm.email" type="email" placeholder="jane@example.com">
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <input v-model="loginForm.password" type="password" placeholder="Your password">
                    </div>
                    <button class="btn" @click="submitLogin">Sign in</button>
                </div>

                <p class="ok" v-if="message">@{{ message }}</p>
                <p class="error" v-if="error">@{{ error }}</p>
            </div>

            <div class="pane">
                <h2 style="margin-top:0">Current Session</h2>
                <template v-if="user">
                    <p><strong>Name:</strong> @{{ user.name }}</p>
                    <p><strong>Email:</strong> @{{ user.email }}</p>
                    <button class="btn alt" @click="submitLogout">Logout</button>
                </template>
                <p class="muted" v-else>No authenticated user.</p>

                <hr style="margin:18px 0;border:0;border-top:1px solid #e5e7eb">
                <p class="muted">Tip: after deploying to Render, open this page and create an account to verify your app handles DB + sessions in production.</p>
            </div>
        </div>
    </div>
</div>

<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            mode: 'login',
            message: '',
            error: '',
            user: null,
            loginForm: { email: '', password: '' },
            registerForm: { name: '', email: '', password: '', password_confirmation: '' }
        }
    },
    async mounted() {
        await this.fetchUser();
    },
    methods: {
        csrf() {
            return '{{ csrf_token() }}';
        },
        async api(path, options = {}) {
            const response = await fetch(path, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrf(),
                },
                ...options,
            });

            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || Object.values(payload.errors || {}).flat().join(' ') || 'Request failed');
            }

            return payload;
        },
        async fetchUser() {
            const payload = await this.api('/api/user', { method: 'GET' });
            this.user = payload.user;
        },
        async submitLogin() {
            this.message = '';
            this.error = '';

            try {
                const payload = await this.api('/api/login', { method: 'POST', body: JSON.stringify(this.loginForm) });
                this.user = payload.user;
                this.message = payload.message;
            } catch (error) {
                this.error = error.message;
            }
        },
        async submitRegister() {
            this.message = '';
            this.error = '';

            try {
                const payload = await this.api('/api/register', { method: 'POST', body: JSON.stringify(this.registerForm) });
                this.user = payload.user;
                this.message = payload.message;
            } catch (error) {
                this.error = error.message;
            }
        },
        async submitLogout() {
            this.message = '';
            this.error = '';

            try {
                const payload = await this.api('/api/logout', { method: 'POST' });
                this.user = null;
                this.message = payload.message;
            } catch (error) {
                this.error = error.message;
            }
        }
    }
}).mount('#app');
</script>
</body>
</html>
