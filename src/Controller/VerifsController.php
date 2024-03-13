<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\JWTService;
use App\Service\RandomPassword;
use Doctrine\ORM\EntityManager;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use App\Form\ResetPasswordFormType;
use App\Form\CreatePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class VerifsController extends AbstractController
{

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser($token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $em, Request $request,SendMailService $mail,TokenGeneratorInterface $tokenGenerator):Response
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
                // Generate a reainitialisation token
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $em->persist($user);
                $em->flush();

                // Generate a mail reinitialisation link
                $url = $this->generateUrl('reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                // Create the mail datas
                $context = compact('url', 'user');

                // send the e-mail
                $mail->send(
                    'user@example.com', 
                    $user->getEmail(),
                    'Création de votre mot de passe CleanThis',
                    'password_create', 
                    $context
                );
                $this->addFlash('success', 'Votre compte est activé! Un e-mail vous a été envoyé pour la création de votre mot de passe');
                return $this->redirectToRoute('app_home');
            }
            if ($user && $user->getIsVerified()) {
                $this->addFlash('success', 'Votre compte est déja activé');
                return $this->render('security/redirect_login.html.twig');
            }
        }
        //here we have a problem in the token
        return $this->render('security/mail.html.twig');
    }


    #[Route('/verifEmployee/{token}', name: 'verify_employee')]
    public function verifyEmployee($token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $em, Request $request,SendMailService $mail,TokenGeneratorInterface $tokenGenerator, RandomPassword $randomPassword,UserPasswordHasherInterface $userPasswordHasherInterface):Response
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
                // Generate a reainitialisation token
                $token = $tokenGenerator->generateToken();
                //create the random password
                $password = $randomPassword->genererMotDePasse(6);
                $user->setPassword(
                    $userPasswordHasherInterface->hashPassword(
                        $user,
                        $password
                    )
                );
                $user->setResetToken($token);
                $em->persist($user);
                $em->flush();

                // Generate a mail reinitialisation link
                $url = $this->generateUrl('reset_pass_employee', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                // Create the mail datas
                $context = compact('url', 'user','password');

                // send the e-mail
                $mail->send(
                    'user@example.com', 
                    $user->getEmail(),
                    'Création de votre mot de passe CleanThis',
                    'password_create_employee', 
                    $context
                );
                $this->addFlash('success', 'Votre compte CleanThis est activé! Un e-mail vous a été envoyé pour la création de votre mot de passe');
                return $this->redirectToRoute('app_home');
            }
            if ($user && $user->getIsVerified()) {
                $this->addFlash('success', 'Votre compte est déja activé');
                return $this->render('security/redirect_login.html.twig');
            }
        }
        //here we have a problem in the token
        return $this->render('security/mail.html.twig');
    }


    #[Route('/renvoiverif', name: 'resend_verif')]
    public function resendVerif(Request $request,JWTService $jwt, SendMailService $mail, UserRepository $userRepository):Response{

        $email = $request->get('email');
        $user = $userRepository->findOneByEmail($email);
        if ($user==null) {
            $this->addFlash('danger', 'Aucun utilisateur trouvé avec cet e-mail');
            return $this->render('auth_oauth_login');
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

