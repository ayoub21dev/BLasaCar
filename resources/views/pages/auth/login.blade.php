@php($title = 'Log in')

@extends('layouts.app')

@section('content')
    <section class="py-16">
        <div class="shell page-enter">
            <div class="mx-auto max-w-md">
                <div class="surface p-8 sm:p-10">
                    <div class="text-center">
                        <img src="{{ asset('assets/blasacar-logo.png') }}" alt="BlassaCar logo" class="mx-auto h-14 w-auto object-contain">
                        <h1 class="mt-8 text-3xl font-black text-slate-950">Welcome back</h1>
                        <p class="mt-3 text-slate-500">Log in to manage rides, bookings, and your role-based dashboard.</p>
                    </div>

                    <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-4">
                        @csrf

                        <label class="block">
                            <span class="sr-only">Email address</span>
                            <div class="input-shell">
                                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect width="20" height="16" x="2" y="4" rx="2" />
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                                </svg>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email address" class="w-full bg-transparent text-sm font-medium text-slate-700 outline-none">
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </label>

                        <label class="block">
                            <span class="sr-only">Password</span>
                            <div class="input-shell">
                                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                <input type="password" name="password" placeholder="Password" class="w-full bg-transparent text-sm font-medium text-slate-700 outline-none">
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </label>

                        <div class="flex items-center justify-between text-sm">
                            <label class="flex items-center gap-2 text-slate-500">
                                <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-600">
                                Remember me
                            </label>
                            <span class="font-medium text-slate-400">Password reset not wired yet</span>
                        </div>

                        <button type="submit" class="brand-button w-full justify-center rounded-[1.25rem] py-4 text-base">
                            Log in
                        </button>
                    </form>

                    <p class="mt-6 text-center text-sm text-slate-500">
                        Not a member yet?
                        <a href="{{ route('signup') }}" class="font-semibold text-brand-700 hover:text-brand-800">Create an account</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
