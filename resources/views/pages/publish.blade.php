@php($title = 'Publish a Ride')

@extends('layouts.app')

@section('content')
    <section class="py-12">
        <div class="shell page-enter">
            <div class="mx-auto max-w-6xl">
                <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">
                    <div class="surface overflow-hidden">
                        <div class="bg-gradient-to-r from-brand-600 to-brand-700 px-8 py-8 text-white">
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-white/70">Driver flow</p>
                            <h1 class="mt-3 text-4xl font-black tracking-tight">Publish a ride</h1>
                            <p class="mt-3 max-w-2xl text-white/80">This page translates your mockup into a real Laravel surface so the ride-posting flow can be connected next.</p>
                        </div>

                        <form class="grid gap-6 p-8 lg:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">From</span>
                                <select class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                    @foreach ($cities as $city)
                                        <option>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">To</span>
                                <select class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                    @foreach ($cities as $city)
                                        <option>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Date</span>
                                <input type="date" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Departure time</span>
                                <input type="time" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Seats offered</span>
                                <select class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                                    @foreach ([1, 2, 3, 4] as $seatCount)
                                        <option>{{ $seatCount }} {{ \Illuminate\Support\Str::plural('seat', $seatCount) }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Price per seat (DH)</span>
                                <input type="number" placeholder="70" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                            </label>

                            <label class="space-y-2 lg:col-span-2">
                                <span class="text-sm font-semibold text-slate-700">Meeting point</span>
                                <input type="text" placeholder="Casa Voyageurs taxi lane" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none">
                            </label>

                            <label class="space-y-2 lg:col-span-2">
                                <span class="text-sm font-semibold text-slate-700">Notes</span>
                                <textarea rows="5" placeholder="Luggage policy, flexible pickup, or other details" class="w-full rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium text-slate-700 outline-none"></textarea>
                            </label>

                            <div class="lg:col-span-2">
                                <button type="button" class="brand-button w-full justify-center rounded-[1.4rem] py-4 text-base">
                                    Publish my ride
                                </button>
                                <p class="mt-4 text-center text-sm leading-6 text-slate-500">
                                    Frontend surface implemented. Hook this form to the ride-posting service/controller next.
                                </p>
                            </div>
                        </form>
                    </div>

                    <aside class="space-y-6">
                        <div class="surface-soft p-6">
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Design carry-over</p>
                            <p class="mt-4 text-sm leading-6 text-slate-600">
                                This page keeps the rounded, calm, sky-blue form treatment from your HTML maquettage while moving it into the Laravel asset pipeline.
                            </p>
                        </div>

                        <div class="surface-soft p-6">
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Related routes</p>
                            <div class="mt-4 space-y-3">
                                <a href="{{ route('dashboards.driver') }}" class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                    Driver dashboard
                                </a>
                                <a href="{{ route('rides.search') }}" class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-brand-200 hover:text-brand-700">
                                    Search results
                                </a>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection
