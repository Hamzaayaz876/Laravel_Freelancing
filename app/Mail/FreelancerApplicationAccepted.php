<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FreelancerApplicationAccepted extends Mailable
{
    use Queueable, SerializesModels;

    public $freelancerName;
    public $clientCompanyName;
    public $clientEmail;

    /**
     * Create a new message instance.
     */
    public function __construct($freelancerName, $clientCompanyName, $clientEmail)
    {
        $this->freelancerName = $freelancerName;
        $this->clientCompanyName = $clientCompanyName;
        $this->clientEmail = $clientEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Freelancer Application Accepted')
                    ->view('freelancer-application-accepted');
    }
}
