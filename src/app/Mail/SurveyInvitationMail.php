<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SurveyInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $surveyUrl;
    public $recipient;
    public $startDate;
    public $endDate;

    /**
     * Create a new message instance.
     *
     * @param  string  $surveyUrl
     * @param  User  $recipient
     * @param  string  $startDate
     * @param  string  $endDate
     * @return void
     */
    public function __construct($surveyUrl, $recipient, $startDate, $endDate)
    {
        $this->surveyUrl = $surveyUrl;
        $this->recipient = $recipient;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'アンケートのご協力お願い',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.survey_invitation',
            with: [
                'surveyUrl' => $this->surveyUrl . '?user=' . $this->recipient->id,
                'recipient' => $this->recipient,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
