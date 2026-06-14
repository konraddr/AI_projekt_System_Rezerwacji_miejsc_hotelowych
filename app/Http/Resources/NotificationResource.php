<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Illuminate\Notifications\DatabaseNotification */
class NotificationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->data;

        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $data['title'] ?? 'Powiadomienie',
            'message' => $data['message'] ?? '',
            'url' => $data['url'] ?? null,
            'action_label' => $data['action_label'] ?? null,
            'event' => $data['event'] ?? null,
            'booking_id' => $data['booking_id'] ?? null,
            'is_read' => $this->read_at !== null,
            'read_at' => $this->read_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
