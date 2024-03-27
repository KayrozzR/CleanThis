<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Operation;
use App\Form\OperationType;
use App\Service\PdfService;
use App\Entity\TypeOperation;
use App\Form\ReclamationType;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/operation')]
class OperationController extends AbstractController
{
    #[Route('/', name: 'app_operation_index', methods: ['GET'])]
    public function index(OperationRepository $operationRepository): Response
    {
        return $this->render('admin/operation/index.html.twig', [
            'operations' => $operationRepository->findAll(),
        ]);
    }


    #[Route('/{id}', name: 'app_operation_show', methods: ['GET'])]
    public function show(Operation $operation): Response
    {
        return $this->render('admin/operation/show.html.twig', [
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

        return $this->render('admin/operation/edit.html.twig', [
            'operation' => $operation,
            'form' => $form,
        ]);
    }



    #[Route('/{id}/facture', name: 'app_operation_facture', methods: ['GET', 'POST'])]
    public function VoirFacture(PdfService $pdf, Operation $operation, UserRepository $userRepository, Devis $devi, EntityManagerInterface $entityManager, Request $request): Response
    {  
       if ($operation->isStatusOperation() == true) {
        
        // $devi = $operation->getDevis();
        $id_operation = $devi->getTypeOperation();
        $email = $devi->getMail();
        $type_operations = $entityManager->getRepository(TypeOperation::class)->find($id_operation);
        
        $publicDirectory = $this->getParameter('kernel.project_dir') . '/public';
        $logoPath = $publicDirectory . '/images/logo.png';
        if (!file_exists($logoPath)) {
            throw new \Exception('Le fichier logo n\'existe pas.');
        }
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoBase64 = 'data:image/png;base64,' . $logoData;

        $html = $this->renderView('Pdf/facture.html.twig', [
                'devi' => $devi,
                'type_operation' => $type_operations,
                'logo_base64' => $logoBase64,
                'operation' => $operation
            ]);

        $pdf ->showPdfFile($html);
        return new Response();

       }else {
        $this->addFlash('warning', 'La facture n\'a pas pu être téléchargée');
        return $this->redirectToRoute('app_user_profil'); 
       }
        return new Response();
    }  

  
}
