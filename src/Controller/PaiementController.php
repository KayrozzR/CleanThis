<?php
// src/Controller/PaiementController.php

namespace App\Controller;

use App\Form\PaiementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/operation')]
class PaiementController extends AbstractController
{
    #[Route('/{id}/operation/paiement', name: 'operation_paiement')]
    public function paiement(Request $request): Response
    {
        $form = $this->createForm(PaiementType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $formData = $form->getData();
            
            return $this->redirectToRoute('operation_paiement_success');
        }

        return $this->render('operation/paiement.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/operation/paiement/success', name: 'operation_paiement_success')]
    public function paiementSuccess(): Response
    {
        return $this->render('operation/success.html.twig');
    }
}
