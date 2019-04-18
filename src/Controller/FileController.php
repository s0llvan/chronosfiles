<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Defuse\Crypto\File as CryptoFile;
use Defuse\Crypto\Key;
use App\Entity\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Defuse\Crypto\Crypto;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Category;
use App\Form\SearchFileType;
use App\Form\FileMoveType;
use App\Repository\FileRepository;
use App\Entity\User;
use App\Form\FileDeleteType;

class FileController extends AbstractController
{
	/**
	 * @Route("/files", name="files")
	 */
	public function index(Request $request, FileRepository $fileRepository)
	{
		$user = $this->getUser();

		$formMoveFile = $this->checkMoveFileForm($request, $fileRepository);
		$formDeleteFile = $this->checkDeleteFileForm($request);

		$files = $user->getFiles();

		$user_key_encoded = $this->get('session')->get('encryption_key');
		$user_key = Key::loadFromAsciiSafeString($user_key_encoded);

		foreach ($files as $file) {
			$fileName = $file->getFileName();
			$fileNameLocation = $file->getFileNameLocation();

			try {
				$fileName = Crypto::decrypt($fileName, $user_key);
				$fileNameLocation = Crypto::encrypt($fileNameLocation, $user_key);
			} catch (Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) { }

			$file->setFileName($fileName);
			$file->setFileNameLocation($fileNameLocation);
		}

		$form = $this->getSearchForm();

		return $this->render('file/index.html.twig', [
			'files' => $files,
			'form' => $form->createView(),
			'formMoveFile' => $formMoveFile->createView(),
			'formDeleteFile' => $formDeleteFile->createView()
		]);
	}

	/**
	 * @Route("/files/search", name="files_search")
	 */
	public function search(Request $request, FileRepository $fileRepository)
	{
		$user = $this->getUser();
		$files = $user->getFiles();
		$filesResults = [];

		$formMoveFile = $this->checkMoveFileForm($request, $fileRepository);
		$formDeleteFile = $this->checkDeleteFileForm($request);

		$form = $this->createForm(SearchFileType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			foreach ($files as $file) {
				$user_key_encoded = $this->get('session')->get('encryption_key');
				$user_key = Key::loadFromAsciiSafeString($user_key_encoded);

				$fileName = $file->getFileName();
				$fileNameLocation = $file->getFileNameLocation();

				try {
					$fileName = Crypto::decrypt($fileName, $user_key);
					$fileNameLocation = Crypto::encrypt($fileNameLocation, $user_key);
				} catch (Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) { }

				$file->setFileName($fileName);
				$file->setFileNameLocation($fileNameLocation);

				$keywords = $form->get('search')->getData();

				$fileName = strtolower($file->getFileName());
				$keywords = strtolower($keywords);

				if (strpos($fileName, $keywords) !== false) {
					$filesResults[] = $file;
				}
			}
		}

		return $this->render('file/index.html.twig', [
			'files' => $filesResults,
			'form' => $form->createView(),
			'formMoveFile' => $formMoveFile->createView(),
			'formDeleteFile' => $formDeleteFile->createView()
		]);
	}

	/**
	 * @Route("/files/{id}", name="files_category", requirements = { "id" = "[0-9]+" })
	 */
	public function categories(Request $request, Category $category, FileRepository $fileRepository)
	{
		$user = $this->getUser();

		if ($category->getUser() != $user) {
			return $this->redirect($this->generateUrl('files'));
		}

		$formMoveFile = $this->checkMoveFileForm($request, $fileRepository);
		$formDeleteFile = $this->checkDeleteFileForm($request);

		$files = $category->getFiles();

		$user_key_encoded = $this->get('session')->get('encryption_key');
		$user_key = Key::loadFromAsciiSafeString($user_key_encoded);

		foreach ($files as $file) {
			$fileName = $file->getFileName();
			$fileNameLocation = $file->getFileNameLocation();

			try {
				$fileName = Crypto::decrypt($fileName, $user_key);
				$fileNameLocation = Crypto::encrypt($fileNameLocation, $user_key);
			} catch (Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) { }

			$file->setFileName($fileName);
			$file->setFileNameLocation($fileNameLocation);
		}

		$form = $this->getSearchForm();

		return $this->render('file/index.html.twig', [
			'files' => $files,
			'category' => $category,
			'form' => $form->createView(),
			'formMoveFile' => $formMoveFile->createView(),
			'formDeleteFile' => $formDeleteFile->createView()
		]);
	}

	/**
	 * @Route("/download/{fileNameLocation}", name="download")
	 */
	public function download(string $fileNameLocation)
	{
		$user_key_encoded = $this->get('session')->get('encryption_key');
		$user_key = Key::loadFromAsciiSafeString($user_key_encoded);

		$fileNameLocation = Crypto::decrypt($fileNameLocation, $user_key);

		$file = $this->getDoctrine()
			->getRepository(File::class)
			->findOneByFileNameLocation($fileNameLocation);

		if (!$file) {
			throw $this->createNotFoundException(
				'No file found for filename ' . $fileName
			);
		}

		if ($file->getUser() != $this->getUser()) {
			return $this->redirect($this->generateUrl('files'));
		}

		$fileNameLocation = $file->getFileNameLocation();
		$fileName = $file->getFileName();
		$fileName = Crypto::decrypt($fileName, $user_key);
		$filePath = $this->getParameter('upload_directory') . $fileNameLocation;
		$newFilePath = $filePath . '.bak';

		CryptoFile::decryptFile($filePath, $newFilePath, $user_key);

		$response = new BinaryFileResponse($newFilePath);
		$response->setContentDisposition(
			ResponseHeaderBag::DISPOSITION_ATTACHMENT,
			$fileName
		);
		$response->deleteFileAfterSend(true);
		return $response;
	}

	/**
	 * @Route("/delete/{fileNameLocation}", name="delete")
	 */
	public function delete(string $fileNameLocation)
	{
		$user_key_encoded = $this->get('session')->get('encryption_key');
		$user_key = Key::loadFromAsciiSafeString($user_key_encoded);

		$fileNameLocation = Crypto::decrypt($fileNameLocation, $user_key);
		$filePath = $this->getParameter('upload_directory') . $fileNameLocation;

		$file = $this->getDoctrine()
			->getRepository(File::class)
			->findOneByFileNameLocation($fileNameLocation);

		if (!$file) {
			return $this->redirect($this->generateUrl('files'));
		}

		if ($file->getUser() == $this->getUser()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($file);
			$em->flush();

			if (file_exists($filePath)) {
				unlink($filePath);
			}
		}

		return $this->redirect($this->generateUrl('files'));
	}

	/**
	 * @Route("/files/uncategorized", name="files_uncategorized")
	 */
	public function uncategorized(Request $request, FileRepository $fileRepository)
	{
		$user = $this->getUser();
		$files = $user->getUncategorizedFiles();

		$formMoveFile = $this->checkMoveFileForm($request, $fileRepository);
		$formDeleteFile = $this->checkDeleteFileForm($request);

		$user_key_encoded = $this->get('session')->get('encryption_key');
		$user_key = Key::loadFromAsciiSafeString($user_key_encoded);

		foreach ($files as $file) {
			$fileName = $file->getFileName();
			$fileNameLocation = $file->getFileNameLocation();

			try {
				$fileName = Crypto::decrypt($fileName, $user_key);
				$fileNameLocation = Crypto::encrypt($fileNameLocation, $user_key);
			} catch (Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) { }

			$file->setFileName($fileName);
			$file->setFileNameLocation($fileNameLocation);
		}

		$form = $this->getSearchForm();

		return $this->render('file/index.html.twig', [
			'files' => $files,
			'form' => $form->createView(),
			'formMoveFile' => $formMoveFile->createView(),
			'formDeleteFile' => $formDeleteFile->createView()
		]);
	}

	private function getSearchForm()
	{
		return $this->createForm(SearchFileType::class, null, [
			'action' => $this->generateUrl('files_search')
		]);
	}

	private function checkMoveFileForm(Request $request, FileRepository $fileRepository)
	{
		$user = $this->getUser();
		$form = $this->createForm(FileMoveType::class, null, [
			'user' => $user
		]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$category = $form->get('category')->getData();
			$json = $form->get('file')->getData();

			if ($id = json_decode($json)) {
				if ($files = $fileRepository->findBy([
					'user' => $user,
					'id' => $id
				])) {
					foreach ($files as $file) {
						if ($user->getCategories()->contains($category) || !$category) {
							$file->setCategory($category);
						}
					}

					$em = $this->getDoctrine()->getManager();
					$em->flush();
				}
			}
		}

		return $form;
	}

	private function checkDeleteFileForm(Request $request)
	{
		$form = $this->createForm(FileDeleteType::class);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$json = $form->get('file')->getData();

			if ($files = json_decode($json)) {
				$files = $this->getUser()->getFiles()->filter(
					function ($file) use ($files) {
						return in_array($file->getId(), $files);
					}
				);

				$em = $this->getDoctrine()->getManager();
				foreach ($files as $file) {
					$em->remove($file);
				}
				$em->flush();
			}
		}

		return $form;
	}
}
