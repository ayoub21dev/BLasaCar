@php($title = 'Account Settings')

@extends('layouts.app')

@section('content')
    <section class="py-8 sm:py-12">
        <div class="shell page-enter">
            <div class="mx-auto max-w-5xl space-y-8">
                <div class="surface p-8 sm:p-10">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Settings</p>
                            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Account preferences</h1>
                            <p class="mt-3 max-w-2xl text-slate-500">Keep your profile, password, and identity details up to date.</p>
                        </div>
                        <a href="{{ route($user->dashboardRoute()) }}" class="brand-button-secondary">My account</a>
                    </div>
                </div>

                <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_340px]">
                    <div class="space-y-8">
                        <div class="bg-white rounded-[2.5rem] border border-slate-100 p-8 shadow-sm">
                            <h2 class="text-2xl font-black text-slate-950">Profile details</h2>

                            <form method="POST" action="{{ route('account.settings.profile.update') }}" class="mt-6 grid gap-5 sm:grid-cols-2">
                                @csrf
                                @method('PATCH')

                                <label class="space-y-2">
                                    <span class="text-sm font-semibold text-slate-700">First name</span>
                                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                    @error('first_name')
                                        <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </label>

                                <label class="space-y-2">
                                    <span class="text-sm font-semibold text-slate-700">Last name</span>
                                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                    @error('last_name')
                                        <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </label>

                                <label class="space-y-2">
                                    <span class="text-sm font-semibold text-slate-700">Email</span>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                    @error('email')
                                        <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </label>

                                <label class="space-y-2">
                                    <span class="text-sm font-semibold text-slate-700">Phone</span>
                                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                    @error('phone')
                                        <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </label>

                                <div class="sm:col-span-2">
                                    <button type="submit" class="brand-button rounded-[1.25rem]">Save profile</button>
                                </div>
                            </form>
                        </div>

                        <div class="bg-white rounded-[2.5rem] border border-slate-100 p-8 shadow-sm">
                            <h2 class="text-2xl font-black text-slate-950">Password</h2>

                            <form method="POST" action="{{ route('account.settings.password.update') }}" class="mt-6 grid gap-5">
                                @csrf
                                @method('PATCH')

                                <label class="space-y-2">
                                    <span class="text-sm font-semibold text-slate-700">Current password</span>
                                    <input type="password" name="current_password" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                    @error('current_password')
                                        <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </label>

                                <div class="grid gap-5 sm:grid-cols-2">
                                    <label class="space-y-2">
                                        <span class="text-sm font-semibold text-slate-700">New password</span>
                                        <input type="password" name="password" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                        @error('password')
                                            <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                        @enderror
                                    </label>

                                    <label class="space-y-2">
                                        <span class="text-sm font-semibold text-slate-700">Confirm password</span>
                                        <input type="password" name="password_confirmation" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                    </label>
                                </div>

                                <div>
                                    <button type="submit" class="brand-button rounded-[1.25rem]">Update password</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if (! $user->isAdmin())
                        <aside class="space-y-8">
                            <div class="bg-white rounded-[2.5rem] border border-slate-100 p-8 shadow-sm">
                                <h2 class="text-2xl font-black text-slate-950">Identity</h2>

                                @if ($user->isDriver())
                                <div class="mt-6 space-y-4 text-sm text-slate-600">
                                    <div class="rounded-2xl bg-slate-50 px-5 py-4">
                                        <p class="font-semibold text-slate-950">Driver profile</p>
                                        <p class="mt-1">{{ $user->driverProfile?->cin_verified ? 'Identity verified' : 'Identity verification pending' }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 px-5 py-4">
                                        <p class="font-semibold text-slate-950">CIN number</p>
                                        <p class="mt-1">{{ $user->driverProfile?->cin_number ?? 'Not provided' }}</p>
                                    </div>
                                </div>
                            @elseif ($user->isTraveler())
                                <p class="mt-4 text-sm leading-6 text-slate-500">
                                    Add your identity and first vehicle to publish rides as a driver.
                                </p>
                                <a href="{{ route('drivers.onboarding.create') }}" class="brand-button mt-6 w-full rounded-[1.25rem]">
                                    Become a driver
                                </a>
                                @endif
                            </div>
                        </aside>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
