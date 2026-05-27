<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Apply your driving school - DriveCM</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-surface text-neutral">
    <div class="mx-auto max-w-xl px-6 py-12">
        <h1 class="text-2xl font-bold text-neutral">Apply your driving school</h1>
        <p class="mt-2 text-sm text-neutral/60">
            Fill in the details below. We review applications and contact approved schools with login credentials.
        </p>

        @if ($errors->any())
            <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('apply.store') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">School name</label>
                <input type="text" name="school_name" value="{{ old('school_name') }}" required
                    class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium">Desired subdomain</label>
                <div class="mt-1 flex items-stretch overflow-hidden rounded-lg border border-neutral/20">
                    <input type="text" name="desired_subdomain" value="{{ old('desired_subdomain') }}" required
                        placeholder="kumba-driving"
                        class="flex-1 px-3 py-2 text-sm">
                    <span class="border-l border-neutral/20 bg-surface px-3 py-2 text-sm text-neutral/60">.drivecm.cm</span>
                </div>
                <p class="mt-1 text-xs text-neutral/50">Lowercase letters, numbers and hyphens. We can adjust this on approval.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium">Your name</label>
                    <input type="text" name="contact_name" value="{{ old('contact_name') }}" required
                        class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email') }}" required
                        class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Phone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone') }}"
                        class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Town</label>
                    <input type="text" name="applicant_town" value="{{ old('applicant_town') }}" required
                        class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
                </div>
            </div>

            {{-- D101 honeypot — off-screen, real users leave blank --}}
            <div style="position:absolute; left:-10000px; top:auto; width:1px; height:1px; overflow:hidden;" aria-hidden="true">
                <label>Website</label>
                <input type="text" name="website" tabindex="-1" autocomplete="off">
            </div>

            <button type="submit"
                class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                Submit application
            </button>
        </form>
    </div>
</body>
</html>