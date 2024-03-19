<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Form\DevisType;
use App\Service\PdfService;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use App\Repository\DevisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

#[Route('/devis')]
class DevisController extends AbstractController
{
    #[Route('/', name: 'app_devis_index', methods: ['GET'])]
    public function index(DevisRepository $devisRepository): Response
    {
        return $this->render('devis/index.html.twig', [
            'devis' => $devisRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_devis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $devi = new Devis();
        $form = $this->createForm(DevisType::class, $devi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($devi);
            $entityManager->flush();

            return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis/new.html.twig', [
            'devi' => $devi,
            'form' => $form,
        ]);
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
            $entityManager->remove($devi);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false]);
    }


    #[Route('/pdf/{id}', name: 'devis_pdf', methods: ['GET'])]
    public function generatePdfDevis(PdfService $pdf, Devis $devis = null):response{
        $html = $this->renderView('Pdf/devis.html.twig', ['devis' => $devis]);
        $pdf ->showPdfFile($html);

        return new Response();
    }

    // #[Route('/SendPdf/{id}', name: 'devis_pdf_send', methods: ['GET'])]
    // public function SendPdf(PdfService $pdf, Devis $devis, $token, UserRepository $userRepository, EntityManagerInterface $em, Request $request,SendMailService $mail,TokenGeneratorInterface $tokenGenerator):response{

    //     $devis = $this->devis->getElementBy
    //     $pdf ->generateBinaryPDF($html);

    //     $token = $tokenGenerator->generateToken();
    //             // $user->setResetToken($token);
    //             // $em->persist($user);
    //             // $em->flush();

    //             // Generate a pdf generator link
    //             $url = $this->generateUrl('devis_pdf', ['pdf' => $pdf], UrlGeneratorInterface::ABSOLUTE_URL);

    //             // Create the mail datas
    //             $context = compact('url', 'user');

    //             // send the e-mail
    //             $mail->send(
    //                 'user@example.com', 
    //                 $user->getEmail(),
    //                 'Création de votre mot de passe CleanThis',
    //                 'password_create', 
    //                 $context
    //             );

    //     return new Response();
    // }

}
