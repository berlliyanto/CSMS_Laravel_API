<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class AssignNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

    protected $title;
    protected $message;
    public function __construct($title, $message)
    {
        $this->title = $title;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(notification: new FcmNotification(
            title: $this->title,
            body: $this->message,
            image: 'https://aplikasipms.com/api/images/icon.png',
        )));
        // ->data(['data1' => 'value', 'data2' => 'value2'])
        // ->custom([
        //     'android' => [
        //         'notification' => [
        //             'color' => '#0A0A0A',
        //         ],
        //         'fcm_options' => [
        //             'analytics_label' => 'analytics',
        //         ],
        //     ],
        //     'apns' => [
        //         'fcm_options' => [
        //             'analytics_label' => 'analytics',
        //         ],
        //     ],
        // ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
