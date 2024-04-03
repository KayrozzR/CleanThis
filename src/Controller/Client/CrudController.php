<?php

namespace App\Controller\Client;

use App\Entity\User;
use App\Entity\Devis;
use App\Form\DevisType;
use App\Form\ClientType;
use App\Entity\Operation;
use App\Service\PdfService;
use App\Entity\TypeOperation;
use App\Form\OperationNoteType;
use App\Form\ReclamationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
                // take all the operations associated with the devis
                $deviOperations = $devi->getOperation();
                
                // stock the operations in an array
                foreach ($deviOperations as $operation) {
                    $lastType = $devi->getTypeOperation();
                    $operations[] = [
                        'operation' => $operation,
                        'lastType' => $lastType,
                    ];
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

        // return $this->route('client/profil.html.twig', [
        //     'user' => $user,
        //     'form' => $form,
        //     'error' => $error
        // ]);
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
            return $this->render('client/success_reclamation.html.twig');
        }

        return $this->render('client/reclamation.html.twig', [
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

    #[Route('/{id}/profile', name: 'app_profile_show', methods: ['GET'])]
    public function show(Operation $operation): Response
    {
        $user = $this->getUser();

        return $this->render('client/show.html.twig', [
            'operation' => $operation,
            'user' => $user,
        ]);
    }

    #[Route('/{id}/facture', name: 'app_operation_facture', methods: ['GET', 'POST'])]
    public function VoirFacture(PdfService $pdf, Operation $operation, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $devis = $operation->getDevis()->first();

        if ($operation->isStatusOperation() && $devis) {

            $typeOperation = $devis->getTypeOperation(); // Récupérer l'entité TypeOperation à partir du devis

            $publicDirectory = $this->getParameter('kernel.project_dir') . '/public';
            $logoPath = $publicDirectory . '/images/logo.png';

            if (!file_exists($logoPath)) {
                throw new \Exception('Le fichier logo n\'existe pas.');
            }

            $logoData = base64_encode(file_get_contents($logoPath));
            $logoBase64 = 'data:image/png;base64,' . $logoData;

            $html = $this->renderView('Pdf/facture.html.twig', [
                'devi' => $devis,
                'type_operation' => $typeOperation,
                'logo_base64' => $logoBase64,
                'operation' => $operation
            ]);

            $pdfContent = $pdf->generateBinaryPDF($html);

            return new Response(
                $pdfContent,
                Response::HTTP_OK,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="facture.pdf"',
                ]
            );
        } else {
            $this->addFlash('warning', 'La facture n\'a pas pu être téléchargée');
            return $this->redirectToRoute('app_user_profil');
        }
    }

    #[Route('/devis', name: 'app_devis_client', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $devi = new Devis();
        $form = $this->createForm(DevisType::class, $devi);
        $form->handleRequest($request);
        $user = $this->getUser();
    
   
        if ($user) {
    
            $devi->setLastname($user->getLastname());
            $devi->setFirstname($user->getFirstname());
            $devi->setMail($user->getEmail());
            $devi->setTel($user->getTel());
            
            // Rendre les champs non modifiables
            $form = $this->createForm(DevisType::class, $devi, ['disabled_fields' => true]);
        } else {
            // Si l'utilisateur n'est pas connecté, créer un formulaire vide
            $form = $this->createForm(DevisType::class, $devi);
        }
        
        $form->handleRequest($request);

        // $type_operations = $entityManager->getRepository(TypeOperation::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {

            $existingDevis = $entityManager->getRepository(Devis::class)->findOneBy([
                'mail' => $devi->getMail(),
                'typeOperation' => $devi->getTypeOperation(),
                'adresse_intervention' => $devi->getAdresseIntervention()
            ]);

            if ($existingDevis !== null) {
                $this->addFlash('error', 'Ce devis existe déjà.');
                return $this->redirectToRoute('app_devis_client');
            }


            $serv = $form->getData();
            $mail = $form->get('mail')->getData();
            $mailConfirmation = $form->get('mailConfirmation')->getData();

            if ($mail === $mailConfirmation) {

                if($photo = $form['image_object']->getData()){
                    $fileName = uniqid().'.'.$photo->guessExtension();
                    $photo->move($this->getParameter('photo_dir'), $fileName);
                    $serv->setImageObject($fileName);
                }

                $typeOperation = $devi->getTypeOperation();
            if ($typeOperation !== null) {
                    $tarifTypeOperation = $typeOperation->getTarif();
                    $devi->setTarifCustom($tarifTypeOperation);
                }

                    $entityManager->persist($devi);
                    $entityManager->flush();
            }else { 
                $this->addFlash('error', 'Les mails ne correspondent pas');
                return $this->redirectToRoute('app_devis_client', [], Response::HTTP_SEE_OTHER);
            };

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/devis.html.twig', [
            'devi' => $devi,
            'form' => $form,
        ]);
    }

#[Route('/{id}/note', name: 'note', methods: ['GET', 'POST'])]
    public function note(Request $request, Operation $operation, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si une note et un commentaire ont déjà été soumis pour cette opération
        // if ($operation->getNote() !== null && $operation->getComment() !== null) {
        //     // Si oui, rediriger l'utilisateur ou afficher un message
        //     // Par exemple, rediriger vers la page de profil de l'utilisateur
        //     return $this->redirectToRoute('app_user_profil', ['id' => $operation->getId()]);
        // }

        $form = $this->createForm(OperationNoteType::class, $operation);
        $form->handleRequest($request);
        $devis = $operation->getDevis()->first();

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
            'devi' => $devis,
        ]);
    }
    
    #[Route('/payer/{id}', name: 'payer', methods: ['GET'])]
    public function payer(Operation $operation, EntityManagerInterface $entityManager): Response
    {
       
        $operation->setStatusPaiement('Payée');
        $entityManager->flush();

        
        return $this->redirectToRoute('app_user_profil', ['id' => $operation->getId()]);
    }

}