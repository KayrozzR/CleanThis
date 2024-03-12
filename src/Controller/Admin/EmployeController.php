<?php

// Controller modifié

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Service\JWTService;
use Doctrine\ORM\EntityManager;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use App\Form\ResetPasswordFormType;
use App\Form\CreatePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/employee')]
class EmployeController extends AbstractController
{
    #[Route('/', name: 'app_employe_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $roles = ['ROLE_SENIOR', 'ROLE_APPRENTI', 'ROLE_ADMIN'];
        $employee = $userRepository->findByRoles($roles);
        
        return $this->render('admin/employe/index.html.twig', [
            'users' => $employee, // Passer les clients à la vue
        ]);
    }

    #[Route('/new', name: 'app_employe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            //We generate the jwt of the user
            //We cretae the header
            $header =[
                'typ'=>'JWT',
                'alg'=>'HS256'
            ];
            //We create the payload
            $payload =[
                'user_id'=>$user->getId()
            ];
            //We generate the token
            $token = $jwt->generate($header,$payload,
            $this->getParameter('app.jwtsecret'));

            $user->setMailToken($token);
            $entityManager->persist($user);
            $entityManager->flush();

            $mail->send ('no-reply@cleanthis.fr',
                $user->getEmail(),
                'Activation de votre compte CleanThis',
                'register',
                compact('user','token')
            );
            return $this->redirectToRoute('app_employe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/employe/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_employe_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/employe/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_employe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $file = $form['avatar']->getData();
            // $someNewFilename = 'file_' . date('YmdHis') . '_' . uniqid() . '.url';
            // $directory= "../public/images";
            // $file->move($directory, $someNewFilename);
            // $user->setAvatar($someNewFilename);
            // $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_employe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/employe/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_employe_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_employe_index', [], Response::HTTP_SEE_OTHER);
    }

}



