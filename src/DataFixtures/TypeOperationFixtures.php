<?php

namespace App\DataFixtures;

use App\Entity\TypeOperation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;;

class TypeOperationFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
    ) {
    }
    public function load(ObjectManager $manager): void
    {
        $service = new TypeOperation();
        $service->setLibelle('Service de Nettoyage Basique');
        $service->setTarif('1000');
        $service->setDescriptif('Ce service comprend le nettoyage de base des espaces résidentiels ou commerciaux. Il inclut le balayage, le lavage des sols, le dépoussiérage des surfaces, le nettoyage des salles de bains et des cuisines, ainsi que la vidange des poubelles. Il est idéal pour les petites maisons, les appartements ou les bureaux de petite taille.');
        $service->setImage('65fb6d07b31d0.png');

        $manager->persist($service);

        $service = new TypeOperation();
        $service->setLibelle('Service de Nettoyage Premium');
        $service->setTarif('2500');
        $service->setDescriptif('Ce service offre une gamme plus étendue de nettoyage, adaptée aux besoins plus spécifiques des clients. En plus des tâches de nettoyage de base, il comprend le nettoyage des vitres, le nettoyage des tapis et des moquettes, le dépoussiérage en profondeur des meubles, ainsi que le nettoyage des appareils électroménagers. Il convient aux résidences plus grandes, aux bureaux de taille moyenne et aux espaces commerciaux haut de gamme.');
        $service->setImage('65f814cb743fe.jpg');

        $manager->persist($service);

        $service = new TypeOperation();
        $service->setLibelle('Service de Nettoyage de Luxe');
        $service->setTarif('5000');
        $service->setDescriptif('Ce service offre un nettoyage haut de gamme avec une attention particulière aux détails et une utilisation de produits de nettoyage de qualité supérieure. En plus de toutes les tâches incluses dans les services de base et premium, ce service comprend le polissage des surfaces en marbre ou en bois, le traitement spécial pour les sols en pierre ou en granit, le nettoyage à la vapeur des meubles rembourrés, ainsi que des services de désinfection avancée. Il convient aux propriétaires exigeants de grandes maisons, de complexes commerciaux de luxe, d\'hôtels haut de gamme, ou pour des événements spéciaux nécessitant un nettoyage impeccable.');
        $service->setImage('65f82f40048d2.png');

        $manager->persist($service);

        $service = new TypeOperation();
        $service->setLibelle('Service de Nettoyage Custom');
        $service->setTarif('0');
        $service->setDescriptif('Notre service de nettoyage personnalisé est conçu pour répondre à vos besoins spécifiques. Avec une attention particulière aux détails et une flexibilité totale, nous adaptons notre travail à vos exigences uniques. Que vous ayez des surfaces délicates, des préférences en matière de produits ou des demandes spéciales de désinfection, notre équipe est là pour vous offrir une solution sur mesure, garantissant un environnement impeccablement propre, quelles que soient vos attentes.');
        $service->setImage('66052e6586501.jpg.png');

        $manager->persist($service);

    
        $manager->flush();
    }
}