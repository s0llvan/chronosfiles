<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Events;
use Defuse\Crypto\KeyProtectedByPassword;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Repository\UserRepository;

class RegistrationController extends AbstractController
{
	/**
	 * @Route("/register", name="register")
	 */
	public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, EventDispatcherInterface $eventDispatcher, \Swift_Mailer $mailer, UserRepository $userRepository)
	{
		$user = new User();
		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$token = bin2hex(random_bytes(32));

			$user->setEmailConfirmationToken($token);

			$confirmation_link = $this->generateUrl('register_confirmation', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

			$message = (new \Swift_Message('ChronosFiles - Registration'))
				->setFrom('donotreply@chronosfiles.fr')
				->setTo($user->getEmail())
				->setBody(
					$this->renderView(
						'emails/registration.html.twig',
						[
							'user' => $user,
							'confirmation_link' => $confirmation_link
						]
					),
					'text/html'
				);
			$mailer->send($message);

			$password = $passwordEncoder->encodePassword($user, $user->getPassword());
			$user->setPassword($password);

			$roles = ['ROLE_USER'];

			if ($userRepository->count([]) <= 0) {
				$roles = ['ROLE_ADMIN'];
			}

			$user->setRoles($roles);

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

			$this->get('session')->getFlashBag()->add('success', 'Please click on the link sent by email to confirm your account');

			return $this->redirectToRoute('register');
		}

		return $this->render('registration.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/register-confirmation/{token}", name="register_confirmation")
	 */
	public function registerConfirmationAction(Request $request, $token, UserRepository $userRepository)
	{
		if ($user = $userRepository->findOneBy(['email_confirmation_token' => $token])) {

			$user->setEmailConfirmationToken(null);
			$user->setEmailConfirmed(true);

			$em = $this->getDoctrine()->getManager();
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Registration completed, you can now log in !');
		} else {
			$this->get('session')->getFlashBag()->add('error', 'Wrong token !');
		}

		return $this->redirectToRoute('login');
	}
}
