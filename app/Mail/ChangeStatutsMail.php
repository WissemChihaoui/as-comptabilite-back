<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChangeStatutsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageContent;
    public $status;
    public $statusLabel;

    public function __construct($messageContent, $status)
    {
        $this->messageContent = $messageContent;
        $this->status         = $status;

        $labels = [
            'accepted' => 'Accepté',
            'rejected' => 'Rejeté',
            'pending'  => 'En attente',
            'review'   => 'En cours d\'examen',
        ];

        $this->statusLabel = $labels[$status] ?? ucfirst($status);
    }

    public function build()
    {
        return $this->subject('Mise à jour du statut de votre formulaire')
            ->view('mails.changeStatus');
    }
}
