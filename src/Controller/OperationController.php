<?php

namespace App\Controller;

use App\Entity\Operation;
use App\Form\OperationType;
use App\Entity\TypeOperation;
use App\Form\ReclamationType;
use App\Form\OperationNoteType;
use App\Form\TypeOperationType;
use Doctrine\ORM\EntityManager;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/operation')]
class OperationController extends AbstractController
{
    #[Route('/', name: 'app_operation_index', methods: ['GET'])]
    public function index(OperationRepository $operationRepository): Response
    {
        return $this->render('operation/index.html.twig', [
            'operations' => $operationRepository->findAll(),
        ]);
    }

    // #[Route('/new', name: 'app_operation_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $operation = new Operation();
    //     $form = $this->createForm(OperationType::class, $operation);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($operation);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_operation_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('operation/new.html.twig', [
    //         'operation' => $operation,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'app_operation_show', methods: ['GET'])]
    public function show(Operation $operation): Response
    {
        return $this->render('operation/show.html.twig', [
            'operation' => $operation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_operation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Operation $operation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OperationType::class, $operation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_operation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('operation/edit.html.twig', [
            'operation' => $operation,
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'app_operation_delete', methods: ['POST'])]
    // public function delete(Request $request, Operation $operation, EntityManagerInterface $entityManager): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$operation->getId(), $request->request->get('_token'))) {
    //         $entityManager->remove($operation);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('app_operation_index', [], Response::HTTP_SEE_OTHER);
    // }
    #[Route('/{id}/note', name: 'note', methods: ['GET', 'POST'])]
    public function note(Request $request, Operation $operation, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si une note et un commentaire ont déjà été soumis pour cette opération
        if ($operation->getNote() !== null && $operation->getComment() !== null) {
            // Si oui, rediriger l'utilisateur ou afficher un message
            // Par exemple, rediriger vers la page de profil de l'utilisateur
            return $this->redirectToRoute('app_user_profil', ['id' => $operation->getId()]);
        }

        $form = $this->createForm(OperationNoteType::class, $operation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'objet Operation avec les données mises à jour du formulaire
        
            $comment = $form->get('comment')->getData();
            $note = $form->get('note')->getData();

            
            $operation->setReclamation($comment);
            $operation->setnote($note);
            $entityManager->persist($operation);
            $entityManager->flush();


            // Enregistrer les modifications dans la base de données

            // Rediriger vers une autre page, par exemple, la page du profil de l'utilisateur
            return $this->redirectToRoute('app_user_profil', ['id' => $operation->getId()]);
        }

        return $this->render('home/operationNote.html.twig', [
            'operation' => $operation,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/payer/{id}', name: 'payer', methods: ['GET'])]
    public function payer(Operation $operation, EntityManagerInterface $entityManager): Response
    {
       
        $operation->setStatusPaiement('Payée');
        $entityManager->flush();

        // Redirection vers la page des détails de l'opération après le paiement
        return $this->redirectToRoute('operation_paiement', ['id' => $operation->getId()]);
    }
}
