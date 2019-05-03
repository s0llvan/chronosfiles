<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RoleRepository;
use App\Entity\Role;
use App\Form\RoleAdminType;
use Symfony\Component\HttpFoundation\Request;

class RoleController extends AbstractController
{
	/**
	 * @Route("/admin/role", name="admin_role")
	 */
	public function index(RoleRepository $roleRepository)
	{
		return $this->render('admin/role/index.html.twig', [
			'roles' => $roleRepository->findAll()
		]);
	}

	/**
	 * @Route("/admin/role/{id}", name="admin_role_edit")
	 */
	public function edit(Request $request, Role $role)
	{
		$form = $this->createForm(RoleAdminType::class, $role);
		$form->handleRequest($request);

		// Check if form is submitted and valid
		if ($form->isSubmitted() && $form->isValid()) {

			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->flush();
		}

		return $this->render('admin/role/edit.html.twig', [
			'form' => $form->createView(),
			'role' => $role
		]);
	}

	/**
	 * @Route("/admin/role/{id}/delete", name="admin_role_delete")
	 */
	public function delete(Role $role)
	{
		$entityManager = $this->getDoctrine()->getManager();

		foreach ($role->getUsers() as $user) {
			foreach ($user->getFiles() as $file) {
				$filePath = $this->getParameter('upload_directory') . $file->getFileNameLocation();

				if (file_exists($filePath)) {
					unlink($filePath);
				}

				$entityManager->remove($file);
			}

			$entityManager->remove($user);
		}

		$entityManager->remove($role);
		$entityManager->flush();

		return $this->redirectToRoute('admin_role');
	}
}
