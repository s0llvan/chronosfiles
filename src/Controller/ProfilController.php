<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\ProfilType;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProfilController extends AbstractController
{
	/**
	 * @Route("/profil", name="profil")
	 */
	public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer)
	{
		$session = $this->get('session');
		$user = $this->getUser();

		$form = $this->createForm(ProfilType::class, [
			'username' => $user->getUsername(),
			'email' => $user->getEmail()
		]);
		$form->handleRequest($request);

		// Check if form is submitted and valid
		if ($form->isSubmitted() && $form->isValid()) {

			$dateNow = new \DateTime();
			$dateDiff = $dateNow->getTimestamp();

			if ($user->getEmailConfirmationLast()) {
				$dateDiff = $dateNow->getTimestamp() - $user->getEmailConfirmationLast()->getTimestamp();

				// Hours
				$dateDiff = $dateDiff / 60 / 60;
			}

			if ($dateDiff > 24) {
				$email = $form->get('email')->getData();
				$token = bin2hex(random_bytes(32));

				$user->setEmailConfirmationToken($token);
				$user->setEmailConfirmation($email);
				$user->setEmailConfirmationLast($dateNow);

				$confirmation_link = $this->generateUrl('profil_email_confirmation', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

				$message = (new \Swift_Message('ChronosFiles - Email changed'))
					->setFrom('donotreply@chronosfiles.fr')
					->setTo($user->getEmail())
					->setBody(
						$this->renderView(
							'emails/profil_email.html.twig',
							[
								'user' => $user,
								'confirmation_link' => $confirmation_link
							]
						),
						'text/html'
					);
				$mailer->send($message);

				$em = $this->getDoctrine()->getManager();
				$em->flush();

				$session->getFlashBag()->add('success', 'Please check your mails at <b>' . $email . '</b> to confirm your new email address');
			} else {
				$session->getFlashBag()->add('error', 'You have already changed your email address, please wait 24 hours');
			}
		}

		return $this->render('profil/index.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/profil/delete", name="profil_delete")
	 */
	public function deleteProfil()
	{
		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();

		foreach ($user->getFiles() as $file) {

			$filePath = $this->getParameter('upload_directory') . $file->getFileNameLocation();

			if (file_exists($filePath)) {
				unlink($filePath);
			}

			$em->remove($file);
		}

		$em->remove($user);
		$em->flush();

		$session = $this->get('session');
		$session = new Session();
		$session->invalidate();

		return $this->redirectToRoute('index');
	}

	/**
     * @Route("/profil-confirmation/{token}", name="profil_email_confirmation")
     */
	public function profilEmailConfirmationAction(Request $request, $token, UserRepository $userRepository)
	{
		if ($user = $userRepository->findOneBy(['email_confirmation_token' => $token])) {

			$user->setEmailConfirmationToken(null);
			$user->setEmail($user->getEmailConfirmation());
			$user->setEmailConfirmation(null);
			$user->setEmailConfirmationLast(null);

			$em = $this->getDoctrine()->getManager();
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Your email addresse have been updated !');
		} else {
			$this->get('session')->getFlashBag()->add('error', 'Wrong token !');
		}

		return $this->redirectToRoute('profil');
	}
}
