{{-- CMS-5e (D151) — instructors: responsive 3/2/1 grid; photo or initials placeholder; name/role/bio. --}}
@php
    $items = $block['children'] ?? [];
    $avatarClass = \App\Support\InstructorAvatar::class;
@endphp

@if (count($items) > 0)
    <section class="px-6 py-12">
        <div class="mx-auto max-w-5xl">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $instructor)
                    <div class="rounded-xl border border-neutral/10 bg-white p-6 text-center">
                        @if (! empty($instructor['photo_url']))
                            <img src="{{ $instructor['photo_url'] }}" alt="{{ $instructor['name'] ?? '' }}"
                                 class="mx-auto h-24 w-24 rounded-full border border-neutral/10 object-cover">
                        @else
                            {{-- D151.2 — initials placeholder. Deterministic color from name. --}}
                            @php
                                $name = $instructor['name'] ?? '';
                                $initials = $avatarClass::initials($name);
                                $bg = $avatarClass::color($name);
                            @endphp
                            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full text-2xl font-semibold text-white"
                                 style="background-color: {{ $bg }};">
                                {{ $initials }}
                            </div>
                        @endif

                        @if (! empty($instructor['name']))
                            <h3 class="mt-4 text-base font-semibold text-neutral">{{ $instructor['name'] }}</h3>
                        @endif
                        @if (! empty($instructor['role']))
                            <p class="text-xs uppercase tracking-wide text-primary">{{ $instructor['role'] }}</p>
                        @endif
                        @if (! empty($instructor['bio']))
                            <p class="mt-3 text-sm text-neutral/70">{{ $instructor['bio'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif