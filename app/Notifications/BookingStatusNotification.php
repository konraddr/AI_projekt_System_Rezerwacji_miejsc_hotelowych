<?php

namespace App\Notifications;

use App\Enums\BookingNotificationEvent;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public BookingNotificationEvent $event
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
        return (new MailMessage)
            ->subject($this->event->title())
            ->line($this->event->body($this->booking))
            ->action('Zobacz rezerwację', route('bookings.show', $this->booking));
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
        ];
    }
}
