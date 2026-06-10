<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0A3D62">
    <title>Pricing & features · DriveCM</title>
    <meta name="description" content="Transparent pricing for Cameroonian driving schools. {{ $settings->free_trial_days }} days free, then {{ number_format($settings->monthly_fee_xaf) }} XAF/month.">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-surface text-neutral antialiased">
    {{-- Header --}}
    <header class="mx-auto flex max-w-4xl items-center justify-between px-6 py-5">
        <a href="/" class="text-lg font-bold tracking-tight text-primary-dark">
            Drive<span class="text-accent">CM</span>
        </a>
        <a href="/" class="text-sm font-medium text-neutral/60 hover:text-neutral">← Back to home</a>
    </header>

    <main class="mx-auto max-w-4xl px-6 py-10">
        <div class="text-center">
            <div class="inline-flex items-center gap-2 rounded-full border border-primary-dark/10 bg-white px-3 py-1 text-xs font-medium text-primary-dark">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-success"></span>
                Transparent pricing
            </div>
            <h1 class="mt-4 text-4xl font-bold tracking-tight text-primary-dark sm:text-5xl">
                Simple pricing for Cameroonian schools.
            </h1>
            <p class="mt-4 text-base text-neutral/70 sm:text-lg">
                One flat monthly fee. {{ $settings->free_trial_days }} days free to try everything.
            </p>
        </div>

        {{-- Pricing card --}}
        <div class="mt-12 rounded-3xl border border-primary-dark/10 bg-gradient-to-br from-primary-dark to-primary px-8 py-12 text-center text-white shadow-2xl shadow-primary-dark/20">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Monthly fee</div>
            <div class="mt-3 flex items-baseline justify-center gap-2">
                <span class="text-6xl font-bold">{{ number_format($settings->monthly_fee_xaf) }}</span>
                <span class="text-2xl text-white/70">XAF</span>
            </div>
            <div class="mt-1 text-sm text-white/60">per school, per month</div>

            <div class="mx-auto mt-8 max-w-md border-t border-white/15 pt-6">
                <div class="inline-flex items-center gap-2 rounded-full bg-accent/20 px-4 py-2 text-sm font-semibold text-accent">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    First {{ $settings->free_trial_days }} days free
                </div>
                <p class="mt-3 text-sm text-white/70">
                    From the day your school is approved. No payment needed during the trial.
                </p>
            </div>
        </div>

        {{-- What's included --}}
        <div class="mt-12">
            <h2 class="text-center text-2xl font-bold tracking-tight text-primary-dark">What's included</h2>
            <p class="mt-2 text-center text-sm text-neutral/60">Everything for one flat fee. No tiers, no surprises.</p>

            <div class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-2">
                @php
                    $features = [
                        ['Theory LMS', 'Author lessons, build tests, auto-grade student attempts.'],
                        ['Practical scheduling', 'Schedule sessions, mark attendance, track instructor hours.'],
                        ['Ministry-of-Transport reports', 'License-hours PDFs validated and ready for submission.'],
                        ['Multi-staff support', 'Owner, secretaries, and instructors with role-based access.'],
                        ['Public school website', 'Your own subdomain with editable pages and branding.'],
                        ['Student enrollment workflow', 'Public application form + review queue.'],
                        ['Tuition payment tracking', 'Configurable payment types + screenshot review.'],
                        ['Custom subdomain', 'yourschool.drivecm.cm — yours from day one.'],
                    ];
                @endphp
                @foreach ($features as [$title, $desc])
                    <div class="flex gap-3 rounded-xl border border-neutral/10 bg-white p-4">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-success/10 text-success">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                 stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-neutral">{{ $title }}</div>
                            <p class="mt-0.5 text-xs text-neutral/60">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- How billing works --}}
        <div class="mt-12 rounded-2xl border border-neutral/10 bg-white p-6 sm:p-8">
            <h2 class="text-xl font-semibold text-primary-dark">How billing works</h2>
            <ul class="mt-4 space-y-3 text-sm text-neutral/70">
                <li class="flex gap-3">
                    <span class="mt-1.5 inline-block h-1.5 w-1.5 shrink-0 rounded-full bg-primary"></span>
                    <span>Your first <strong class="text-neutral">{{ $settings->free_trial_days }} days are free</strong> from approval. Set everything up — no payment yet.</span>
                </li>
                <li class="flex gap-3">
                    <span class="mt-1.5 inline-block h-1.5 w-1.5 shrink-0 rounded-full bg-primary"></span>
                    <span>After the trial, you'll be prompted to pay <strong class="text-neutral">{{ number_format($settings->monthly_fee_xaf) }} XAF</strong> in the last 5 days of each month via MTN MoMo or Orange Money.</span>
                </li>
                <li class="flex gap-3">
                    <span class="mt-1.5 inline-block h-1.5 w-1.5 shrink-0 rounded-full bg-primary"></span>
                    <span>If a month goes unpaid, your account is paused — your public website stays online, but the platform features pause until you pay.</span>
                </li>
                <li class="flex gap-3">
                    <span class="mt-1.5 inline-block h-1.5 w-1.5 shrink-0 rounded-full bg-primary"></span>
                    <span>Upload a screenshot of your payment, our team reviews it, and you're back instantly.</span>
                </li>
            </ul>
        </div>

        {{-- CTA --}}
        <div class="mt-12 text-center">
            <a href="{{ route('apply.create') }}"
               class="inline-flex items-center gap-2 rounded-full bg-primary-dark px-7 py-3.5 text-sm font-semibold text-white shadow-xl shadow-primary-dark/25 transition hover:bg-primary">
                Continue to application
                <span>→</span>
            </a>
            <p class="mt-3 text-xs text-neutral/50">
                You'll confirm you agree to these terms on the next page.
            </p>
        </div>
    </main>

    <footer class="mt-16 border-t border-neutral/10 bg-white/60">
        <div class="mx-auto max-w-4xl px-6 py-6 text-center text-xs text-neutral/50">
            © {{ now()->year }} DriveCM
        </div>
    </footer>
</body>
</html>