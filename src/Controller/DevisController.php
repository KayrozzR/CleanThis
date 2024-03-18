<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Operation;
use App\Entity\User;
use App\Form\DevisType;
use App\Repository\DevisRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/devis')]
class DevisController extends AbstractController
{
    #[Route('/', name: 'app_devis_index', methods: ['GET'])]
    public function index(DevisRepository $devisRepository): Response
    {
        $devis = $devisRepository->findAll();

        // Création d'un tableau pour stocker les IDs des devis avec le statut vrai
        $devisWithTrueStatus = [];

        // Parcourir tous les devis pour vérifier le statut
        foreach ($devis as $devi) {
            if ($devi->isStatus() === true) {
                // Ajouter l'ID du devis avec le statut vrai au tableau
                $devisWithTrueStatus[] = $devi->getId();
            }
        }

        return $this->render('devis/index.html.twig', [
            'devis' => $devis,
            'devisWithTrueStatus' => $devisWithTrueStatus, // Envoyer les IDs des devis avec le statut vrai au modèle
        ]);
    }

    #[Route('/new', name: 'app_devis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $devi = new Devis();
        $form = $this->createForm(DevisType::class, $devi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist et flush du devis
            $entityManager->persist($devi);
            $entityManager->flush();

            // Redirection vers une page où vous pouvez éventuellement changer le statut

            return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis/new.html.twig', [
            'devi' => $devi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/toggle-status', name: 'app_devis_toggle_status', methods: ['POST'])]
    public function toggleStatus(Request $request, Devis $devi, EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        // Vérifier si le formulaire a été soumis avec le champ 'status' en tant que paramètre POST
        if ($request->request->has('status')) {
            // Récupérer la valeur du statut du formulaire
            $status = $request->request->get('status');
            
            // Vérifier si le statut est true
            if ($status === 'true') {
                // Création d'un nouvel utilisateur
                $user = new User();
                // Assigner les données de l'utilisateur depuis le devis
                $user->setFirstname($devi->getFirstname());
                $user->setLastname($devi->getLastname());
                $user->setEmail($devi->getMail());
                // Persist et flush de l'utilisateur
                $entityManager->persist($user);
                $entityManager->flush();

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

                $operation = new Operation();
                
                $devi->addOperation($operation);
                
                // Assigner l'utilisateur au devis
                $devi->setUser($user);
                
                // Mettre à jour le statut du devis
                $devi->setStatus(true);
                
                // Persist et flush du devis mis à jour
                $entityManager->persist($operation);
                $entityManager->persist($devi);
                $entityManager->flush();
            }
        }

        // Redirection vers une page appropriée
        return $this->redirectToRoute('app_devis_index');
    }

    #[Route('/{id}', name: 'app_devis_show', methods: ['GET'])]
    public function show(Devis $devi): Response
    {
        return $this->render('devis/show.html.twig', [
            'devi' => $devi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_devis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Devis $devi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DevisType::class, $devi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis/edit.html.twig', [
            'devi' => $devi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_devis_delete', methods: ['POST'])]
    public function delete(Request $request, Devis $devi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$devi->getId(), $request->request->get('_token'))) {
            // Supprimer l'utilisateur lié au devis s'il existe
            $user = $devi->getUser();
            if ($user !== null) {
                $entityManager->remove($user);
            }
            
            // Supprimer le devis
            $entityManager->remove($devi);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false]);
    }
    
}
