{{-- sections/contact.blade.php --}}
@php $contact = $section['contact'] ?? $contactInfo ?? []; @endphp
<section id="{{ $data['section_id'] ?? 'kontak' }}" class="py-20 bg-gradient-to-br from-madeena-blue to-teal-800 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-block bg-white/10 text-white font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Kontak</span>
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">{{ $data['section_title'] ?? 'Hubungi Kami' }}</h2>
        @if(!empty($data['section_subtitle']))
        <p class="text-white/80 text-lg mb-10">{{ $data['section_subtitle'] }}</p>
        @else
        <p class="text-white/80 text-lg mb-10">Untuk informasi lebih lanjut mengenai produk dan layanan kami, silakan menghubungi kami melalui saluran berikut</p>
        @endif
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            @if(!empty($contact['email']))
            <a href="mailto:{{ $contact['email'] }}"
               class="bg-white/10 hover:bg-white/20 transition-colors rounded-xl p-5 text-center border border-white/20">
                <i class="fas fa-envelope text-2xl text-madeena-teal mb-3 block"></i>
                <div class="text-sm font-medium">Email</div>
                <div class="text-white/70 text-xs mt-1">{{ $contact['email'] }}</div>
            </a>
            @endif
            @if(!empty($contact['phone']))
            <a href="tel:{{ preg_replace('/\s/', '', $contact['phone']) }}"
               class="bg-white/10 hover:bg-white/20 transition-colors rounded-xl p-5 text-center border border-white/20">
                <i class="fas fa-phone text-2xl text-madeena-teal mb-3 block"></i>
                <div class="text-sm font-medium">Telepon</div>
                <div class="text-white/70 text-xs mt-1">{{ $contact['phone'] }}</div>
            </a>
            @endif
            @if(!empty($contact['whatsapp']))
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $contact['whatsapp']) }}" target="_blank"
               class="bg-white/10 hover:bg-white/20 transition-colors rounded-xl p-5 text-center border border-white/20">
                <i class="fab fa-whatsapp text-2xl text-madeena-teal mb-3 block"></i>
                <div class="text-sm font-medium">WhatsApp</div>
                <div class="text-white/70 text-xs mt-1">{{ $contact['whatsapp'] }}</div>
            </a>
            @endif
            @if(!empty($contact['address']))
            <div class="bg-white/10 rounded-xl p-5 text-center border border-white/20">
                <i class="fas fa-map-marker-alt text-2xl text-madeena-teal mb-3 block"></i>
                <div class="text-sm font-medium">Alamat</div>
                <div class="text-white/70 text-xs mt-1">{{ $contact['address'] }}</div>
            </div>
            @endif
        </div>
        @if(!empty($contact['whatsapp']))
        <a href="https://wa.me/{{ preg_replace('/\D/', '', $contact['whatsapp']) }}" target="_blank"
           class="btn-primary text-lg inline-flex items-center gap-2">
            <i class="fab fa-whatsapp"></i> Chat via WhatsApp
        </a>
        @endif
    </div>
</section>
