<?php

namespace App\Services;

use App\Models\Message;
use App\Notifications\NewMessageNotification;

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

        $message->receiver->notify(new NewMessageNotification($message));
    }
}
