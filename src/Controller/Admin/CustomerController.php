<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Form\User1Type;
use App\Form\CustomerType;
use App\Service\JWTService;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/customer')]
class CustomerController extends AbstractController
{
    #[Route('/', name: 'app_customer_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
         // Récupérer uniquement les utilisateurs ayant le rôle 'ROLE_CLIENT'
        $roles = ['ROLE_CLIENT'];
        $customers = $userRepository->findByRoles($roles);

        return $this->render('admin/customer/index.html.twig', [
            'users' => $customers,
    ]);
    }

    #[Route('/new', name: 'app_customer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,SendMailService $mail, JWTService $jwt): Response
    {
        $user = new User();
        $user->setRoles(["ROLE_CLIENT"]);
        $form = $this->createForm(CustomerType::class, $user);
        $form->handleRequest($request);

        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            try {$entityManager->persist($user);
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
    
                $mail->send ('no-reply@cleanthis.fr',
                    $user->getEmail(),
                    'Activation de votre compte CleanThis',
                    'register',
                    compact('user','token')
                );
    
                return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
                
            } catch (UniqueConstraintViolationException $e) {
                // Vérifier si le message d'erreur indique une violation de la contrainte d'unicité pour l'adresse e-mail
                if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'for key \'UNIQ_8D93D649E7927C74\'')) {
                    // Définir le message d'erreur approprié
                    $error = 'L\'adresse e-mail existe déjà. Veuillez en choisir une autre.';
                }
                // Autres exceptions de violation de contrainte d'unicité peuvent être gérées ici si nécessaire
                // Vous pouvez ajouter d'autres blocs if-else pour d'autres contraintes d'unicité si nécessaire
            }
            
        }

        $this->denyAccessUnlessGranted('ROLE_APPRENTI');

        return $this->render('admin/customer/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'error' => $error,
        ]);
    }




    #[Route('/{id}', name: 'app_customer_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/customer/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_customer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CustomerType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/customer/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_customer_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);
        }
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return new JsonResponse(['success' => false]);
    }
}
