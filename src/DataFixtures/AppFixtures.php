<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Defuse\Crypto\KeyProtectedByPassword;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$username, $password, $email, $roles]) {
            $user = new User();
            $user->setUsername($username);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);
            $user->setEmailConfirmed(true);

            $password = sha1($password);

            $protected_key = KeyProtectedByPassword::createRandomPasswordProtectedKey($password);
            $protected_key_encoded = $protected_key->saveToAsciiSafeString();

            $user->setEncryptionKey($protected_key_encoded);

            $manager->persist($user);
            $this->addReference($username, $user);
        }

        $manager->flush();
    }

    private function getUserData() : array
    {
        return [
            // $userData = [$username, $password, $email, $roles];
            ['admin', 'admin', 'admin@local', ['ROLE_ADMIN']],
            ['modo', 'user', 'modo@local', ['ROLE_MODO']],
            ['user', 'user', 'user@local', ['ROLE_USER']],
        ];
    }


}
