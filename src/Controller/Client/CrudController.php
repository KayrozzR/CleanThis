<?php

namespace App\Controller\Client;

use App\Entity\User;
use App\Entity\Devis;
use App\Form\UserType;
use App\Form\User1Type;
use App\Form\ClientType;
use App\Form\CustomerType;
use App\Service\JWTService;
use App\Entity\Operation;
use App\Form\ReclamationType;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use App\Repository\DevisRepository;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/user')]
class CrudController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/profile', name: 'app_user_profil')]
    public function index(): Response
    {  
        $user = $this->getUser();

        // Vérifie if the user is connected
        if ($user !== null) {
            //get the devis associate to the user
            $devis = $user->getDevis();

            // array to stock all the operations 
            $operations = [];

            // we get into each devis
            foreach ($devis as $devi) {
                // take all tghe operations associate to the devis
                $deviOperations = $devi->getOperation();

                // stock the operations in ana array
                foreach ($deviOperations as $operation) {
                    $operations[] = $operation;
                }
            }

            // we send the operations to the view
            return $this->render('client/profil.html.twig', [
                'controller_name' => 'ClientController',
                'operations' => $operations, 
                'devi' => $devis,
            ]);
        } else {
            // We are going to redirect to an error page
        }
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientType::class, $user);
        $form->handleRequest($request);
        $user = $this->getUser();

        $error = null;
        
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();

                return $this->redirectToRoute('app_user_profil', [], Response::HTTP_SEE_OTHER);
            } catch (UniqueConstraintViolationException $e) {
                // Vérifier si le message d'erreur indique une violation de la contrainte d'unicité pour l'adresse e-mail
                if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'for key \'UNIQ_8D93D649E7927C74\'')) {
                    // Définir le message d'erreur approprié
                    $error = 'L\'adresse e-mail existe déjà. Veuillez en choisir une autre.';
                }

            }
            
        }
        return $this->render('client/edit_profil.html.twig', [
            'form' => $form,
        ]);

        return $this->route('client/profil.html.twig', [
            'user' => $user,
            'form' => $form,
            'error' => $error
        ]);
    }

    #[Route('/{id}/reclamation', name: 'app_operation_reclamation', methods: ['GET', 'POST'])]
    public function reclamation(Request $request, Operation $operation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $operation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamation = $form->get('reclamation')->getData();
            $operation->setReclamation($reclamation);
            $entityManager->persist($operation);
            $entityManager->flush();

            return $this->render('operation/success_reclamation.html.twig');
        }

        return $this->render('operation/reclamation.html.twig', [
            'operation' => $operation,
            'form' => $form,
        ]);
    }

    #[Route("/uploads-avatar", name: "uploads_avatar", methods: ['POST'])]

    public function uploadAvatar(Request $request): Response
    {

        $file = $request->files->get('avatar');

        if ($file) {

            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            $directory = $this->getParameter('kernel.project_dir') . '/public/uploads/avatars';

            $file->move($directory, $fileName);

            $user = $this->getUser();
            $id = $this->getUser()->getId();

            $user->setAvatar('uploads/avatars/' . $fileName);

            $entityManager = $this->entityManager;
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_edit', [
                'id' => $id,
            ]);
        }
    }


}