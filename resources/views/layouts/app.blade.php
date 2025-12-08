<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="description" content="Sistem Verifikasi TUK - LSP LPK Gataksindo" />

        <title>@yield('title', 'Verifikasi TUK') - LSP LPK Gataksindo</title>
        @vite('resources/css/app.css')

        @stack('styles')

        <style>
            /* Page Transition Styles */
            .page-transition-enter {
                opacity: 0;
                transform: translateY(20px);
            }

            .page-transition-enter-active {
                opacity: 1;
                transform: translateY(0);
                transition: opacity 0.5s ease, transform 0.5s ease;
            }

            .page-transition-exit {
                opacity: 1;
                transform: translateY(0);
            }

            .page-transition-exit-active {
                opacity: 0;
                transform: translateY(-20px);
                transition: opacity 0.3s ease, transform 0.3s ease;
            }

            /* Loading Overlay */
            #page-loader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #1F3A73 0%, #0F1A36 100%);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                transition: opacity 0.5s ease, visibility 0.5s ease;
            }

            #page-loader.hidden {
                opacity: 0;
                visibility: hidden;
            }

            .loader-content {
                text-align: center;
                color: white;
            }

            .loader-logo {
                width: 80px;
                height: 80px;
                margin-bottom: 20px;
                animation: pulse 2s infinite;
            }

            .loader-spinner {
                width: 50px;
                height: 50px;
                border: 4px solid rgba(255, 255, 255, 0.3);
                border-top: 4px solid white;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 20px;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            @keyframes pulse {
                0%, 100% { transform: scale(1); opacity: 1; }
                50% { transform: scale(1.1); opacity: 0.8; }
            }

            /* Smooth scroll behavior */
            html {
                scroll-behavior: smooth;
            }

            /* Link transitions */
            .smooth-link {
                position: relative;
                transition: all 0.3s ease;
            }

            .smooth-link::after {
                content: '';
                position: absolute;
                width: 0;
                height: 2px;
                bottom: -2px;
                left: 50%;
                background-color: currentColor;
                transition: all 0.3s ease;
            }

            .smooth-link:hover::after {
                width: 100%;
                left: 0;
            }

            /* Fade in animation for content */
            .fade-in {
                animation: fadeIn 0.8s ease forwards;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Custom scrollbar styling */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }

            ::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            ::-webkit-scrollbar-thumb {
                background: #1F3A73;
                border-radius: 4px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #3F5FA8;
            }

            /* Selection color */
            ::selection {
                background-color: #1F3A73;
                color: white;
            }

            ::-moz-selection {
                background-color: #1F3A73;
                color: white;
            }
        </style>
    </head>
    <body class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100">
        <!-- Page Loader -->
        <div id="page-loader">
            <div class="loader-content">
                <img src="/images/logo-banner.png" alt="Loading..." class="loader-logo">
                <div class="loader-spinner"></div>
                <p class="text-lg font-medium">Memuat...</p>
            </div>
        </div>

        <!-- Main Content -->
        <div id="app" class="page-transition-enter">
            @yield('content')
        </div>

        <!-- Scripts -->
        <script>
            // Page loader management
            window.addEventListener('load', function() {
                // Hide loader after page loads
                setTimeout(() => {
                    const loader = document.getElementById('page-loader');
                    const app = document.getElementById('app');

                    loader.classList.add('hidden');
                    app.classList.remove('page-transition-enter');
                    app.classList.add('page-transition-enter-active');
                }, 500);
            });

            // Smooth navigation for all internal links
            document.addEventListener('DOMContentLoaded', function() {
                // Add loading state to all internal links
                const internalLinks = document.querySelectorAll('a[href^="/"]:not([target="_blank"]):not([data-no-transition])');

                internalLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        const href = this.getAttribute('href');

                        // Skip if it's a hash link or current page
                        if (href.startsWith('#') || href === window.location.pathname) {
                            return;
                        }

                        e.preventDefault();

                        // Show loader with transition
                        const loader = document.getElementById('page-loader');
                        const app = document.getElementById('app');

                        // Start exit animation
                        app.classList.remove('page-transition-enter-active');
                        app.classList.add('page-transition-exit-active');

                        setTimeout(() => {
                            loader.classList.remove('hidden');
                            // Navigate to new page
                            window.location.href = href;
                        }, 300);
                    });
                });

                // Add smooth scroll for anchor links
                const anchorLinks = document.querySelectorAll('a[href^="#"]');
                anchorLinks.forEach(anchor => {
                    anchor.addEventListener('click', function (e) {
                        e.preventDefault();
                        const target = document.querySelector(this.getAttribute('href'));
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    });
                });

                // Intersection Observer for fade-in animations
                const observerOptions = {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                };

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }
                    });
                }, observerOptions);

                // Observe elements with fade-in class
                document.querySelectorAll('.fade-in').forEach(el => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(30px)';
                    el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                    observer.observe(el);
                });
            });

            // Handle browser back/forward buttons
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    // Page is being restored from cache
                    const loader = document.getElementById('page-loader');
                    loader.classList.add('hidden');
                }
            });

            // Prevent FOUC (Flash of Unstyled Content)
            document.documentElement.classList.add('js');
        </script>

        @stack('scripts')
    </body>
</html>