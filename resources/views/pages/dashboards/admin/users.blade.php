<div class="dashboard-panel">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Users</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">All users</h1>
            <p class="mt-2 text-sm text-slate-500">{{ $metrics['active_users'] }} active &middot; {{ $alerts['suspended_users'] }} suspended</p>
        </div>
    </div>

    <div class="mt-6 overflow-x-auto rounded-[1.5rem] border border-slate-200">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-5 py-4 font-semibold">User</th>
                    <th class="px-5 py-4 font-semibold">Role</th>
                    <th class="px-5 py-4 font-semibold">Contact</th>
                    <th class="px-5 py-4 font-semibold">Identity</th>
                    <th class="px-5 py-4 font-semibold">Status</th>
                    <th class="px-5 py-4 font-semibold">Data</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @foreach ($users as $user)
                    @php($profile = $user->driverProfile)
                    <tr class="align-top">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-900">{{ $user->first_name }} {{ $user->last_name }}</p>
                            <p class="text-xs text-slate-500">Joined {{ $user->created_at?->format('d M Y') }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ ucfirst($user->role) }}</td>
                        <td class="px-5 py-4 text-slate-600">
                            <p>{{ $user->email }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $user->phone }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-600">
                            @if ($profile)
                                <p>{{ $profile->cin_verified ? 'Verified' : 'Pending' }}</p>
                                <p class="mt-1 text-xs text-slate-500">CIN {{ $profile->cin_number }}</p>
                            @else
                                <span class="text-slate-400">No driver profile</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">@include('partials.status-chip', ['status' => $user->account_status])</td>
                        <td class="px-5 py-4">
                            <details class="group w-72 max-w-[70vw]">
                                <summary class="cursor-pointer rounded-full bg-slate-100 px-4 py-2 text-xs font-black text-slate-700 transition hover:bg-brand-50 hover:text-brand-700">View data</summary>
                                <dl class="mt-3 grid gap-2 rounded-[1.25rem] border border-slate-200 bg-slate-50 p-4 text-xs text-slate-600">
                                    <div><dt class="font-bold text-slate-900">User ID</dt><dd>{{ $user->id }}</dd></div>
                                    <div><dt class="font-bold text-slate-900">Email verified</dt><dd>{{ $user->email_verified ? 'Yes' : 'No' }}</dd></div>
                                    <div><dt class="font-bold text-slate-900">Phone verified</dt><dd>{{ $user->phone_verified ? 'Yes' : 'No' }}</dd></div>
                                    <div><dt class="font-bold text-slate-900">Suspended at</dt><dd>{{ $user->suspended_at?->format('d M Y H:i') ?? 'Not suspended' }}</dd></div>
                                    <div><dt class="font-bold text-slate-900">Driver rating</dt><dd>{{ $profile?->avg_rating ?? 'N/A' }}</dd></div>
                                    <div><dt class="font-bold text-slate-900">Driver trips</dt><dd>{{ $profile?->total_trips ?? 'N/A' }}</dd></div>
                                    <div><dt class="font-bold text-slate-900">CIN photo</dt><dd>
                                        @if ($profile?->cin_photo)
                                            <a href="{{ Storage::url($profile->cin_photo) }}" target="_blank" rel="noreferrer" class="font-bold text-brand-700 hover:text-brand-800">Open photo</a>
                                        @else
                                            Not provided
                                        @endif
                                    </dd></div>
                                    <div><dt class="font-bold text-slate-900">Vehicle</dt><dd>{{ $profile?->vehicles->first()?->brand ?? 'N/A' }} {{ $profile?->vehicles->first()?->model }}</dd></div>
                                </dl>
                            </details>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
