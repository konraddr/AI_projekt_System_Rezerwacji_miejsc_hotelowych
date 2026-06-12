<?php

namespace App\Notifications;

use App\Enums\BookingNotificationEvent;
use App\Models\Booking;
use App\Notifications\Concerns\BuildsWebPushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class BookingStatusNotification extends Notification
{
    use BuildsWebPushMessage;
    use Queueable;

    public function __construct(
        public Booking $booking,
        public BookingNotificationEvent $event
    ) {}

    /**
     * @return array<int, string|class-string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database', 'mail'];

        if ($this->webPushEnabled()) {
            $channels[] = WebPushChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->event->title())
            ->line($this->event->body($this->booking))
            ->action('Zobacz rezerwację', route('bookings.show', $this->booking));
    }

    public function toWebPush(object $notifiable, self $notification): WebPushMessage
    {
        return $this->buildWebPushMessage($this->toArray($notifiable));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'event' => $this->event->value,
            'title' => $this->event->title(),
            'message' => $this->event->body($this->booking),
            'url' => route('bookings.show', $this->booking),
            'action_label' => 'Zobacz rezerwację',
        ];
    }

    private function webPushEnabled(): bool
    {
        return filled(config('webpush.vapid.public_key'))
            && filled(config('webpush.vapid.private_key'));
    }
}
