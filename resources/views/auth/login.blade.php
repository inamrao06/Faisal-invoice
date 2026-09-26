<!doctype html>
<html lang="en">
@php
    $loginSettings = \App\Models\Setting::pluck('value', 'key');
    $siteName = $loginSettings['site_name'] ?? $loginSettings['business_name'] ?? 'Car Business';
    $logoPath = $loginSettings['logo_path'] ?? null;
@endphp
<head>
    <meta charset="utf-8">
    <title>Sign In | {{ $siteName }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sign in to {{ $siteName }}">
    <link rel="shortcut icon" href="{{ asset('paces/assets/images/favicon.ico') }}">
    <script src="{{ asset('paces/assets/js/config.js') }}"></script>
    <link href="{{ asset('paces/assets/css/vendors.min.css') }}" rel="stylesheet">
    <link id="app-style" href="{{ asset('paces/assets/css/app.min.css') }}" rel="stylesheet">
    <style>
        .auth-app-logo { display:inline-flex;align-items:center;justify-content:center;max-width:220px;min-height:48px }
        .auth-app-logo img { display:block;max-width:200px;max-height:58px;object-fit:contain }
        .auth-app-logo span { color:var(--bs-heading-color);font-size:27px;font-weight:800 }
        .auth-card-bg-img { pointer-events:none }
    </style>
</head>
<body>
    <div class="position-absolute top-0 end-0">
        <img src="{{ asset('paces/assets/images/auth-card-bg.svg') }}" class="auth-card-bg-img" alt="">
    </div>
    <div class="position-absolute bottom-0 start-0" style="transform:rotate(180deg)">
        <img src="{{ asset('paces/assets/images/auth-card-bg.svg') }}" class="auth-card-bg-img" alt="">
    </div>

    <main class="auth-box overflow-hidden align-items-center d-flex">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-lg-6 col-md-7 col-sm-9">
                    <div class="card p-4">
                        <div class="auth-brand text-center mb-4">
                            <a href="{{ route('login') }}" class="auth-app-logo">
                                @if($logoPath)
                                    <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $siteName }}">
                                @else
                                    <span>{{ $siteName }}</span>
                                @endif
                            </a>
                            <h4 class="fw-bold mt-3">Welcome back</h4>
                            <p class="text-muted w-lg-75 mx-auto mb-0">Sign in with your email and password to continue.</p>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger py-2" role="alert">
                                <i class="ti ti-alert-circle me-1"></i>{{ $errors->first() }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="userEmail" class="form-label">Email address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="userEmail" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="userPassword" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                    <input type="password" class="form-control" id="userPassword" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                                    <button class="btn btn-outline-secondary" id="togglePassword" type="button" aria-label="Show password" title="Show password"><i class="ti ti-eye"></i></button>
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="remember" value="1" id="rememberMe" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="rememberMe">Keep me signed in</label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary fw-semibold py-2"><i class="ti ti-login me-1"></i> Sign In</button>
                            </div>
                        </form>
                    </div>
                    <p class="text-center text-muted mt-4 mb-0">&copy; {{ date('Y') }} <span class="fw-semibold">{{ $siteName }}</span></p>
                </div>
            </div>
        </div>
    </main>

    <script src="{{ asset('paces/assets/js/vendors.min.js') }}"></script>
    <script src="{{ asset('paces/assets/js/app.js') }}"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('userPassword');
            const visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            this.innerHTML = visible ? '<i class="ti ti-eye"></i>' : '<i class="ti ti-eye-off"></i>';
            this.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
        });
    </script>
</body>
</html>
