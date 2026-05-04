<div wire:poll.5s class="relative flex h-screen w-full flex-col overflow-hidden bg-slate-950 font-sans text-white">
    <!-- Ambient Background Effects -->
    <div class="absolute inset-0 z-0 overflow-hidden opacity-40">
        <div
            class="absolute -top-[20%] left-1/2 h-[50vh] w-[80vw] -translate-x-1/2 rounded-[100%] bg-madeena-blue/20 blur-[120px]">
        </div>
        <div class="absolute top-[40%] -left-[20%] h-[40vh] w-[60vw] rounded-[100%] bg-madeena-teal/15 blur-[100px]">
        </div>
        <div class="absolute -bottom-[10%] -right-[10%] h-[50vh] w-[70vw] rounded-[100%] bg-indigo-500/10 blur-[120px]">
        </div>
    </div>

    <!-- Header Section (Top) -->
    <div
        class="relative z-10 flex shrink-0 flex-col items-center justify-center gap-4 bg-white/5 px-8 py-10 shadow-2xl shadow-black/50 backdrop-blur-xl border-b border-white/10">
        <img src="{{ asset('images/logo-current.png') }}" alt="Madeena Logo"
            class="h-20 object-contain drop-shadow-2xl">
        <div class="text-center">
            <h1
                class="text-4xl font-black uppercase tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-200 to-white/70">
                Booth Madeena
            </h1>
            <p class="mt-2 text-xl font-medium tracking-wide text-madeena-teal">
                Inabuyer 2026 | Live Impressions & Messages | <span class="text-white/50">{{ now()->format('H:i:s') }}</span>
            </p>
        </div>
    </div>

    <!-- Main Content Area (Messages List) -->
    <div class="relative z-10 flex flex-1 flex-col justify-start gap-6 overflow-y-auto min-h-0 px-8 py-8 pb-32">
        @forelse ($messages as $message)
            <div wire:key="{{ $message->id }}" class="bg-white/5 border border-white/10 rounded-3xl p-8 shadow-xl backdrop-blur-md">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between border-b border-white/10 pb-4">
                        <h2 class="text-3xl font-bold text-white">
                            {{ $message->name }}
                            @if($message->organization)
                                <span class="text-xl font-normal text-madeena-teal ml-2">dari {{ $message->organization }}</span>
                            @endif
                        </h2>
                        <span class="text-sm font-semibold tracking-wider text-white/40 uppercase">
                            {{ $message->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <div class="pt-2">
                        <p class="text-4xl leading-relaxed text-white font-black">
                            {{ $message->kesan_dan_pesan }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex h-full flex-col items-center justify-center text-center opacity-60">
                <i class="fas fa-comment-dots mb-6 text-7xl text-madeena-teal"></i>
                <p class="text-3xl font-semibold">Belum ada pesan yang masuk.</p>
                <p class="mt-3 text-xl">Jadilah yang pertama untuk memberikan kesan dan pesan!</p>
            </div>
        @endforelse
    </div>

    <!-- Footer Section (Bottom Call to Action) -->
    <div class="relative z-10 shrink-0 bg-gradient-to-t from-slate-950 via-slate-950/90 to-transparent pt-12 pb-8">
        <div
            class="mx-8 grid gap-6 rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-lg lg:grid-cols-[auto,1fr,auto] lg:items-center">
            <div class="flex items-center gap-5">
                <div class="rounded-2xl border border-white/10 bg-white p-3 shadow-2xl shadow-black/30">
                    <img
                        src="{{ asset('images/' . rawurlencode('qr_Kesan dan Pesan Booth Madeena Inabuyer 2026.png')) }}"
                        alt="QR code feedback booth Madeena Inabuyer 2026"
                        class="h-28 w-28 object-contain sm:h-32 sm:w-32">
                </div>
                <div class="space-y-2">
                    <p class="text-sm font-semibold uppercase tracking-[0.35em] text-madeena-teal/80">Kesan dan pesan</p>
                    <h2 class="text-3xl font-black text-white sm:text-4xl">Scan untuk kirim kesan dan pesan</h2>
                    <p class="max-w-2xl text-lg text-white/75">Bantu kami meningkatkan pengalaman Booth Madeena. Buka link berikut atau scan QR code di samping.</p>
                </div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-950/60 px-5 py-4 shadow-inner shadow-black/20">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-white/40">Kesan dan Pesan URL</p>
                <a href="https://bit.ly/madeenafeedback" target="_blank" rel="noreferrer"
                    class="mt-2 block break-all text-2xl font-black text-white transition hover:text-madeena-teal sm:text-3xl">
                    https://bit.ly/madeenafeedback
                </a>
            </div>

            <a href="https://bit.ly/madeenafeedback" target="_blank" rel="noreferrer"
                class="inline-flex items-center justify-center rounded-full border border-madeena-teal/40 bg-madeena-teal/15 px-6 py-4 text-lg font-bold text-white transition hover:border-madeena-teal hover:bg-madeena-teal/25">
                Buka Form Feedback
            </a>
        </div>
    </div>
</div>