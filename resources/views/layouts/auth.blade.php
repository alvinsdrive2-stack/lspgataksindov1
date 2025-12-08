<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="description" content="Login - Sistem Verifikasi TUK - LSP LPK Gataksindo" />

        <title>@yield('title', 'Login') - LSP LPK Gataksindo</title>
        @vite('resources/css/app.css')

        <style>
            /* Background animation */
            @keyframes gradient {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            .animated-bg {
                background: linear-gradient(-45deg, #1F3A73, #3F5FA8, #0F1A36, #1F3A73);
                background-size: 400% 400%;
                animation: gradient 15s ease infinite;
            }

            /* Floating shapes animation */
            @keyframes float {
                0%, 100% { transform: translateY(0px) rotate(0deg); }
                33% { transform: translateY(-20px) rotate(120deg); }
                66% { transform: translateY(20px) rotate(240deg); }
            }

            .float-animation {
                animation: float 6s ease-in-out infinite;
            }

            /* Form animations */
            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .slide-in-up {
                animation: slideInUp 0.6s ease-out forwards;
            }

            .slide-in-delay-1 { animation-delay: 0.1s; opacity: 0; animation-fill-mode: forwards; }
            .slide-in-delay-2 { animation-delay: 0.2s; opacity: 0; animation-fill-mode: forwards; }
            .slide-in-delay-3 { animation-delay: 0.3s; opacity: 0; animation-fill-mode: forwards; }
            .slide-in-delay-4 { animation-delay: 0.4s; opacity: 0; animation-fill-mode: forwards; }

            /* Glassmorphism effect */
            .glass {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                box-shadow: 0 8px 32px 0 rgba(31, 58, 115, 0.37);
            }

            /* Custom input styles */
            .custom-input {
                background: rgba(255, 255, 255, 0.9);
                border: 2px solid transparent;
                transition: all 0.3s ease;
            }

            .custom-input:focus {
                background: white;
                border-color: #1F3A73;
                box-shadow: 0 0 0 4px rgba(31, 58, 115, 0.1);
            }

            /* Button animations */
            .btn-primary {
                background: linear-gradient(135deg, #1F3A73 0%, #3F5FA8 100%);
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .btn-primary::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transition: left 0.5s;
            }

            .btn-primary:hover::before {
                left: 100%;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(31, 58, 115, 0.3);
            }

            /* Role badge animations */
            .role-badge {
                animation: slideInUp 0.6s ease-out forwards;
                animation-delay: 0.5s;
                opacity: 0;
            }

            /* Back link animation */
            .back-link {
                position: relative;
                transition: all 0.3s ease;
            }

            .back-link::after {
                content: '';
                position: absolute;
                bottom: -2px;
                left: 0;
                width: 0;
                height: 2px;
                background: white;
                transition: width 0.3s ease;
            }

            .back-link:hover::after {
                width: 100%;
            }

            /* Error shake animation */
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                20%, 40%, 60%, 80% { transform: translateX(5px); }
            }

            .shake {
                animation: shake 0.5s ease-in-out;
            }
        </style>
    </head>
    <body class="animated-bg min-h-screen flex items-center justify-center relative overflow-hidden">
        <!-- Animated background shapes -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 left-20 w-72 h-72 bg-white/5 rounded-full blur-3xl float-animation"></div>
            <div class="absolute bottom-20 right-20 w-96 h-96 bg-white/5 rounded-full blur-3xl float-animation" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/3 w-80 h-80 bg-white/5 rounded-full blur-3xl float-animation" style="animation-delay: 4s;"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 w-full max-w-7xl mx-auto px-4 py-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Side - Branding -->
                <div class="text-center lg:text-left slide-in-up">
                    <div class="mb-8">
                        <img
                            src="/images/logo-banner.png"
                            alt="LSP LPK Gataksindo Logo"
                            class="w-32 h-32 lg:w-40 lg:h-40 mx-auto lg:mx-0 object-contain drop-shadow-2xl"
                        />
                    </div>
                    <h1 class="text-4xl lg:text-6xl font-black text-white mb-4">
                        SELAMAT DATANG
                    </h1>
                    <p class="text-xl lg:text-2xl text-blue-100 mb-6">
                        Sistem Verifikasi TUK
                    </p>
                    <p class="text-lg text-blue-200/80 max-w-lg mx-auto lg:mx-0">
                        Lembaga Sertifikasi Profesi LPK Gataksindo
                    </p>

                    @hasSection('roleBadge')
                        <div class="role-badge mt-8">
                            @yield('roleBadge')
                        </div>
                    @endif
                </div>

                <!-- Right Side - Login Form -->
                <div class="slide-in-delay-2">
                    <div class="glass rounded-3xl p-8 lg:p-12">
                        @yield('content')

                        <!-- Back to Home -->
                        <div class="mt-8 text-center slide-in-delay-4">
                            <a href="/" class="back-link inline-flex items-center text-white/80 hover:text-white transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transition Scripts -->
        <script>
            // Page transition effects
            document.addEventListener('DOMContentLoaded', function() {
                // Add entrance animations
                const elements = document.querySelectorAll('.slide-in-up, .slide-in-delay-1, .slide-in-delay-2, .slide-in-delay-3, .slide-in-delay-4');
                elements.forEach((el, index) => {
                    setTimeout(() => {
                        el.style.opacity = '1';
                    }, index * 100);
                });

                // Handle form submission with loading state
                const forms = document.querySelectorAll('form');
                forms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        const submitBtn = this.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = `
                                <span class="inline-flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memproses...
                                </span>
                            `;
                        }
                    });
                });

                // Add shake animation to error messages
                const errorAlerts = document.querySelectorAll('.alert-error');
                errorAlerts.forEach(alert => {
                    alert.classList.add('shake');
                    setTimeout(() => {
                        alert.classList.remove('shake');
                    }, 500);
                });
            });
        </script>
    </body>
</html>