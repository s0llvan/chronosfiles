<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Events;
use Defuse\Crypto\KeyProtectedByPassword;

class RegistrationController extends Controller
{
    /**
    * @Route("/register", name="register")
    */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, EventDispatcherInterface $eventDispatcher)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $user->setRoles(['ROLE_USER']);

            $password = $form->get('password')->getData();
            $password = sha1($password);

            $protected_key = KeyProtectedByPassword::createRandomPasswordProtectedKey($password);
            $protected_key_encoded = $protected_key->saveToAsciiSafeString();

            $user->setEncryptionKey($protected_key_encoded);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $event = new GenericEvent($user);
            $eventDispatcher->dispatch(Events::USER_REGISTERED, $event);

            $this->get('session')->getFlashBag()->add('success', 'Registration completed !');

            return $this->redirectToRoute('login');
        }

        return $this->render('registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
