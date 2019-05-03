<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\RoleRepository;
use App\Repository\FileRepository;
use App\Repository\CategoryRepository;

class IndexController extends AbstractController
{
	/**
	 * @Route("/admin", name="admin")
	 */
	public function index(UserRepository $userRepository, RoleRepository $roleRepository, FileRepository $fileRepository, CategoryRepository $categoryRepository)
	{
		return $this->render('admin/index.html.twig', [
			'users' => $userRepository->findAll(),
			'roles' => $roleRepository->findAll(),
			'files' => $fileRepository->findAll(),
			'categories' => $categoryRepository->findAll()
		]);
	}
}
