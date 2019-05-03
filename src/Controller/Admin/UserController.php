<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserAdminType;

class UserController extends AbstractController
{
	/**
	 * @Route("/admin/user", name="admin_user")
	 */
	public function index(UserRepository $userRepository)
	{
		return $this->render('admin/user/index.html.twig', [
			'users' => $userRepository->findAll()
		]);
	}

	/**
	 * @Route("/admin/user/{id}", name="admin_user_edit")
	 */
	public function edit(Request $request, User $user)
	{
		$this->denyAccessUnlessGranted($user->getRoles());

		$form = $this->createForm(UserAdminType::class, $user, [
			'super_admin' => $this->isGranted('ROLE_SUPER_ADMIN')
		]);
		$form->handleRequest($request);

		// Check if form is submitted and valid
		if ($form->isSubmitted() && $form->isValid()) {

			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->flush();

			$session = $this->get('session');
			$session->getFlashBag()->add('success', 'Informations saved');
		}

		return $this->render('admin/user/edit.html.twig', [
			'form' => $form->createView(),
			'user' => $user
		]);
	}

	/**
	 * @Route("/admin/user/{id}/delete", name="admin_user_delete")
	 */
	public function delete(User $user)
	{
		$entityManager = $this->getDoctrine()->getManager();

		foreach ($user->getFiles() as $file) {

			$filePath = $this->getParameter('upload_directory') . $file->getFileNameLocation();

			if (file_exists($filePath)) {
				unlink($filePath);
			}

			$entityManager->remove($file);
		}

		$entityManager->remove($user);
		$entityManager->flush();

		return $this->redirectToRoute('admin_user');
	}
}
