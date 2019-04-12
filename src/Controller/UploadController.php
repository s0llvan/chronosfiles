<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\File;
use App\Form\FileType;
use Defuse\Crypto\File as CryptoFile;
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;

class UploadController extends AbstractController
{
    /**
    * @Route("/upload", name="upload")
    */
    public function index(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(FileType::class, null, [
            'user' => $user
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $files = $form->get('fileName')->getData();

            $em = $this->getDoctrine()->getManager();

            foreach ($files as $file) {
                    $fileEntity = new File();
                    $fileEntity->setCategory($form->get('category')->getData());

                    $fileOriginalName = $file->getClientOriginalName();
                    $fileSize = $file->getClientSize();

                    $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                    $tmpFileName = $fileName . '.bak';

                    $filePath = $this->getParameter('upload_directory') . $fileName;
                    $tmpFilePath = $filePath . '.bak';

                    $file->move(
                        $this->getParameter('upload_directory'),
                        $tmpFileName
                    );

                    $fileHash = md5_file($tmpFilePath);

                    $user_key_encoded = $this->get('session')->get('encryption_key');
                    $user_key = Key::loadFromAsciiSafeString($user_key_encoded);

                    CryptoFile::encryptFile($tmpFilePath, $filePath, $user_key);
                    $encryptedFileName = Crypto::encrypt($fileOriginalName, $user_key);

                    unlink($tmpFilePath);

                    $fileEntity->setFileName($encryptedFileName);
                    $fileEntity->setFileNameLocation($fileName);
                    $fileEntity->setFileHash($fileHash);
                    $fileEntity->setFileSize($fileSize);
                    $fileEntity->setUser($user);

                    $em->persist($fileEntity);
                }

            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Upload completed !');
        }

        return $this->render('upload/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
    * @return string
    */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}
