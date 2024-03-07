<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AutoCompleteController extends AbstractController
{
    #[Route('/autocomplete', name: 'address_autocomplete')]
    public function autocomplete(Request $request, HttpClientInterface $httpClient): JsonResponse
    {
        $query = $request->query->get('query');

        $response = $httpClient->request(
            'GET',
            'https://api-adresse.data.gouv.fr/search/',
            ['query' => ['q' => $query, 'limit' => 5]]
        );

        return new JsonResponse($response->toArray(false));
    }
}
