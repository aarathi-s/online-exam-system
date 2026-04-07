<x-guest-layout>
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Outfit:wght@300;400;500;600&display=swap');

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Outfit', sans-serif;
        background: #0a0c10;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-wrapper {
        width: 100%;
        max-width: 440px;
        padding: 2rem;
    }

    .brand-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .brand-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #1a1f2e, #232a3d);
        border: 1px solid #2a3350;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        position: relative;
    }

    .brand-icon svg {
        width: 26px;
        height: 26px;
        color: #4f8ef7;
    }

    .brand-icon::after {
        content: '';
        position: absolute;
        inset: -1px;
        border-radius: 14px;
        background: linear-gradient(135deg, #4f8ef740, transparent 60%);
        pointer-events: none;
    }

    .brand-title {
        font-size: 1.35rem;
        font-weight: 600;
        color: #e8ecf4;
        letter-spacing: -0.02em;
    }

    .brand-subtitle {
        font-size: 0.78rem;
        color: #4a5568;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        margin-top: 0.3rem;
        font-family: 'DM Mono', monospace;
    }

    .login-card {
        background: #0f1117;
        border: 1px solid #1e2436;
        border-radius: 18px;
        padding: 2.2rem 2.2rem 2rem;
        position: relative;
        overflow: hidden;
    }

    .login-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, #4f8ef730, #4f8ef760, #4f8ef730, transparent);
    }

    .card-heading {
        font-size: 1.05rem;
        font-weight: 500;
        color: #c5cede;
        margin-bottom: 1.8rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        font-size: 0.78rem;
        font-weight: 500;
        color: #6b7a99;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        font-family: 'DM Mono', monospace;
    }

    .form-input {
        width: 100%;
        background: #080a0f;
        border: 1px solid #1e2638;
        border-radius: 10px;
        padding: 0.72rem 1rem;
        color: #d1d9ee;
        font-size: 0.92rem;
        font-family: 'Outfit', sans-serif;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
    }

    .form-input:focus {
        border-color: #4f8ef7;
        box-shadow: 0 0 0 3px #4f8ef715;
    }

    .form-input::placeholder {
        color: #2d3650;
    }

    .form-input:-webkit-autofill,
    .form-input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0 100px #080a0f inset;
        -webkit-text-fill-color: #d1d9ee;
    }

    .form-error {
        font-size: 0.78rem;
        color: #f87171;
        margin-top: 0.4rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .remember-row {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-top: 1.4rem;
    }

    .remember-row input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #4f8ef7;
        border-radius: 4px;
        cursor: pointer;
    }

    .remember-row label {
        font-size: 0.85rem;
        color: #5a6680;
        cursor: pointer;
    }

    .divider {
        height: 1px;
        background: #1a2030;
        margin: 1.6rem 0;
    }

    .bottom-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .forgot-link {
        font-size: 0.82rem;
        color: #4a5a80;
        text-decoration: none;
        transition: color 0.2s;
        font-family: 'DM Mono', monospace;
    }

    .forgot-link:hover {
        color: #4f8ef7;
    }

    .submit-btn {
        background: #4f8ef7;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 0.72rem 1.6rem;
        font-size: 0.9rem;
        font-weight: 500;
        font-family: 'Outfit', sans-serif;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background 0.2s, transform 0.1s;
        letter-spacing: 0.01em;
    }

    .submit-btn:hover {
        background: #3d7de8;
    }

    .submit-btn:active {
        transform: scale(0.98);
    }

    .submit-btn svg {
        width: 15px;
        height: 15px;
    }

    .status-banner {
        background: #0c2a1a;
        border: 1px solid #1a4d30;
        border-radius: 10px;
        padding: 0.7rem 1rem;
        font-size: 0.84rem;
        color: #4ade80;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-family: 'DM Mono', monospace;
    }

    .footer-note {
        text-align: center;
        margin-top: 1.8rem;
        font-size: 0.75rem;
        color: #2a3148;
        font-family: 'DM Mono', monospace;
        letter-spacing: 0.04em;
    }

    .footer-note span {
        color: #3d4f6e;
    }
</style>

<!-- Session Status -->
@if(session('status'))
    <div class="status-banner">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:14px;height:14px;flex-shrink:0">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('status') }}
    </div>
@endif

<div class="login-wrapper">
    <div class="brand-header">
        <div class="brand-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
            </svg>
        </div>
        <div class="brand-title">ExamGuard</div>
        <div class="brand-subtitle">Integrity Monitoring System</div>
    </div>

    <div class="login-card">
        <div class="card-heading">Sign in to your account</div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input
                    id="email"
                    class="form-input"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="you@institution.edu"
                    required
                    autofocus
                    autocomplete="username"
                />
                @error('email')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" style="width:13px;height:13px;flex-shrink:0">
                            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm.75-10.25a.75.75 0 0 0-1.5 0v4.5a.75.75 0 0 0 1.5 0v-4.5zm-.75 7a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input
                    id="password"
                    class="form-input"
                    type="password"
                    name="password"
                    placeholder="••••••••••••"
                    required
                    autocomplete="current-password"
                />
                @error('password')
                    <div class="form-error">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" style="width:13px;height:13px;flex-shrink:0">
                            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm.75-10.25a.75.75 0 0 0-1.5 0v4.5a.75.75 0 0 0 1.5 0v-4.5zm-.75 7a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="remember-row">
                <input id="remember_me" type="checkbox" name="remember">
                <label for="remember_me">{{ __('Keep me signed in') }}</label>
            </div>

            <div class="divider"></div>

            <div class="bottom-row">
                @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif

                <button type="submit" class="submit-btn">
                    {{ __('Sign In') }}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <div class="footer-note">
        <span>SECURED &middot; MONITORED &middot; PROTECTED</span>
    </div>
</div>

</x-guest-layout>