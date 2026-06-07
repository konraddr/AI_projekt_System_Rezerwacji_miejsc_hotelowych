<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Message $message
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->message->loadMissing(['sender', 'hotel']);

        return (new MailMessage)
            ->subject('Nowa wiadomość — '.$this->message->hotel->name)
            ->line($this->body())
            ->action('Otwórz czat', route('manage.hotels.chat', $this->message->hotel));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->message->loadMissing(['sender', 'hotel']);

        return [
            'message_id' => $this->message->id,
            'hotel_id' => $this->message->hotel_id,
            'title' => 'Nowa wiadomość',
            'message' => $this->body(),
            'url' => route('manage.hotels.chat', $this->message->hotel),
            'action_label' => 'Otwórz czat',
        ];
    }

    private function body(): string
    {
        $sender = $this->message->sender->name;
        $hotel = $this->message->hotel->name;
        $excerpt = Str::limit($this->message->content, 120);

        return "{$sender} napisał w czacie hotelu {$hotel}: „{$excerpt}”";
    }
}
