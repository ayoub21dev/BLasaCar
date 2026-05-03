@php($title = 'Become a Driver')

@extends('layouts.app')

@section('content')
    <section class="py-8 sm:py-12">
        <div class="shell page-enter">
            <div class="mx-auto max-w-5xl">
                <div>
                    <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-8 py-12 sm:px-16 sm:py-16">
                            <div class="inline-flex items-center gap-2 rounded-full bg-brand-100 px-4 py-2 text-[12px] font-black uppercase tracking-widest text-brand-600 mb-6">
                                Driver onboarding
                            </div>
                            <h1 class="text-[3rem] sm:text-[4.5rem] font-black text-slate-900 leading-[0.95] tracking-tight">
                                Become a <span class="italic font-serif text-brand-500">driver</span>.
                            </h1>
                            <p class="mt-6 max-w-2xl text-lg text-slate-500">
                                Your account starts as a traveler. Add your identity and first vehicle to unlock ride publishing.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('drivers.onboarding.store') }}" class="grid gap-6 p-5 sm:p-8 lg:grid-cols-2">
                            @csrf

                            <label class="space-y-2 lg:col-span-2">
                                <span class="text-sm font-semibold text-slate-700">CIN number</span>
                                <input type="text" name="cin_number" value="{{ old('cin_number') }}" placeholder="BE123456" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                @error('cin_number')
                                    <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Vehicle brand</span>
                                <input type="text" name="vehicle_brand" value="{{ old('vehicle_brand') }}" placeholder="Dacia" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                @error('vehicle_brand')
                                    <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Vehicle model</span>
                                <input type="text" name="vehicle_model" value="{{ old('vehicle_model') }}" placeholder="Logan" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                @error('vehicle_model')
                                    <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </label>

                            <div class="lg:col-span-2">
                                <button type="submit" class="brand-button w-full justify-center rounded-[1.4rem] py-4 text-base">
                                    Create driver profile
                                </button>
                                <p class="mt-4 text-center text-sm leading-6 text-slate-500">
                                    CIN verification starts as pending. Publishing is available after the driver profile and vehicle are created.
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
