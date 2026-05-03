<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>citalify — Reservas online y recordatorios Email para tu negocio</title>
    <meta name="description" content="Gestiona tus citas online y reduce las ausencias hasta un 60% con recordatorios automáticos por Email. Para peluquerías, clínicas, fisioterapeutas y más.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700,800|outfit:600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-favicons />
    <style>
        :root {
            --brand:       #047857;
            --brand-dark:  #065f46;
            --brand-light: #d1fae5;
            --brand-mid:   #10b981;
            --accent:      #f59e0b;
            --accent-light:#fef3c7;
            --dark:        #0f172a;
            --dark-soft:   #1e293b;
            --muted:       #64748b;
            --muted-light: #94a3b8;
            --surface:     #f8fafc;
            --surface-warm: #fefce8;
            --white:       #ffffff;
            --border:      #e2e8f0;
            --danger-bg:   #fef2f2;
            --danger-border:#fecaca;
            --shadow-sm: 0 1px 2px rgba(0,0,0,.04);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,.07), 0 2px 4px -2px rgba(0,0,0,.05);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,.08), 0 4px 6px -4px rgba(0,0,0,.04);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,.08), 0 8px 10px -6px rgba(0,0,0,.04);
        }

        body { font-family: 'DM Sans', sans-serif; color: var(--dark); background: var(--white); -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        .font-display { font-family: 'Outfit', sans-serif; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp .6s cubic-bezier(.16,1,.3,1) both; }
        .delay-1 { animation-delay: .1s; }
        .delay-2 { animation-delay: .2s; }
        .delay-3 { animation-delay: .3s; }
        .delay-4 { animation-delay: .4s; }

        .gradient-mesh {
            background: linear-gradient(135deg, var(--surface) 0%, var(--brand-light) 30%, var(--surface-warm) 70%, var(--surface) 100%);
            background-size: 400% 400%;
        }

        .dot-grid {
            background-image: radial-gradient(circle at 1px 1px, #cbd5e1 1px, transparent 0);
            background-size: 32px 32px;
            opacity: 0.5;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            opacity: 0.2;
        }
        .blob-1 { width: 500px; height: 500px; top: -200px; right: -150px; background: linear-gradient(135deg, var(--brand-mid), var(--brand)); }
        .blob-2 { width: 400px; height: 400px; bottom: -100px; left: -100px; background: linear-gradient(135deg, var(--accent), #fbbf24); }

        .badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: white;
            color: var(--brand-dark);
            font-size: .75rem; font-weight: 700;
            letter-spacing: .1em; text-transform: uppercase;
            padding: 8px 18px; border-radius: 100px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
        }
        .badge-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--brand-mid);
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.6;transform:scale(1.1)} }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: white;
            font-weight: 700;
            border-radius: 14px;
            transition: all .25s cubic-bezier(.4,0,.2,1);
            box-shadow: 0 4px 14px rgba(4,120,87,.35);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(4,120,87,.4);
            color: white;
        }
        .btn-primary:active { transform: translateY(0); }

        .btn-ghost {
            border: 2px solid var(--border);
            color: var(--dark);
            font-weight: 600;
            border-radius: 14px;
            background: white;
            transition: all .2s ease;
        }
        .btn-ghost:hover { background: var(--surface); border-color: var(--brand-mid); color: var(--brand-dark); }

        .feature-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 2rem;
            transition: all .3s cubic-bezier(.4,0,.2,1);
            box-shadow: var(--shadow-sm);
        }
        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-xl);
            border-color: var(--brand-light);
        }
        .feature-card.featured {
            border-color: var(--brand);
            background: linear-gradient(180deg, white 0%, var(--brand-light) 100%);
            box-shadow: 0 0 0 1px rgba(4,120,87,.1), var(--shadow-lg);
        }

        .icon-box {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, var(--brand-light) 0%, #a7f3d0 100%);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            color: var(--brand-dark);
            margin-bottom: 1.5rem;
            flex-shrink: 0;
        }
        .icon-box.solid {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: white;
        }

        .problem-card {
            background: white;
            border: 1px solid var(--danger-border);
            border-radius: 20px;
            padding: 2rem;
            transition: all .25s ease;
            box-shadow: var(--shadow-sm);
        }
        .problem-card:hover {
            box-shadow: var(--shadow-md);
            border-color: #fca5a5;
        }
        .problem-icon {
            width: 48px; height: 48px;
            background: var(--danger-bg);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            color: #dc2626;
            margin-bottom: 1rem;
        }

        .price-card {
            border: 2px solid var(--border);
            border-radius: 24px;
            padding: 2rem;
            background: white;
            transition: all .3s ease;
        }
        .price-card:hover {
            border-color: var(--brand-light);
            box-shadow: var(--shadow-lg);
        }
        .price-card.popular {
            border-color: var(--brand);
            background: linear-gradient(180deg, var(--brand-light) 0%, white 100%);
            box-shadow: 0 0 0 2px rgba(4,120,87,.15), var(--shadow-lg);
            transform: scale(1.02);
        }
        @media (max-width: 768px) { .price-card.popular { transform: none; } }

        .testimonial {
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.25);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 2rem;
        }

        .divider-label {
            display: flex; align-items: center; gap: 1rem;
            color: var(--muted); font-size: .75rem; font-weight: 700;
            letter-spacing: .12em; text-transform: uppercase;
        }
        .divider-label::before, .divider-label::after {
            content: ''; flex: 1; height: 1px; background: linear-gradient(90deg, transparent, var(--border), transparent);
        }

        .nav-link {
            color: var(--muted);
            font-size: .9375rem;
            font-weight: 500;
            transition: color .2s;
        }
        .nav-link:hover { color: var(--brand); }

        .check { color: var(--brand-mid); flex-shrink: 0; }

        /* Mobile menu */
        .mobile-menu { transition: opacity .2s, transform .2s; }
        .mobile-menu.closed { opacity: 0; pointer-events: none; transform: translateY(-10px); }
    </style>
</head>
<body x-data="{ mobileMenuOpen: false }" class="scroll-smooth">

{{-- NAVBAR --}}
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-xl border-b border-slate-100 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 flex items-center justify-between h-16 lg:h-18">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
            <x-application-logo class="group-hover:opacity-90 transition-opacity" />
        </a>
        <nav class="hidden md:flex items-center gap-8">
            <a href="#como-funciona" class="nav-link">Cómo funciona</a>
            <a href="#precios" class="nav-link">Precios</a>
        </nav>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="nav-link font-semibold" style="color:var(--dark)">Mi panel</a>
            @else
                <a href="{{ route('login') }}" class="nav-link hidden sm:inline">Entrar</a>
                <a href="{{ route('register') }}" class="btn-primary text-sm px-5 py-3">
                    Probar gratis
                </a>
            @endauth
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden w-10 h-10 rounded-xl flex items-center justify-center hover:bg-slate-100" aria-label="Menú">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
    <div :class="mobileMenuOpen ? '' : 'closed'" class="mobile-menu md:hidden absolute top-full left-0 right-0 bg-white border-b border-slate-100 shadow-lg px-4 py-4">
        <nav class="flex flex-col gap-1">
            <a href="#como-funciona" @click="mobileMenuOpen = false" class="nav-link py-3 px-4 rounded-xl hover:bg-slate-50">Cómo funciona</a>
            <a href="#precios" @click="mobileMenuOpen = false" class="nav-link py-3 px-4 rounded-xl hover:bg-slate-50">Precios</a>
            @guest
            <a href="{{ route('login') }}" class="nav-link py-3 px-4 rounded-xl hover:bg-slate-50">Entrar</a>
            @endguest
        </nav>
    </div>
</header>

{{-- HERO --}}
<section class="relative overflow-hidden pt-24 pb-32 px-4 sm:px-6 gradient-mesh">
    <div class="dot-grid absolute inset-0"></div>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="relative max-w-4xl mx-auto text-center">
        <div class="fade-up">
            <span class="badge">
                <span class="badge-dot"></span>
                30 días gratis · Sin tarjeta
            </span>
        </div>

        <h1 class="font-display fade-up delay-1 mt-8 mb-6 leading-[1.1] tracking-tight"
            style="font-size:clamp(2.75rem,7vw,4.5rem); color:var(--dark)">
            Una agenda que trabaja<br>
            <span class="bg-gradient-to-r from-emerald-600 to-emerald-800 bg-clip-text text-transparent">mientras tú trabajas</span>
        </h1>

        <p class="fade-up delay-2 text-lg sm:text-xl max-w-2xl mx-auto mb-10 leading-relaxed" style="color:var(--muted)">
            Página de reservas propia, panel de agenda y recordatorios automáticos por Email.
            Reduce las ausencias hasta un <strong class="text-slate-800">60%</strong> y olvídate de gestionar citas por teléfono.
        </p>

        <div class="fade-up delay-3 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="btn-primary px-8 py-4 text-lg rounded-xl">
                Prueba 30 días gratis
            </a>
            <a href="#como-funciona" class="btn-ghost px-8 py-4 text-lg rounded-xl">
                Ver cómo funciona
            </a>
        </div>

        <p class="fade-up delay-4 mt-6 text-sm" style="color:var(--muted-light)">
            Configuración en menos de 10 minutos · Sin conocimientos técnicos
        </p>

        {{-- Social proof --}}
        <div class="fade-up delay-4 mt-14 inline-flex items-center gap-4 bg-white/80 backdrop-blur-sm border px-6 py-4 rounded-2xl shadow-lg" style="border-color:var(--border)">
            <div class="flex -space-x-3">
                @foreach(['#059669','#10b981','#34d399','#6ee7b7'] as $c)
                    <div class="w-10 h-10 rounded-full border-2 border-white flex items-center justify-center text-white text-sm font-bold shadow-md" style="background:{{ $c }}">
                        {{ ['A','M','L','C'][$loop->index] }}
                    </div>
                @endforeach
            </div>
            <div class="text-left">
                <div class="text-sm font-bold text-slate-800">+240 negocios activos</div>
                <div class="flex gap-0.5 mt-0.5">
                    @for ($i=0;$i<5;$i++)
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="#f59e0b"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</section>

{{-- PROBLEMA --}}
<section class="py-24 px-4 sm:px-6 bg-white">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16">
            <p class="divider-label mb-8"><span>El problema</span></p>
            <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl mb-4 font-bold tracking-tight text-slate-800">
                ¿Cuánto te cuesta gestionar<br>citas por WhatsApp?
            </h2>
            <p class="text-lg max-w-xl mx-auto text-slate-600">
                Cada ausencia es dinero perdido. Cada confirmación manual es tiempo robado.
            </p>
        </div>
        <div class="grid sm:grid-cols-3 gap-6">
            <div class="problem-card">
                <div class="problem-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="font-bold text-lg mb-2 text-slate-800">Mensajes perdidos</h3>
                <p class="text-sm leading-relaxed text-slate-600">El WhatsApp de trabajo se mezcla con el personal. Peticiones sin responder. Clientes que se van a la competencia.</p>
            </div>
            <div class="problem-card">
                <div class="problem-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
                <h3 class="font-bold text-lg mb-2 text-slate-800">Ausencias constantes</h3>
                <p class="text-sm leading-relaxed text-slate-600">Con 2 ausencias a la semana y citas de 20€, pierdes <strong class="text-slate-800">más de 1.500€ al año</strong>. Sin posibilidad de recuperar ese hueco.</p>
            </div>
            <div class="problem-card">
                <div class="problem-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="font-bold text-lg mb-2 text-slate-800">Sin visibilidad</h3>
                <p class="text-sm leading-relaxed text-slate-600">No sabes qué horas son las más demandadas, qué servicios generan más ingresos ni quiénes son tus clientes más fieles.</p>
            </div>
        </div>
    </div>
</section>

{{-- SOLUCIÓN --}}
<section class="py-24 px-4 sm:px-6" style="background:var(--surface)">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16">
            <p class="divider-label mb-8"><span>La solución</span></p>
            <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl mb-4 font-bold tracking-tight text-slate-800">
                Todo lo que necesitas,<br>nada de lo que no
            </h2>
            <p class="text-lg text-slate-600">Tres herramientas que trabajan juntas para que tú trabajes menos.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="feature-card">
                <div class="icon-box">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-2 text-slate-800">Página de reservas propia</h3>
                <p class="text-sm leading-relaxed text-slate-600">
                    Tu enlace personalizado <code class="text-xs px-2 py-1 rounded-lg font-mono bg-emerald-50 text-emerald-700">citalify.es/tu-negocio</code>. Tus clientes ven tu disponibilidad real y reservan en 30 segundos, sin llamarte.
                </p>
            </div>
            <div class="feature-card">
                <div class="icon-box">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-lg mb-2 text-slate-800">Panel de agenda</h3>
                <p class="text-sm leading-relaxed text-slate-600">Vista diaria y semanal. Gestiona servicios, precios, empleados y horarios. Añade citas manuales y bloquea días con un clic.</p>
            </div>
            <div class="feature-card featured">
                <div class="icon-box solid">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-700">El más valorado</span>
                <h3 class="font-bold text-lg mb-2 mt-1 text-slate-800">Recordatorios por Email</h3>
                <p class="text-sm leading-relaxed text-slate-600">24h antes de cada cita, tu cliente recibe un mensaje automático. Puede confirmar o cancelar con una respuesta. Tu agenda se actualiza sola.</p>
            </div>
        </div>
    </div>
</section>

{{-- CÓMO FUNCIONA --}}
<section id="como-funciona" class="py-24 px-4 sm:px-6 bg-white">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-16">
            <p class="divider-label mb-8"><span>El proceso</span></p>
            <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl mb-4 font-bold tracking-tight text-slate-800">
                Listo en menos de 10 minutos
            </h2>
            <p class="text-lg text-slate-600">Sin instalaciones. Sin equipo técnico. Desde el navegador.</p>
        </div>
        <div class="space-y-10">
            @foreach([
                ['01', 'Crea tu cuenta', 'Regístrate en 30 segundos. En el siguiente paso configuras el nombre de tu negocio, tus servicios y tu horario semanal.'],
                ['02', 'Comparte tu enlace', 'Copia tu URL única y ponla en tu Instagram, en tu web o mándala por WhatsApp. Ellos ven tu disponibilidad real y reservan solos.'],
                ['03', 'Recibe reservas y olvídate', 'Citalify envía recordatorios automáticos por Email 24h antes. Si el cliente cancela, recibes un aviso al instante y el hueco queda libre.'],
            ] as [$num, $title, $desc])
            <div class="flex gap-6 items-start group">
                <div class="relative shrink-0 w-16 h-16 rounded-2xl flex items-center justify-center font-display font-bold text-xl bg-gradient-to-br from-emerald-50 to-emerald-100 text-emerald-700 transition-all duration-300 group-hover:scale-105 group-hover:shadow-md">
                    {{ $num }}
                </div>
                <div class="pt-2">
                    <h3 class="font-bold text-xl mb-2 text-slate-800">{{ $title }}</h3>
                    <p class="text-slate-600 leading-relaxed">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- PRUEBA SOCIAL --}}
<section class="py-24 px-4 sm:px-6 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800">
    <div class="max-w-3xl mx-auto text-center">
        <p class="font-display mb-2 text-7xl sm:text-8xl font-bold text-white leading-none">60%</p>
        <p class="text-xl mb-12 text-emerald-100">de reducción en ausencias con recordatorios automáticos</p>
        <div class="testimonial max-w-lg mx-auto text-left">
            <div class="flex gap-1 mb-4">
                @for ($i=0;$i<5;$i++)
                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="#fbbf24"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @endfor
            </div>
            <p class="text-lg sm:text-xl mb-4 text-white/95 leading-relaxed">
                "Desde que uso citalify no tengo que estar pendiente del móvil. Los clientes reservan solos y casi nadie falla a la cita."
            </p>
            <p class="text-sm font-semibold text-white/60">— Carmen R., Peluquería en Madrid</p>
        </div>
    </div>
</section>

{{-- PRECIOS --}}
<section id="precios" class="py-24 px-4 sm:px-6 bg-center bg-slate-50">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16">
            <p class="divider-label mb-8"><span>Precios</span></p>
            <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl mb-4 font-bold tracking-tight text-slate-800">
                Precio claro, sin sorpresas
            </h2>
            <p class="text-lg text-slate-600">Todos los planes incluyen 30 días de prueba gratis. Sin tarjeta de crédito.</p>
        </div>
        <div class="grid md:grid-cols-2 gap-6 items-stretch">

            {{-- Básico --}}
            <div class="price-card h-full flex flex-col">
                <h3 class="font-bold text-xl mb-2 text-slate-800">Básico</h3>
                <p class="text-sm mb-6 text-slate-600">Para autónomos con agenda personal</p>
                <div class="mb-8">
                    <span class="font-display text-5xl font-bold text-slate-800">9,99€</span>
                    <span class="text-sm text-slate-500">/mes</span>
                </div>
                <ul class="space-y-4 text-sm mb-8 text-slate-600 flex-1">
                    @foreach(['Agenda digital completa','Servicios ilimitados','Página de reservas propia','Recordatorios por email','Soporte por email'] as $feat)
                    <li class="flex items-center gap-3">
                        <svg class="check w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        {{ $feat }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="btn-primary block text-center py-3.5 text-sm font-semibold rounded-xl mt-auto">
                    Empezar gratis
                </a>
            </div>

            {{-- Avanzado --}}
            <div class="price-card relative overflow-hidden h-full flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-bold text-xl text-slate-800">Avanzado</h3>
                    <span class="text-[10px] sm:text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-500 uppercase tracking-wider">
                        Próximamente
                    </span>
                </div>
                <p class="text-sm mb-6 text-slate-600">Para negocios que quieren reducir ausencias</p>
                <div class="mb-8">
                    <span class="font-display text-5xl font-bold text-slate-800">19,99€</span>
                    <span class="text-sm text-slate-500">/mes</span>
                </div>
                <div class="opacity-30 select-none pointer-events-none flex-1 flex flex-col" style="filter: blur(5px);">
                    <ul class="space-y-4 text-sm mb-8 text-slate-600 flex-1">
                        @foreach(['Hasta 3 empleados','Citas ilimitadas','Todo del plan Básico','Recordatorios automáticos por WhatsApp','2.º recordatorio configurable','Estadísticas básicas','Soporte prioritario por chat'] as $feat)
                        <li class="flex items-center gap-3">
                            <svg class="check w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            {{ $feat }}
                        </li>
                        @endforeach
                    </ul>
                    <div class="btn-ghost block text-center py-3.5 text-sm font-bold rounded-xl bg-slate-50 mt-auto">
                        Contratar
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA FINAL --}}
<section class="py-28 px-4 sm:px-6 bg-slate-900">
    <div class="max-w-2xl mx-auto text-center">
        <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl text-white mb-6 leading-tight font-bold">
            ¿Cuánto vale para ti recuperar<br>
            <span class="text-emerald-400">2 horas a la semana?</span>
        </h2>
        <p class="text-lg mb-10 text-slate-400">
            Con citalify dejas de gestionar citas por teléfono, reduces las ausencias y tienes control total de tu agenda. Desde 9,99€ al mes.
        </p>
        <a href="{{ route('register') }}" class="btn-primary inline-block px-10 py-4 text-lg rounded-xl">
            Prueba 30 días gratis
        </a>
        <p class="mt-5 text-sm text-slate-500">30 días de prueba en todos los planes. Sin tarjeta de crédito. Cancela cuando quieras.</p>
    </div>
</section>

{{-- FOOTER --}}
<footer class="border-t border-slate-800 py-10 px-4 sm:px-6 bg-slate-900">
    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-2.5">
            <img src="{{ asset('img/logo.png') }}" alt="Citalify Logo" class="h-9 w-auto rounded-xl">
            <span class="font-display font-bold text-white text-lg">citalify</span>
        </div>
        <p class="text-sm text-slate-500">&copy; {{ date('Y') }} citalify. Todos los derechos reservados.</p>
        <div class="flex gap-6 text-sm text-slate-500">
            <a href="#" class="hover:text-white transition-colors">Privacidad</a>
            <a href="#" class="hover:text-white transition-colors">Términos</a>
            <a href="#" class="hover:text-white transition-colors">Contacto</a>
        </div>
    </div>
</footer>
</body>
</html>
