<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;


class TokenGenerated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $token;
    private $user;
    public $appType;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $token, User $user, string $appType)
    {

        $this->token = $token;
        $this->user = $user;
        $this->appType = $appType;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */

    public function envelope()
    {
        return new Envelope(
            from: new Address('noreply@thegameark.com', $this->appType),
            subject: "$this->appType: Reset Password",
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.users.token',
            with: [
                'username' => $this->user->username,
                'year' => Carbon::now()->year,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }

 
}
