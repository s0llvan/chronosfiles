<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Defuse\Crypto\KeyProtectedByPassword;
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;
use App\Form\ProfilType;
use Symfony\Component\HttpFoundation\Session\Session;

class ProfilController extends Controller
{
	/**
	 * @Route("/profil", name="profil")
	 */
	public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder)
	{
		$user = $this->getUser();

		$form = $this->createForm(ProfilType::class, [
			'username' => $user->getUsername(),
			'email' => $user->getEmail()
		]);
		$form->handleRequest($request);

    	// Check if form is submitted and valid
		if ($form->isSubmitted() && $form->isValid()) {
    		// Email saved
			if (($email = $form->get('email'))) {
				$user->setEmail($email->getData());
			}

			$em = $this->getDoctrine()->getManager();
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Profil saved !');
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
}
