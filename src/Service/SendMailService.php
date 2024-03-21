<?php

namespace App\Service;

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


    // public function sendPdf($from, $to, $subject, $template, $data = [], $attachments = [])
    // {
    //     // Créer le message
    //     $message = (new \Swift_Message($subject))
    //         ->setFrom($from)
    //         ->setTo($to)
    //         ->setBody(
    //             $this->templating->render($template.'.html.twig', $data),
    //             'text/html'
    //         );

    //     // Ajouter des pièces jointes
    //     foreach ($attachments as $attachment) {
    //         $message->attach(
    //             \Swift_Attachment::fromPath($attachment['path'])->setFilename($attachment['filename'])
    //         );
    //     }

    //     $this->mailer->send($message);
    // }
}