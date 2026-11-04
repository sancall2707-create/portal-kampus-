<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    protected ?WebPush $webPush = null;

    public function __construct()
    {
        $publicKey = config('services.vapid.public_key');
        $privateKey = config('services.vapid.private_key');
        $subject = config('services.vapid.subject', 'mailto:admin@pangudiluhur.sch.id');

        if ($publicKey && $privateKey) {
            $this->webPush = new WebPush([
                'VAPID' => [
                    'subject' => $subject,
                    'publicKey' => $publicKey,
                    'privateKey' => $privateKey,
                ],
            ]);
            $this->webPush->setReuseVapidHeaders(true);
        }
    }

    public function isConfigured(): bool
    {
        return $this->webPush !== null;
    }

    public function sendToSubscription(PushSubscription $sub, array $payload): bool
    {
        if (!$this->webPush || empty($sub->endpoint)) {
            return false;
        }

        try {
            $subscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->public_key,
                'authToken' => $sub->auth_token,
                'contentEncoding' => $sub->content_encoding ?? 'aesgcm',
            ]);

            $this->webPush->queueNotification($subscription, json_encode($payload));
            $reports = $this->webPush->flush();

            $success = true;
            foreach ($reports as $report) {
                if (!$report->isSuccess()) {
                    $success = false;
                    Log::warning('Push notification failed: ' . $report->getReason());
                    if ($report->isSubscriptionExpired()) {
                        $sub->delete();
                    }
                }
            }

            return $success;
        } catch (\Throwable $e) {
            Log::error('Push send exception: ' . $e->getMessage());
            return false;
        }
    }

    public function sendToUser(User $user, array $payload): int
    {
        $sent = 0;
        foreach ($user->pushSubscriptions as $sub) {
            if ($this->sendToSubscription($sub, $payload)) {
                $sent++;
            }
        }
        return $sent;
    }

    public function sendToAll(array $payload): int
    {
        $sent = 0;
        $subs = PushSubscription::all();
        foreach ($subs as $sub) {
            if ($this->sendToSubscription($sub, $payload)) {
                $sent++;
            }
        }
        return $sent;
    }
}
