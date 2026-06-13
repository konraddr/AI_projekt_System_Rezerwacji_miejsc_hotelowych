<?php

namespace App\Notifications\Concerns;

use NotificationChannels\WebPush\WebPushMessage;

trait BuildsWebPushMessage
{
    /**
     * @param  array<string, mixed>  $payload
     */
    protected function buildWebPushMessage(array $payload): WebPushMessage
    {
        $message = (new WebPushMessage)
            ->title((string) ($payload['title'] ?? config('app.name')))
            ->body((string) ($payload['message'] ?? ''));

        $url = $payload['url'] ?? null;

        if (is_string($url) && $url !== '') {
            $message
                ->action((string) ($payload['action_label'] ?? 'Otwórz'), 'open')
                ->data(['url' => $url]);
        }

        return $message;
    }
}
