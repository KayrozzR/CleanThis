<?php
// src/Controller/LanguageController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageController extends AbstractController
{
    public function changeLanguage(Request $request, string $language): RedirectResponse
    {
        // Stockez la langue sélectionnée dans la session ou le cookie pour une utilisation future
        $request->getSession()->set('_locale', $language);

        // Redirigez vers la page précédente ou une page spécifique avec la langue sélectionnée
        return $this->redirectToRoute('homepage', ['_locale' => $language]);
    }
}
