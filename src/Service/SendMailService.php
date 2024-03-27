<?php

namespace App\Service;

use Twig\Environment;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendMailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer){
        $this->mailer = $mailer;
    }
    public function send(string $from, string $to, string $subject, string $template,array $context):void{
        //we create the e-mail
        $email = (new TemplatedEmail())
        ->from($from)
        ->to($to)
        ->subject($subject)
        ->htmlTemplate("emailS/$template.html.twig")
        ->context($context);

        //we send the e-mail
        $this->mailer->send($email);
    }

    public function sendPdf($from, $to, $subject, $template, array $context ,$data = []): void
{
    $email = (new TemplatedEmail())
        ->from($from)
        ->to($to)
        ->subject($subject)
        ->htmlTemplate("emailS/$template.html.twig");

    // Ajouter la pièce jointe PDF
    if (isset($data['pdfContent'])) {
        $pdfAttachment = new DataPart($data['pdfContent'], 'VotreDevis.pdf', 'application/pdf');
        $email->attach($pdfAttachment);
    }
    $this->mailer->send($email);
}
}