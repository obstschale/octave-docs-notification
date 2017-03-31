<?php

namespace App\Mail;

use App\Version;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewOctaveVersion extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The version instance
     *
     * @var Version
     */
    public $version;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param Group $group
     */
    public function __construct(Version $version)
    {
        $this->version = $version;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('example@example.com')
                ->view('emails.newVersion');
    }
}
