<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0A3D62">
    <title>DriveCM — Driving school management, built for Cameroon</title>
    <meta name="description" content="DriveCM is the all-in-one platform for Cameroonian driving schools. Manage theory, practical lessons, Ministry reports, and your public website — all in one place.">
    @vite(['resources/css/app.css'])

    <style>
        /* Gradient mesh background — animated slowly so it feels alive without being distracting. */
        .mesh-bg {
            background:
                radial-gradient(ellipse 80% 60% at 20% 10%, rgba(10, 61, 98, 0.18), transparent 50%),
                radial-gradient(ellipse 70% 50% at 80% 20%, rgba(255, 149, 0, 0.12), transparent 50%),
                radial-gradient(ellipse 60% 50% at 50% 90%, rgba(0, 166, 81, 0.10), transparent 55%),
                linear-gradient(180deg, #fbfcfd 0%, #f4f7fa 100%);
        }

        /* Scroll-reveal animation — applied by the intersection observer below. */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 700ms cubic-bezier(0.16, 1, 0.3, 1), transform 700ms cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Subtle hero pulse — purely decorative. */
        @keyframes hero-glow {
            0%, 100% { opacity: 0.35; transform: scale(1); }
            50%      { opacity: 0.55; transform: scale(1.08); }
        }
        .hero-glow {
            animation: hero-glow 6s ease-in-out infinite;
        }

        /* Stylized device-frame SVG placeholders for screenshots. */
        .device {
            background: linear-gradient(135deg, #0A3D62 0%, #061B33 100%);
            border-radius: 16px;
            box-shadow:
                0 30px 60px -20px rgba(10, 61, 98, 0.45),
                0 18px 32px -8px rgba(10, 61, 98, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        /* Reduce motion for users who request it. */
        @media (prefers-reduced-motion: reduce) {
            .reveal { transition: none; opacity: 1; transform: none; }
            .hero-glow { animation: none; }
        }
    </style>
</head>
<body class="mesh-bg text-neutral antialiased">

    {{-- TOP NAV --}}
    <header class="relative z-10 mx-auto flex max-w-6xl items-center justify-between px-6 py-5">
        <a href="/" class="text-lg font-bold tracking-tight text-primary-dark">
            Drive<span class="text-accent">CM</span>
        </a>
        <nav class="flex items-center gap-3 text-sm">
            <a href="#features" class="hidden text-neutral/70 hover:text-neutral sm:inline">Features</a>
            <a href="#how-it-works" class="hidden text-neutral/70 hover:text-neutral sm:inline">How it works</a>
            <a href="{{ route('apply.create') }}"
               class="rounded-full bg-primary-dark px-4 py-2 font-semibold text-white shadow-lg shadow-primary-dark/20 hover:bg-primary">
                Apply your school
            </a>
        </nav>
    </header>

    {{-- HERO --}}
    <section class="relative overflow-hidden">
        {{-- Decorative glow behind the headline --}}
        <div class="hero-glow pointer-events-none absolute left-1/2 top-1/3 -z-0 h-[28rem] w-[28rem] -translate-x-1/2 -translate-y-1/2 rounded-full bg-accent/30 blur-3xl"></div>

        <div class="relative mx-auto max-w-6xl px-6 pt-10 pb-20 sm:pt-16 sm:pb-28">
            <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-12 lg:gap-16">
                <div class="reveal lg:col-span-7">
                    <div class="inline-flex items-center gap-2 rounded-full border border-primary-dark/10 bg-white/60 px-3 py-1 text-xs font-medium text-primary-dark backdrop-blur">
                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-success"></span>
                        Built for Cameroonian driving schools
                    </div>
                    <h1 class="mt-5 text-[2.75rem] font-bold leading-[1.05] tracking-tight text-primary-dark sm:text-6xl lg:text-7xl">
                        Run your school. <br>
                        <span class="bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent">All in one place.</span>
                    </h1>
                    <p class="mt-6 max-w-xl text-base text-neutral/70 sm:text-lg">
                        DriveCM brings theory lessons, practical sessions, Ministry reports, and your school's public website into one platform — designed for the realities of running a school in Bamenda, Yaoundé, or anywhere in Cameroon.
                    </p>
                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <a href="{{ route('apply.create') }}"
                           class="group inline-flex items-center gap-2 rounded-full bg-primary-dark px-6 py-3 text-sm font-semibold text-white shadow-xl shadow-primary-dark/25 transition hover:bg-primary">
                            Apply your school
                            <span class="transition-transform group-hover:translate-x-0.5">→</span>
                        </a>
                        <a href="#features" class="rounded-full px-5 py-3 text-sm font-medium text-neutral/70 hover:text-neutral">
                            See how it works
                        </a>
                    </div>
                </div>

                <div class="reveal lg:col-span-5">
                    {{-- Stylized SVG placeholder — suggests a dashboard without claiming to be one --}}
                    <div class="device relative aspect-[5/4] p-3">
                        <div class="absolute inset-3 rounded-lg bg-white">
                            <div class="flex h-8 items-center gap-1.5 border-b border-neutral/10 px-3">
                                <span class="h-2 w-2 rounded-full bg-red-300"></span>
                                <span class="h-2 w-2 rounded-full bg-yellow-300"></span>
                                <span class="h-2 w-2 rounded-full bg-green-300"></span>
                                <span class="ml-3 text-[10px] font-mono text-neutral/40">yourschool.drivecm.cm</span>
                            </div>
                            <div class="grid grid-cols-3 gap-3 p-3">
                                <div class="col-span-1 space-y-2">
                                    <div class="rounded bg-primary/15 px-2 py-1 text-[10px] font-semibold text-primary">Dashboard</div>
                                    <div class="px-2 py-1 text-[10px] text-neutral/50">Theory</div>
                                    <div class="px-2 py-1 text-[10px] text-neutral/50">Practical</div>
                                    <div class="px-2 py-1 text-[10px] text-neutral/50">Reports</div>
                                    <div class="px-2 py-1 text-[10px] text-neutral/50">Website</div>
                                </div>
                                <div class="col-span-2 space-y-2">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="rounded bg-surface p-2">
                                            <div class="text-[9px] text-neutral/50">Students</div>
                                            <div class="text-sm font-bold text-neutral">128</div>
                                        </div>
                                        <div class="rounded bg-surface p-2">
                                            <div class="text-[9px] text-neutral/50">Active reports</div>
                                            <div class="text-sm font-bold text-neutral">14</div>
                                        </div>
                                    </div>
                                    <div class="rounded bg-surface p-2">
                                        <div class="mb-1 text-[9px] text-neutral/50">Recent practical sessions</div>
                                        <div class="space-y-1">
                                            <div class="flex justify-between text-[9px]"><span>Mon 9:00</span><span class="text-success">Completed</span></div>
                                            <div class="flex justify-between text-[9px]"><span>Mon 11:00</span><span class="text-success">Completed</span></div>
                                            <div class="flex justify-between text-[9px]"><span>Mon 14:00</span><span class="text-neutral/40">Scheduled</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURES --}}
    <section id="features" class="relative mx-auto max-w-6xl px-6 py-20 sm:py-28">
        <div class="reveal max-w-3xl">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-primary">Everything you need</div>
            <h2 class="mt-3 text-4xl font-bold tracking-tight text-primary-dark sm:text-5xl">
                Four products, one platform.
            </h2>
            <p class="mt-4 text-base text-neutral/70 sm:text-lg">
                Stop juggling Excel sheets, WhatsApp groups and paper files. DriveCM puts every part of running a driving school in one place.
            </p>
        </div>

        <div class="mt-16 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Card 1: Theory --}}
            <div class="reveal group relative overflow-hidden rounded-2xl border border-neutral/10 bg-white p-6 shadow-sm transition hover:shadow-xl hover:-translate-y-1">
                <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-primary/8 transition-transform group-hover:scale-150"></div>
                <div class="relative">
                    <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-neutral">Theory LMS</h3>
                    <p class="mt-2 text-sm text-neutral/60">
                        Author lessons with rich text, images, and video. MCQ tests with auto-grading and pass thresholds.
                    </p>
                </div>
            </div>

            {{-- Card 2: Practical --}}
            <div class="reveal group relative overflow-hidden rounded-2xl border border-neutral/10 bg-white p-6 shadow-sm transition hover:shadow-xl hover:-translate-y-1">
                <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-accent/10 transition-transform group-hover:scale-150"></div>
                <div class="relative">
                    <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-accent/15 text-accent">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M5 17H3v-3.65a1 1 0 0 1 .76-.97l13-3.04a1 1 0 0 1 1.24.97V17h-2"/></svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-neutral">Practical sessions</h3>
                    <p class="mt-2 text-sm text-neutral/60">
                        Schedule lessons, mark attendance, track hours — by instructor, by student, by vehicle.
                    </p>
                </div>
            </div>

            {{-- Card 3: Reports --}}
            <div class="reveal group relative overflow-hidden rounded-2xl border border-neutral/10 bg-white p-6 shadow-sm transition hover:shadow-xl hover:-translate-y-1">
                <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-success/10 transition-transform group-hover:scale-150"></div>
                <div class="relative">
                    <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-success/15 text-success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-neutral">Ministry reports</h3>
                    <p class="mt-2 text-sm text-neutral/60">
                        Validated license-hours reports as PDFs, ready for submission. Snapshot-locked once signed off.
                    </p>
                </div>
            </div>

            {{-- Card 4: Public site --}}
            <div class="reveal group relative overflow-hidden rounded-2xl border border-neutral/10 bg-white p-6 shadow-sm transition hover:shadow-xl hover:-translate-y-1">
                <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-primary-dark/8 transition-transform group-hover:scale-150"></div>
                <div class="relative">
                    <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary-dark/10 text-primary-dark">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-neutral">Your public website</h3>
                    <p class="mt-2 text-sm text-neutral/60">
                        Every school gets a branded site on its own subdomain. Pages, photos, pricing, instructors, map.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section id="how-it-works" class="relative mx-auto max-w-6xl px-6 py-20 sm:py-28">
        <div class="reveal max-w-3xl">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-primary">How it works</div>
            <h2 class="mt-3 text-4xl font-bold tracking-tight text-primary-dark sm:text-5xl">
                From application to first student in days.
            </h2>
        </div>

        <div class="mt-16 grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="reveal relative">
                <div class="text-6xl font-bold text-primary/15">01</div>
                <h3 class="-mt-6 text-lg font-semibold text-neutral">Apply</h3>
                <p class="mt-2 text-sm text-neutral/60">
                    Fill in the form — school name, location, contact details, the subdomain you want.
                </p>
            </div>
            <div class="reveal relative">
                <div class="text-6xl font-bold text-accent/20">02</div>
                <h3 class="-mt-6 text-lg font-semibold text-neutral">Get approved</h3>
                <p class="mt-2 text-sm text-neutral/60">
                    Our team reviews your application and provisions your school. You receive login credentials.
                </p>
            </div>
            <div class="reveal relative">
                <div class="text-6xl font-bold text-success/20">03</div>
                <h3 class="-mt-6 text-lg font-semibold text-neutral">Build &amp; teach</h3>
                <p class="mt-2 text-sm text-neutral/60">
                    Set up your lessons, invite instructors, launch your public site. Run your school.
                </p>
            </div>
        </div>
    </section>

    {{-- PRICING (D155.1 — c "Contact for pricing") --}}
    <section class="relative mx-auto max-w-6xl px-6 py-20 sm:py-28">
        <div class="reveal mx-auto max-w-3xl rounded-3xl border border-primary-dark/10 bg-gradient-to-br from-primary-dark to-primary px-8 py-14 text-center text-white shadow-2xl shadow-primary-dark/30 sm:px-14 sm:py-20">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Pricing</div>
            <h2 class="mt-3 text-3xl font-bold sm:text-4xl">
                Plans that fit your school's size.
            </h2>
            <p class="mx-auto mt-4 max-w-xl text-base text-white/70">
                We're working with schools across Cameroon to find pricing that's fair for every size of operation. Talk to us about what works for you.
            </p>
            <a href="{{ route('apply.create') }}"
               class="mt-8 inline-flex items-center gap-2 rounded-full bg-accent px-6 py-3 text-sm font-semibold text-primary-dark shadow-xl transition hover:bg-accent/90">
                Contact us
                <span>→</span>
            </a>
        </div>
    </section>

    {{-- FINAL CTA --}}
    <section class="relative mx-auto max-w-6xl px-6 py-20 sm:py-28">
        <div class="reveal mx-auto max-w-3xl text-center">
            <h2 class="text-4xl font-bold tracking-tight text-primary-dark sm:text-5xl">
                Ready to put your school online?
            </h2>
            <p class="mx-auto mt-4 max-w-xl text-base text-neutral/70">
                Made in Bamenda, built for Cameroonian driving schools.
            </p>
            <a href="{{ route('apply.create') }}"
               class="group mt-8 inline-flex items-center gap-2 rounded-full bg-primary-dark px-7 py-3.5 text-sm font-semibold text-white shadow-xl shadow-primary-dark/25 transition hover:bg-primary">
                Apply your school
                <span class="transition-transform group-hover:translate-x-0.5">→</span>
            </a>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="border-t border-neutral/10 bg-white/60 backdrop-blur">
        <div class="mx-auto flex max-w-6xl flex-col items-start justify-between gap-4 px-6 py-10 text-sm text-neutral/60 sm:flex-row sm:items-center">
            <div>
                <div class="text-base font-bold text-primary-dark">Drive<span class="text-accent">CM</span></div>
                <div class="mt-1 text-xs">© {{ now()->year }} BlueApex Ltd. All rights reserved.</div>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <a href="{{ route('apply.create') }}" class="hover:text-neutral">Apply your school</a>
            </div>
        </div>
    </footer>

    {{-- Scroll-reveal animation: IntersectionObserver toggles .is-visible when sections enter viewport. --}}
    <script>
        (function () {
            if (! ('IntersectionObserver' in window)) {
                // Fallback — just show everything.
                document.querySelectorAll('.reveal').forEach((el) => el.classList.add('is-visible'));
                return;
            }
            const io = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });
            document.querySelectorAll('.reveal').forEach((el) => io.observe(el));
        })();
    </script>
</body>
</html>