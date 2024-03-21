<?php

namespace App\Controller\Client;

use App\Repository\TypeOperationRepository;
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
}
