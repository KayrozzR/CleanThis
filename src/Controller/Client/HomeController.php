<?php

namespace App\Controller\Client;

use App\Form\ContactFormType;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TypeOperationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/services', name: 'app_services')]
    public function services(TypeOperationRepository $typeOperationRepository): Response
    {
        return $this->render('home/services.html.twig', [
            'type_operations' => $typeOperationRepository->findAll(),
        ]);
    }


    #[Route('/contact_form', name: 'contact_form', methods: ['GET', 'POST'])]
    public function contact(Request $request,EntityManagerInterface $entityManager,SendMailService $mail): Response
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $form->get('lastName')->getData();
            $prenom = $form->get('firstName')->getData();
            $emailClient =$form->get('email')->getData();
            $telephone = $form->get('phone')->getData();
            $adresse = $form->get('address')->getData();
            $codePostale = $form->get('postalCode')->getData();
            $ville = $form->get('city')->getData();
            $preferenceContact = $form->get('contactMethod')->getData();
            $message = $form->get('message')->getData();

            $mail->send ('no-reply@cleanthis.fr',
            'no-reply@cleanthis.fr',
            'Contact Client',
            'contact',
            compact('nom','prenom','emailClient' ,'telephone', 'adresse', 'codePostale', 'ville', 'preferenceContact', 'message')
        );
         
            return $this->render('home/success_contact.html.twig');
        }

        return $this->render('home/contact_form.html.twig', [
                    'form' => $form->createView(),
        ]);
    }
}