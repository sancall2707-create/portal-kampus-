<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushNotificationController extends Controller
{
    public function getKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => config('services.vapid.public_key'),
        ]);
    }

    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'nullable|string',
            'keys.auth' => 'nullable|string',
        ]);

        $sub = PushSubscription::updateOrCreate(
            ['endpoint' => $request->endpoint],
            [
                'user_id' => Auth::id(),
                'public_key' => $request->input('keys.p256dh'),
                'auth_token' => $request->input('keys.auth'),
                'content_encoding' => $request->input('contentEncoding', 'aesgcm'),
                'device_info' => $request->header('User-Agent'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Langganan notifikasi smartphone berhasil disimpan.',
            'id' => $sub->id,
        ]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate(['endpoint' => 'required|string']);

        PushSubscription::where('endpoint', $request->endpoint)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Langganan notifikasi berhasil dihapus.',
        ]);
    }

    public function sendTest(Request $request, WebPushService $webPush): JsonResponse
    {
        $user = Auth::user();
        $title = $request->input('title', 'SIAP PL Deltamas');
        $body = $request->input('body', 'Notifikasi berhasil terhubung ke smartphone Anda!');
        $url = $request->input('url', route('dashboard'));

        $payload = [
            'title' => $title,
            'body' => $body,
            'icon' => asset('icons/icon-192x192.png'),
            'badge' => asset('icons/icon-192x192.png'),
            'url' => $url,
            'timestamp' => now()->timestamp,
        ];

        $sentCount = 0;
        if ($user) {
            $sentCount = $webPush->sendToUser($user, $payload);
        }

        // If no user-specific subs or sending to direct subscription from request
        if ($sentCount === 0 && $request->filled('endpoint')) {
            $sub = PushSubscription::where('endpoint', $request->endpoint)->first();
            if ($sub) {
                if ($webPush->sendToSubscription($sub, $payload)) {
                    $sentCount++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'sent' => $sentCount,
            'message' => $sentCount > 0 
                ? "Notifikasi berhasil dikirimkan ke {$sentCount} perangkat smartphone."
                : "Belum ada perangkat smartphone yang terdaftar. Aktifkan izin notifikasi terlebih dahulu.",
        ]);
    }
}
