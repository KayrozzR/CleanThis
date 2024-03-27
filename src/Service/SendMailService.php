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
    
    public function sendDevis($from, $to, $subject, $template, $client, $pdfContent): void
    {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("emailS/$template.html.twig")
            ->context([
                'client' => $client,
            ]);
    
        // we attach the pdf 
        $email->attach($pdfContent, 'VotreDevis.pdf', 'application/pdf');
    
        $this->mailer->send($email);
    }

}