<?php
// src/Controller/LanguageController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LanguageController extends AbstractController
{
    #[Route('/change-language/{locale}', name: 'change_language')]
    public function changeLanguage(Request $request, string $locale): Response
    {
        if (!in_array($locale, ['en', 'fr'])) {
            throw new \InvalidArgumentException('Langue non valide');
        }

        $request->getSession()->set('_locale', $locale);

        // Rediriger vers la page précédente après le changement de langue
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    #[Route('/language-options', name: 'language_options')]
    public function languageOptions(): Response
    {
        // Afficher une vue avec les options de langue disponibles
        return $this->render('language/language_options.html.twig');
    }
}
