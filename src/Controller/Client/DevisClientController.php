<?php

namespace App\Controller\Client;

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

#[Route('/devis')]
class DevisClientController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

 

    #[Route('/', name: 'app_devis_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $devi = new Devis();
        $form = $this->createForm(DevisType::class, $devi);
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
                return $this->redirectToRoute('app_devis_client_new');
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

                    $entityManager->persist($devi);
                    $entityManager->flush();
            }else { 
                $this->addFlash('error', 'Les mails ne correspondent pas');
                return $this->redirectToRoute('app_devis_client_new', [], Response::HTTP_SEE_OTHER);
            };

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/devis.html.twig', [
            'devi' => $devi,
            'form' => $form,
        ]);
    }


   
}
