<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LandingInquiry;
use App\Services\ContactChallengeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ContactInquiryController extends Controller
{
    public function __construct(private ContactChallengeService $challenges)
    {
    }

    public function challenge()
    {
        return response()->json($this->challenges->issue());
    }

    public function verifySquare(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|string|max:80',
        ]);

        $ok = $this->challenges->markSquareVerified($data['token']);

        if (!$ok) {
            return response()->json([
                'message' => 'Could not verify the security checkbox. Refresh and try again.',
            ], 422);
        }

        return response()->json(['verified' => true]);
    }

    public function store(Request $request)
    {
        $key = 'contact-submit:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many submissions from this network. Please try again later.',
            ], 429);
        }
        RateLimiter::hit($key, 600);

        // Honeypot — bots fill this; humans leave it blank
        if (filled($request->input('website'))) {
            return response()->json([
                'message' => 'Thanks! We will get back to you shortly.',
            ], 201);
        }

        $validated = $request->validate([
            'type' => 'required|in:contact,demo',
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:40',
            'company' => 'nullable|string|max:160',
            'subject' => 'nullable|string|max:180',
            'message' => 'required|string|max:5000',
            'challenge_token' => 'required|string|max:80',
            'challenge_answer' => 'required',
            'human_square' => 'accepted',
        ]);

        $check = $this->challenges->validate(
            $validated['challenge_token'],
            $validated['challenge_answer'],
            $request->boolean('human_square')
        );

        if (!$check['ok']) {
            return response()->json(['message' => $check['message']], 422);
        }

        $inquiry = LandingInquiry::create([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'subject' => $validated['subject'] ?? ($validated['type'] === 'demo' ? 'Book a Demo' : 'Contact'),
            'message' => $validated['message'],
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        return response()->json([
            'message' => $validated['type'] === 'demo'
                ? 'Demo request received. Our team will contact you soon.'
                : 'Message sent. We will get back to you shortly.',
            'id' => $inquiry->id,
        ], 201);
    }

    public function index(Request $request)
    {
        $query = LandingInquiry::query()->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }
        if ($request->filled('search')) {
            $search = '%' . $request->string('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('company', 'like', $search)
                    ->orWhere('message', 'like', $search);
            });
        }

        return response()->json([
            'inquiries' => $query->paginate(20),
            'unread_count' => LandingInquiry::where('status', 'new')->count(),
        ]);
    }

    public function show(LandingInquiry $inquiry)
    {
        $inquiry->markRead();

        return response()->json(['inquiry' => $inquiry->fresh()]);
    }

    public function update(Request $request, LandingInquiry $inquiry)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,read,archived',
        ]);

        $inquiry->update([
            'status' => $validated['status'],
            'read_at' => $validated['status'] === 'new' ? null : ($inquiry->read_at ?? now()),
        ]);

        return response()->json([
            'message' => 'Inquiry updated.',
            'inquiry' => $inquiry->fresh(),
        ]);
    }

    public function destroy(LandingInquiry $inquiry)
    {
        $inquiry->delete();

        return response()->json(['message' => 'Inquiry deleted.']);
    }
}
