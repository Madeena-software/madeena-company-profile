@extends('layouts.app')

@section('title', 'Kesan dan Pesan Booth Madeena Inabuyer 2026')
@section('description', 'Sampaikan kesan dan pesan Anda untuk booth Madeena di Inabuyer 2026 melalui formulir mobile-friendly Madeena.')

@section('content')
    <section
        class="relative overflow-hidden bg-gradient-to-br from-madeena-blue via-slate-950 to-madeena-teal pt-28 pb-20 text-white">
        <div class="absolute inset-0 opacity-30">
            <div class="absolute -top-24 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 h-96 w-96 rounded-full bg-madeena-teal/30 blur-3xl"></div>
        </div>

        <div class="relative mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8">
            <div class="flex flex-col justify-center gap-6">
                <span
                    class="inline-flex w-fit items-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold tracking-wide text-white/90 backdrop-blur">
                    Madeena booth Inabuyer 2026 Feedback
                </span>

                <div class="space-y-4">
                    <h1 class="max-w-2xl text-4xl font-black leading-tight sm:text-5xl lg:text-6xl">
                        Kesan dan Pesan Anda untuk booth Madeena di Inabuyer 2026
                    </h1>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                        <p class="text-3xl font-bold">01</p>
                        <p class="mt-2 text-sm text-white/75">Isi nama dan organisasi dengan cepat.</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                        <p class="text-3xl font-bold">02</p>
                        <p class="mt-2 text-sm text-white/75">Gunakan diktasi suara untuk menulis pesan.</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                        <p class="text-3xl font-bold">03</p>
                        <p class="mt-2 text-sm text-white/75">Tim kami memantau semua masukan di dashboard.</p>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div
                    class="rounded-[2rem] border border-white/15 bg-white/95 p-5 shadow-2xl shadow-slate-950/30 text-slate-900 sm:p-8">
                    <div class="mb-6 space-y-2">
                        <h2 class="text-2xl font-bold text-slate-950">Form Feedback Booth Madeena</h2>
                        <p class="text-sm leading-6 text-slate-600">Kolom dibuat lebar, kontras tinggi, dan ramah input
                            suara.</p>
                    </div>

                    @if (session('success'))
                        <div
                            class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            Mohon periksa kembali isian Anda sebelum mengirimkan formulir.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('inabuyer2026.feedback.store') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nama</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name"
                                autocapitalize="words" inputmode="text" enterkeyhint="next" spellcheck="false"
                                class="h-14 w-full rounded-2xl border border-slate-300 bg-white px-4 text-base text-slate-900 shadow-sm transition focus:border-madeena-teal focus:ring-4 focus:ring-madeena-teal/15"
                                placeholder="Nama lengkap">
                            @error('name')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="organization"
                                class="mb-2 block text-sm font-semibold text-slate-700">Organisasi</label>
                            <input id="organization" name="organization" type="text" value="{{ old('organization') }}" required
                                autocomplete="organization" autocapitalize="words" inputmode="text" enterkeyhint="next"
                                spellcheck="false"
                                class="h-14 w-full rounded-2xl border border-slate-300 bg-white px-4 text-base text-slate-900 shadow-sm transition focus:border-madeena-teal focus:ring-4 focus:ring-madeena-teal/15"
                                placeholder="Nama perusahaan atau instansi">
                            @error('organization')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="position" class="mb-2 block text-sm font-semibold text-slate-700">Jabatan</label>
                            <input id="position" name="position" type="text" value="{{ old('position') }}" required
                                autocomplete="organization-title" autocapitalize="words" inputmode="text"
                                enterkeyhint="next" spellcheck="false"
                                class="h-14 w-full rounded-2xl border border-slate-300 bg-white px-4 text-base text-slate-900 shadow-sm transition focus:border-madeena-teal focus:ring-4 focus:ring-madeena-teal/15"
                                placeholder="Jabatan atau peran">
                            @error('position')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="mb-2 block text-sm font-semibold text-slate-700">Nomor yang bisa
                                dihubungi</label>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required
                                autocomplete="tel" inputmode="tel" enterkeyhint="next" spellcheck="false"
                                class="h-14 w-full rounded-2xl border border-slate-300 bg-white px-4 text-base text-slate-900 shadow-sm transition focus:border-madeena-teal focus:ring-4 focus:ring-madeena-teal/15"
                                placeholder="Nomor WhatsApp atau telepon">
                            @error('phone')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                autocomplete="email" inputmode="email" enterkeyhint="next" spellcheck="false"
                                class="h-14 w-full rounded-2xl border border-slate-300 bg-white px-4 text-base text-slate-900 shadow-sm transition focus:border-madeena-teal focus:ring-4 focus:ring-madeena-teal/15"
                                placeholder="nama@email.com">
                            @error('email')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="kesan_dan_pesan" class="mb-2 block text-sm font-semibold text-slate-700">Kesan dan
                                Pesan</label>
                            <textarea id="kesan_dan_pesan" name="kesan_dan_pesan" rows="8" required maxlength="5000"
                                autocomplete="off" autocapitalize="sentences" spellcheck="true" enterkeyhint="done"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-4 text-base leading-7 text-slate-900 shadow-sm transition focus:border-madeena-teal focus:ring-4 focus:ring-madeena-teal/15"
                                placeholder="Ceritakan pengalaman, masukan, atau harapan Anda untuk Booth Madeena di Inabuyer 2026">{{ old('kesan_dan_pesan') }}</textarea>
                            @error('kesan_dan_pesan')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs leading-5 text-slate-500">Gunakan dikte suara jika lebih nyaman.</p>
                        </div>

                        <button type="submit"
                            class="inline-flex h-14 w-full items-center justify-center rounded-2xl bg-madeena-blue px-6 text-base font-semibold text-white shadow-lg shadow-madeena-blue/25 transition hover:-translate-y-0.5 hover:bg-opacity-95 focus:outline-none focus:ring-4 focus:ring-madeena-blue/25">
                            Kirim ke Booth Madeena
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
