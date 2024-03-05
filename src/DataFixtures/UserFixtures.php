<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
    ) {
    }
    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@demo.fr');
        $admin->setLastname('Stark');
        $admin->setFirstname('Tony');
        $admin->setTel('0789456321');
        $admin->setAddress('10880 Malibu Point');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'admin')
        );
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $faker = Faker\Factory::create('fr_FR');

        for ($usr = 1; $usr <= 15; $usr++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setLastname($faker->lastName);
            $user->setFirstname($faker->firstName);
            $user->setTel(str_replace(' ', '', $faker->mobileNumber));
            $user->setAddress($faker->streetAddress);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'test')
            );
            $user->setRoles(['ROLE_CLIENT']);

            $manager->persist($user);
        }

        for ($usr = 1; $usr <= 10; $usr++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setLastname($faker->lastName);
            $user->setFirstname($faker->firstName);
            $user->setTel(str_replace(' ', '', $faker->mobileNumber));
            $user->setAddress($faker->streetAddress);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'test')
            );
            $user->setRoles(['ROLE_APPRENTI']);

            $manager->persist($user);
        }

        for ($usr = 1; $usr <= 5; $usr++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setLastname($faker->lastName);
            $user->setFirstname($faker->firstName);
            $user->setTel(str_replace(' ', '', $faker->mobileNumber));
            $user->setAddress($faker->streetAddress);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'test')
            );
            $user->setRoles(['ROLE_SENIOR']);

            $manager->persist($user);
        }


        $manager->flush();
    }
}
