<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Operation;
use App\Entity\TypeOperation;
use App\Entity\User;
use App\Form\DevisType;
use App\Service\PdfService;
use App\Repository\UserRepository;
use App\Repository\DevisRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\Catch_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

#[Route('/admin/devis')]
class DevisController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_devis_index', methods: ['GET'])]
    public function index(DevisRepository $devisRepository): Response
    {
        $devis = $devisRepository->findAll();

        $devisWithTrueStatus = [];

        foreach ($devis as $devi) {
            if ($devi->isStatus() === true) {
                $devisWithTrueStatus[] = $devi->getId();
            }
        }

        return $this->render('devis/index.html.twig', [
            'devis' => $devis,
            'devisWithTrueStatus' => $devisWithTrueStatus,
        ]);
    }

    #[Route('/new', name: 'app_devis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $devi = new Devis();
        $form = $this->createForm(DevisType::class, $devi);
        $form->handleRequest($request);

        // $type_operations = $entityManager->getRepository(TypeOperation::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {

            $serv = $form->getData();
            $mail = $form->get('mail')->getData();
            $mailConfirmation = $form->get('mailConfirmation')->getData();

            if ($mail === $mailConfirmation) {

                if($photo = $form['image_object']->getData()){
                    $fileName = uniqid().'.'.$photo->guessExtension();
                    $photo->move($this->getParameter('photo_dir'), $fileName);
                    $serv->setImageObject($fileName);
                }

                    $entityManager->persist($devi);
                    $entityManager->flush();
            }else { 
                $this->addFlash('error', 'Les mails ne correspondent pas');
                return $this->redirectToRoute('app_devis_new', [], Response::HTTP_SEE_OTHER);
            };

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

        $currentUser = $this->getUser();

        if (in_array('ROLE_ADMIN', $currentUser->getRoles(), true)) {

            if ($currentUser->getOperationEnCours() >= 5) {

                return new Response('Vous avez déjà atteint le nombre maximum d\'opérations en cours.', Response::HTTP_FORBIDDEN);
            }
        } elseif (in_array('ROLE_SENIOR', $currentUser->getRoles(), true)) {

            if ($currentUser->getOperationEnCours() >= 3) {
                
                return new Response('Vous avez déjà atteint le nombre maximum d\'opérations en cours.', Response::HTTP_FORBIDDEN);
            }
        } elseif (in_array('ROLE_APPRENTI', $currentUser->getRoles(), true)) {

            if ($currentUser->getOperationEnCours() >= 1) {
                
                return new Response('Vous avez déjà atteint le nombre maximum d\'opérations en cours.', Response::HTTP_FORBIDDEN);
            }
        }

        if ($request->request->has('status')) {

            $status = $request->request->get('status');
            
            if ($status === 'true') {

                $currentUser->setOperationEnCours(($currentUser->getOperationEnCours() ?? 0) + 1);
                $entityManager->persist($currentUser);

                $user = new User();

                $user->setFirstname($devi->getFirstname());
                $user->setLastname($devi->getLastname());
                $user->setEmail($devi->getMail());
                $user->setRoles(["ROLE_CLIENT"]);
                // Persist et flush de l'utilisateur
                $entityManager->persist($user);
                $entityManager->flush();

                $header =[
                    'typ'=>'JWT',
                    'alg'=>'HS256'
                ];

                $payload =[
                    'user_id'=>$user->getId()
                ];

                $token = $jwt->generate($header,$payload,
                $this->getParameter('app.jwtsecret'));
    
                $mail->send ('no-reply@cleanthis.fr',
                    $user->getEmail(),
                    'Activation de votre compte CleanThis',
                    'register',
                    compact('user','token')
                );


                $operation = new Operation();

                $operation->setUser($currentUser);
                
                $devi->addOperation($operation);

                $devi->setUser($user);

                $devi->setStatus(true);
                
                $entityManager->persist($operation);
                $entityManager->persist($devi);
                $entityManager->flush();
            }
            
        }

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
        $serv = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            if($photo = $form['image_object']->getData()){
                $fileName = uniqid().'.'.$photo->guessExtension();
                $photo->move($this->getParameter('photo_dir'), $fileName);
                $serv->setImageObject($fileName);
            }

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

    #[Route('/pdf/{id}', name: 'devis_pdf', methods: ['GET'])]
    public function generatePdfDevis(PdfService $pdf, Devis $devi = null):response{
        // $html = $this->renderView('Pdf/devis.html.twig', ['devi' => $devi]);
        // $pdf ->showPdfFile($html);

        // return new Response();

        return $this->render('Pdf/devis.html.twig', ['devi' => $devi]);
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
