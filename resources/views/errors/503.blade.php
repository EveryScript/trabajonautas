<!DOCTYPE html>
<html lang="es" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trabajonautas.com</title>

    <!-- Tailwind CSS (Asegúrate de incluir tu CDN o compilación de Tailwind en el proyecto) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'media', // O 'class' si prefieres controlarlo manualmente
            theme: {
                extend: {
                    colors: {
                        'tbn': {
                            primary: '#ff420a',
                            secondary: '#485054',
                            dark: '#242424',
                            light: '#bbbbbb',
                        }
                    }
                }
            }
        }
    </script>
    </main>
</head>

<body
    class="h-full bg-slate-50 dark:bg-[#1a1a1a] text-[#485054] dark:text-[#bbbbbb] font-sans antialiased flex flex-col justify-between selection:bg-[#ff420a] selection:text-white transition-colors duration-300">

    <!-- Elementos decorativos de fondo -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none opacity-20 dark:opacity-10">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-[#ff420a] rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-[#485054] rounded-full blur-3xl"></div>
    </div>

    <!-- Contenido Principal -->
    <main class="relative z-10 flex items-center justify-center flex-grow px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-xl text-center">

            <!-- Logotipo / Insignia Animada -->
            <div
                class="inline-flex items-center justify-center p-3 mb-6 bg-white dark:bg-[#242424] rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-neutral-800 animate-bounce">
                <svg class="w-10 h-10 text-[#ff420a]" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                    </path>
                </svg>
            </div>

            <!-- Título Principal -->
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[#242424] dark:text-white tracking-tight mb-8">
                Trabajonautas se está <span
                    class="text-[#ff420a] underline decoration-wavy decoration-[#ff420a]/40 underline-offset-8">actualizando</span>
            </h1>

            <!-- Descripción -->
            <p class="text-base sm:text-lg text-[#485054] dark:text-[#bbbbbb] mb-8 leading-relaxed">
                <strong class="text-[#242424] dark:text-white font-semibold">Trabajonautas.com</strong> está
                evolucionando.
                Nos encontramos realizando una mejora importante y migrando nuestra infraestructura a un nuevo servicio
                de hosting de
                alto rendimiento para ofrecerte una experiencia más rápida, fluida y sin interrupciones.
            </p>

            <!-- Botón de Reintentar -->
            <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                <button onclick="window.location.reload();"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-[#ff420a] hover:bg-[#e03908] transition-all shadow-lg shadow-[#ff420a]/25 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#ff420a]">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Verificar Estado
                </button>
            </div>

        </div>
    </main>

    <!-- Footer sutil -->
    <footer class="relative z-10 py-6 text-xs text-center text-slate-400 dark:text-neutral-600">
        &copy; {{ date('Y') }} Trabajonautas. Todos los derechos reservados.
    </footer>

</body>

</html>
