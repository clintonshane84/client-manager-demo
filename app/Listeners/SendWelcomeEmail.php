<?php
namespace App\Listeners;

use App\Events\RegisteredClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Contracts\MailerFactoryInterface;

class SendWelcomeEmail implements ShouldQueue
{
    public function __construct(
        // DI this sucker, thank you zero configuration
        protected readonly MailerFactoryInterface $mailerFactory
        ) {}

    public function handle(RegisteredClient $event): void
    {
        // Be defensive: only send if client has an email.
        if (! empty($event->client->email)) {
            Mail::to($event->client->email)->send($this->mailerFactory->make('welcome-client', [$event->client]));
        }
    }
}
