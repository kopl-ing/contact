<?php

declare(strict_types=1);

namespace Kopling\Contact\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Kopling\Contact\Message;
use Kopling\Core\Settings\Settings;

class NewMessage extends Mailable
{
    public function __construct(public Message $contactMessage)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('kopling-contact::messages.mail.subject', [
                'site' => Settings::get('kopling-core::community-name', config('app.name')),
                'subject' => $this->contactMessage->subjectLabel(),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'kopling-contact::mail.new-message',
            with: [
                'subject' => $this->contactMessage->subjectLabel(),
                'context' => $this->contactMessage->context,
                'url' => route('kopling-admin::admin/contact.show', $this->contactMessage),
            ],
        );
    }
}
