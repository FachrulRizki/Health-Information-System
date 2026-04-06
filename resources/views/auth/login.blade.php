<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { font-family: 'Inter', sans-serif; }
        :root {
            --primary: #7B1D1D;
            --primary-dark: #5C1414;
            --gold: #D4A017;
        }
        .login-left {
            background: linear-gradient(160deg, #5C1414 0%, #7B1D1D 50%, #9B2C2C 100%);
        }
        .input-field { transition: all 0.2s ease; }
        .input-field:focus {
            outline: none;
            border-color: #7B1D1D;
            box-shadow: 0 0 0 3px rgba(123,29,29,0.12);
        }
        .btn-login {
            background: linear-gradient(135deg, #7B1D1D, #5C1414);
            transition: all 0.2s ease;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #5C1414, #3D0D0D);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(123,29,29,0.4);
        }
        @keyframes float {
            0%,100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-18px) rotate(3deg); }
        }
        @keyframes float2 {
            0%,100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(-2deg); }
        }
        .float-1 { animation: float 7s ease-in-out infinite; }
        .float-2 { animation: float2 9s ease-in-out infinite; }
        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(20px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .fade-in-right { animation: fadeInRight 0.5s ease forwards; }
    </style>
</head>
<body class="min-h-screen flex" style="background: #F9F5F5;">

    {{-- Left Panel: Branding (60%) --}}
    <div class="hidden lg:flex lg:w-3/5 login-left flex-col items-center justify-center p-12 relative overflow-hidden">

        {{-- Decorative circles --}}
        <div class="float-1 absolute top-12 right-20 w-40 h-40 rounded-full" style="background: rgba(212,160,23,0.08);"></div>
        <div class="float-2 absolute bottom-20 left-16 w-56 h-56 rounded-full" style="background: rgba(255,255,255,0.05);"></div>
        <div class="absolute top-1/2 right-8 w-20 h-20 rounded-full" style="background: rgba(212,160,23,0.06);"></div>
        <div class="absolute bottom-1/3 right-1/3 w-12 h-12 rounded-full" style="background: rgba(255,255,255,0.08);"></div>

        <div class="relative z-10 text-center max-w-md">
            {{-- Hospital icon --}}
            <div class="w-24 h-24 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-2xl"
                 style="background: rgba(212,160,23,0.2); border: 2px solid rgba(212,160,23,0.4);">
                <i class="fa-solid fa-hospital-user text-white text-4xl"></i>
            </div>

            <h1 class="text-4xl font-bold text-white mb-3 leading-tight">{{ config('app.name') }}</h1>
            <p class="text-base mb-2" style="color: rgba(212,160,23,0.9);">Rekam Medis Elektronik</p>
            <p class="text-sm leading-relaxed mb-10" style="color: rgba(255,255,255,0.65);">
                Sistem informasi kesehatan terintegrasi untuk pelayanan medis yang lebih baik, efisien, dan akurat.
            </p>

            {{-- Feature icons --}}
            <div class="grid grid-cols-3 gap-4">
                @foreach([
                    ['fa-stethoscope', 'Rawat Jalan'],
                    ['fa-bed-pulse', 'Rawat Inap'],
                    ['fa-pills', 'Farmasi'],
                    ['fa-flask', 'Laboratorium'],
                    ['fa-x-ray', 'Radiologi'],
                    ['fa-chart-line', 'Laporan'],
                ] as [$icon, $label])
                <div class="rounded-xl p-3 text-center" style="background: rgba(255,255,255,0.08);">
                    <i class="fa-solid {{ $icon }} text-xl mb-1.5 block" style="color: rgba(212,160,23,0.9);"></i>
                    <p class="text-xs" style="color: rgba(255,255,255,0.7);">{{ $label }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right Panel: Login Form (40%) --}}
    <div class="w-full lg:w-2/5 flex items-center justify-center p-6 sm:p-10" style="background: #fff;">
        <div class="w-full max-w-sm fade-in-right">

            {{-- Mobile logo --}}
            <div class="lg:hidden text-center mb-8">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg"
                     style="background: linear-gradient(135deg, #5C1414, #7B1D1D);">
                    <i class="fa-solid fa-hospital-user text-white text-2xl"></i>
                </div>
                <h1 class="text-xl font-bold" style="color: #1A0A0A;">{{ config('app.name') }}</h1>
                <p class="text-xs mt-1" style="color: #6B4C4C;">Rekam Medis Elektronik</p>
            </div>

            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-1" style="color: #1A0A0A;">Selamat datang</h2>
                <p class="text-sm" style="color: #6B4C4C;">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            @if (session('error'))
                <div class="flex items-start gap-3 px-4 py-3 rounded-xl mb-5 text-sm"
                     style="background: #FFF5F5; border: 1px solid #FEB2B2; color: #C53030;">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 flex-shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="username" class="block text-sm font-semibold mb-1.5" style="color: #1A0A0A;">Username</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color: #6B4C4C;">
                            <i class="fa-solid fa-user text-sm"></i>
                        </span>
                        <input type="text" id="username" name="username" value="{{ old('username') }}"
                               class="input-field w-full border rounded-xl pl-10 pr-4 py-3 text-sm
                                      @error('username') @else @enderror"
                               style="color: #1A0A0A; border-color: #E8D5D5; background: #FFF9F9;"
                               placeholder="Masukkan username"
                               required autofocus>
                    </div>
                    @error('username')
                        <p class="text-xs mt-1.5 flex items-center gap-1" style="color: #C53030;">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold mb-1.5" style="color: #1A0A0A;">Password</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color: #6B4C4C;">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input type="password" id="password" name="password"
                               class="input-field w-full border rounded-xl pl-10 pr-12 py-3 text-sm"
                               style="color: #1A0A0A; border-color: #E8D5D5; background: #FFF9F9;"
                               placeholder="Masukkan password"
                               required>
                        <button type="button" onclick="togglePassword()"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 transition-colors"
                                style="color: #6B4C4C;">
                            <i class="fa-solid fa-eye text-sm" id="pwd-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit"
                        class="btn-login w-full text-white font-semibold py-3 px-4 rounded-xl text-sm">
                    <i class="fa-solid fa-right-to-bracket mr-2"></i>
                    Masuk ke Sistem
                </button>
            </form>

            <p class="text-center text-xs mt-8" style="color: #6B4C4C;">
                &copy; {{ date('Y') }} {{ config('app.name') }}
            </p>
        </div>
    </div>

    <script>
    function togglePassword() {
        const pwd = document.getElementById('password');
        const eye = document.getElementById('pwd-eye');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            eye.className = 'fa-solid fa-eye-slash text-sm';
        } else {
            pwd.type = 'password';
            eye.className = 'fa-solid fa-eye text-sm';
        }
    }
    </script>
</body>
</html>
