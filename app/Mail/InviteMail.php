<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Invite;

class InviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invite;
    public $inviterName;

    public function __construct(Invite $invite, $inviterName)
    {
        $this->invite = $invite;
        $this->inviterName = $inviterName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You Have Been Invited! 🎉',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invite',
        );
    }
}