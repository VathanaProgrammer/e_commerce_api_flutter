<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string|max:255'
        ]);

        $newsletter = Newsletter::where('email', $request->email)->first();

        if ($newsletter) {
            if ($newsletter->is_subscribed) {
                return response()->json([
                    'success' => false,
                    'message' => 'This email is already subscribed'
                ], 400);
            }

            $newsletter->update([
                'is_subscribed' => true,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
                'name' => $request->name ?? $newsletter->name
            ]);
        } else {
            $newsletter = Newsletter::create([
                'email' => $request->email,
                'name' => $request->name,
                'is_subscribed' => true,
                'subscribed_at' => now(),
                'verification_token' => Str::random(64)
            ]);
        }

        // Here you would send a verification email
        // Mail::to($newsletter->email)->send(new NewsletterVerification($newsletter));

        return response()->json([
            'success' => true,
            'message' => 'Successfully subscribed to newsletter',
            'data' => $newsletter
        ]);
    }

    public function unsubscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $newsletter = Newsletter::where('email', $request->email)->first();

        if (!$newsletter || !$newsletter->is_subscribed) {
            return response()->json([
                'success' => false,
                'message' => 'Email not found in subscription list'
            ], 404);
        }

        $newsletter->update([
            'is_subscribed' => false,
            'unsubscribed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully unsubscribed from newsletter'
        ]);
    }

    public function verify($token)
    {
        $newsletter = Newsletter::where('verification_token', $token)->first();

        if (!$newsletter) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification token'
            ], 404);
        }

        $newsletter->update([
            'verified_at' => now(),
            'verification_token' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully'
        ]);
    }
}
