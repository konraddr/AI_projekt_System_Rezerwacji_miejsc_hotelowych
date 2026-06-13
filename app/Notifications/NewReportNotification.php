<?php

namespace App\Notifications;

use App\Models\Report;
use App\Notifications\Concerns\BuildsWebPushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewReportNotification extends Notification
{
    use BuildsWebPushMessage;
    use Queueable;

    public function __construct(
        public Report $report
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
        $this->report->loadMissing(['user', 'hotel']);

        $mail = (new MailMessage)
            ->subject('Nowe zgłoszenie'.$this->hotelSuffix())
            ->line($this->body());

        return $mail->action('Zobacz zgłoszenie', route('manage.reports.show', $this->report));
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
        $this->report->loadMissing(['user', 'hotel']);

        return [
            'report_id' => $this->report->id,
            'hotel_id' => $this->report->hotel_id,
            'title' => 'Nowe zgłoszenie',
            'message' => $this->body(),
            'url' => route('manage.reports.show', $this->report),
            'action_label' => 'Zobacz zgłoszenie',
        ];
    }

    private function body(): string
    {
        $reporter = $this->report->user->name;
        $excerpt = Str::limit($this->report->reason, 120);

        if ($this->report->hotel !== null) {
            return "{$reporter} zgłosił problem dotyczący hotelu {$this->report->hotel->name}: „{$excerpt}”";
        }

        return "{$reporter} wysłał nowe zgłoszenie: „{$excerpt}”";
    }

    private function hotelSuffix(): string
    {
        if ($this->report->hotel === null) {
            return '';
        }

        return ' — '.$this->report->hotel->name;
    }

    private function webPushEnabled(): bool
    {
        return filled(config('webpush.vapid.public_key'))
            && filled(config('webpush.vapid.private_key'));
    }
}
