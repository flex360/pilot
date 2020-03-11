<?php

namespace Flex360\Pilot\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Flex360\Pilot\Pilot\Forms\FormHandler;

class FormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $handler;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(FormHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->handler->getSubject();
        
        return $this->view('emails.forms.submission', compact('subject'));
    }
}
