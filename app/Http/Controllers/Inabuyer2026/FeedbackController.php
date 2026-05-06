<?php

namespace App\Http\Controllers\Inabuyer2026;

use App\Http\Controllers\Controller;
use App\Models\InabuyerMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function create(): View
    {
        return view('inabuyer2026.feedback');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'organization' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'kesan_dan_pesan' => ['required', 'string', 'max:5000'],
        ]);

        InabuyerMessage::create([
            'name' => trim($validated['name'] ?? ''),
            'organization' => isset($validated['organization']) ? trim($validated['organization']) : null,
            'position' => isset($validated['position']) ? trim($validated['position']) : null,
            'phone' => isset($validated['phone']) ? trim($validated['phone']) : null,
            'email' => isset($validated['email']) ? trim($validated['email']) : null,
            'kesan_dan_pesan' => trim($validated['kesan_dan_pesan']),
        ]);

        return redirect()
            ->route('inabuyer2026.feedback')
            ->with('success', 'Terima kasih. Kesan dan pesan Anda telah kami terima.');
    }
}
