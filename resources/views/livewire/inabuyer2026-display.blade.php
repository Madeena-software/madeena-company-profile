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
                Inabuyer 2026 | Live Impressions & Messages
            </p>
        </div>
    </div>

    <!-- Main Content Area (Messages List) -->
    <div class="relative z-10 flex flex-1 flex-col justify-start gap-6 overflow-hidden px-8 py-8">
        @forelse ($messages as $message)
            <div wire:key="{{ $message->id }}"
                class="group relative overflow-hidden rounded-3xl border border-white/10 bg-white/5 px-8 py-6 shadow-xl backdrop-blur-md transition-all duration-700 ease-in-out hover:bg-white/10">
                <!-- Decorative Accent Line -->
                <div
                    class="absolute left-0 top-0 h-full w-1.5 bg-gradient-to-b from-madeena-teal to-madeena-blue opacity-80">
                </div>

                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white">{{ $message->name }}</h2>
                        @if($message->organization)
                            <p class="mt-1 text-lg font-medium text-madeena-teal">{{ $message->organization }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-semibold tracking-wider text-white/40 uppercase">
                            {{ $message->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                <p class="text-2xl leading-relaxed text-slate-200 font-medium">
                    "{{ $message->kesan_dan_pesan }}"
                </p>
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
            class="mx-8 rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-lg flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-3xl font-black text-white">Bagikan Kesan untuk Booth Madeena!</span>
            </div>
        </div>
    </div>
</div>