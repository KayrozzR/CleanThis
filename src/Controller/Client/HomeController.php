<?php

namespace App\Controller\Client;

use App\Form\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/home/contact_form', name: 'contact_form')]
    public function contactForm(Request $request): Response
    {
        $form = $this->createForm(ContactFormType::class);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/contact_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
