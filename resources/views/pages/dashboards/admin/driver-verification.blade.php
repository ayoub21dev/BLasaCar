<div class="dashboard-panel">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Driver verification</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Review submitted IDs</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">{{ $metrics['pending_driver_verifications'] }} profile{{ $metrics['pending_driver_verifications'] === 1 ? '' : 's' }} waiting for CIN review.</p>
        </div>
        <span class="w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-black text-amber-700">{{ $pendingDriverProfiles->count() }} pending</span>
    </div>

    <div class="mt-6 space-y-4">
        @forelse ($pendingDriverProfiles as $profile)
            @php
                $cinPhotoExists = filled($profile->cin_photo) && Storage::disk('public')->exists($profile->cin_photo);
            @endphp

            <div class="grid gap-5 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 lg:grid-cols-[minmax(280px,380px)_minmax(0,1fr)]">
                <div class="min-w-0">
                    <div class="aspect-[4/3] overflow-hidden rounded-[1.25rem] border border-slate-200 bg-white">
                        @if ($cinPhotoExists)
                            <a href="{{ Storage::url($profile->cin_photo) }}" target="_blank" rel="noreferrer">
                                <img src="{{ Storage::url($profile->cin_photo) }}" alt="CIN photo for {{ $profile->user?->first_name }} {{ $profile->user?->last_name }}" class="h-full w-full object-cover">
                            </a>
                        @else
                            <div class="flex h-full items-center justify-center bg-white px-6 text-center">
                                <div>
                                    <p class="text-sm font-black text-rose-600">ID photo unavailable</p>
                                    @if ($profile->cin_photo)
                                        <p class="mt-2 break-all text-xs font-semibold text-slate-400">{{ $profile->cin_photo }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="min-w-0">
                    <div class="flex h-full flex-col justify-between gap-5">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-950">{{ $profile->user?->first_name }} {{ $profile->user?->last_name }}</p>
                            <p class="mt-1 break-words text-sm font-semibold text-slate-500">{{ $profile->user?->email }}</p>
                            <p class="mt-1 text-sm font-semibold text-slate-500">{{ $profile->user?->phone }}</p>
                        </div>

                        <dl class="grid gap-3 text-sm text-slate-600 sm:grid-cols-2">
                            <div class="rounded-[1rem] bg-white px-4 py-3">
                                <dt class="font-bold text-slate-900">CIN</dt>
                                <dd class="mt-1 break-words">{{ $profile->cin_number }}</dd>
                            </div>
                            <div class="rounded-[1rem] bg-white px-4 py-3">
                                <dt class="font-bold text-slate-900">Vehicle</dt>
                                <dd class="mt-1 break-words">{{ $profile->vehicles->first()?->brand ?? 'No vehicle' }} {{ $profile->vehicles->first()?->model }}</dd>
                            </div>
                            <div class="rounded-[1rem] bg-white px-4 py-3">
                                <dt class="font-bold text-slate-900">Submitted</dt>
                                <dd class="mt-1">{{ $profile->created_at?->format('d M Y H:i') }}</dd>
                            </div>
                            <div class="rounded-[1rem] bg-white px-4 py-3">
                                <dt class="font-bold text-slate-900">ID file</dt>
                                <dd class="mt-1">{{ $cinPhotoExists ? 'Uploaded' : 'Missing locally' }}</dd>
                            </div>
                        </dl>

                        <form method="POST" action="{{ route('admin.driver-profiles.verify', $profile) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" @disabled(! $cinPhotoExists) class="rounded-full bg-emerald-600 px-4 py-2 text-xs font-black text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300">
                                Verify driver
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="rounded-[1.25rem] bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-500">No pending driver profiles.</p>
        @endforelse
    </div>
</div>
