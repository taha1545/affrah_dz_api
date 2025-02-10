<?php

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class Notify
{
    private $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(__DIR__ . '/notify-d5f50-firebase-adminsdk-fbsvc-45c641ec97.json');
        $this->messaging = $factory->createMessaging();
    }

    public function sendFCM($token, $title, $body)
    {
        $notification = Notification::create($title, $body);
        $message = CloudMessage::withTarget('token', $token)->withNotification($notification);

        try {
            $this->messaging->send($message);
            return ['success' => true, 'message' => 'Notification sent successfully.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}


