@php($title = 'Sign Up')

@extends('layouts.app')

@section('content')
    <section class="py-16">
        <div class="shell page-enter">
            <div class="mx-auto max-w-md">
                <div class="surface p-8 sm:p-10">
                    <div class="text-center">
                        <img src="{{ asset('assets/logoBlasaCar.png') }}" alt="BlassaCar logo" class="mx-auto h-14 w-auto object-contain">
                        <h1 class="mt-8 text-3xl font-black text-slate-950">Create an account</h1>
                        <p class="mt-3 text-slate-500">Join the community and start saving on your next trip.</p>
                    </div>

                    <form method="POST" action="{{ route('signup.store') }}" class="mt-8 space-y-4">
                        @csrf

                        <div class="input-shell">
                            <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="Full name" class="w-full bg-transparent text-sm font-medium text-slate-700 outline-none">
                        </div>
                        @error('full_name')
                            <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="input-shell">
                            <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Phone number" class="w-full bg-transparent text-sm font-medium text-slate-700 outline-none">
                        </div>
                        @error('phone')
                            <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="input-shell">
                            <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect width="20" height="16" x="2" y="4" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email address" class="w-full bg-transparent text-sm font-medium text-slate-700 outline-none">
                        </div>
                        @error('email')
                            <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="input-shell">
                            <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input type="password" name="password" placeholder="Create a password" class="w-full bg-transparent text-sm font-medium text-slate-700 outline-none">
                        </div>
                        @error('password')
                            <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="brand-button w-full justify-center rounded-[1.25rem] py-4 text-base">
                            Sign up
                        </button>
                    </form>

                    <p class="mt-6 text-center text-sm text-slate-500">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:text-brand-800">Log in</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
