<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Client;

class RegisteredClient
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Ensure listeners run only after the surrounding DB transaction commits.
     * (Supported in modern Laravel; safe to include.)
     */
    public bool $afterCommit = true;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Client $client
    ) {
    }
}
