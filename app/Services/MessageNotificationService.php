<?php

namespace App\Services;

use App\Models\Message;
use App\Notifications\NewMessageNotification;
use Throwable;

class MessageNotificationService
{
    public function notifyReceiver(Message $message): void
    {
        $message->loadMissing(['receiver', 'sender', 'hotel']);

        if ($message->receiver === null) {
            return;
        }

        if ($message->sender_id === $message->receiver_id) {
            return;
        }

        if ($message->receiver->isBanned()) {
            return;
        }

        try {
            $message->receiver->notify(new NewMessageNotification($message));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
