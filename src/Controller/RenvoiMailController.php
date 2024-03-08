<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\JWTService;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RenvoiMailController extends AbstractController
{

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser($token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $em):Response
    {
        //we verify if the token is valid, if it's not expired and if it wasn't modified 
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            //we take the payload 
            $payload =$jwt->getPayload($token);
            //we take the user of the token
            $user = $userRepository->find($payload['user_id']);

            //we verify if the user exists and if he didn't activate his account
            if ($user && !$user->getIsVerified()) {
                $user->setIsVerified(true);
                $em->flush($user);
                $this->addFlash('success', 'Votre compte est activé');
                return $this->redirectToRoute('auth_oauth_login');
            }
            if ($user && $user->getIsVerified()) {
                $this->addFlash('success', 'Votre compte est déja activé');
                return $this->redirectToRoute('auth_oauth_login');
            }
        }
        //here we have a problem in the token
        return $this->render('user/mail.html.twig');
    }


    #[Route('/renvoiverif', name: 'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail, UserRepository $userRepository):Response{

        $user = $this->getUser();

        if(!$user){
            $this->addFlash('danger', 'vous devez être connecté pour accéder à cette page' );
            return $this->redirectToRoute('auth_oauth_login');
        }
        if ($user->getIsVerified()) {
            $this->addFlash('warning', 'Cet utilisateur est déja activé');
            return $this->redirectToRoute('auth_oauth_login'); 
        }
        //We generate the jwt of the user
            //We create the header
            $header =[
                'typ'=>'JWT',
                'alg'=>'HS256'
            ];
            //We create the payload
            $payload =[
                'user_id'=>$user->getId()
            ];
            //We generate the token
            $token = $jwt->generate($header,$payload,$this->getParameter('app.jwtsecret'));

            $mail->send ('no-reply@cleanthis.fr',
                $user->getEmail(),
                'Activation de votre compte CleanThis',
                'register',
                compact('user','token')
            );
            $this->addFlash('warning', 'E-mail de vérification envoyé');
            return $this->redirectToRoute('auth_oauth_login'); 
    }
}

