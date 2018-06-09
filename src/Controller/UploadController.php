<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\File;
use App\Form\FileType;
use Defuse\Crypto\File as CryptoFile;
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;

class UploadController extends Controller
{
    /**
    * @Route("/upload", name="upload")
    */
    public function index(Request $request)
    {
        $user = $this->getUser();
        $fileEntity = new File();
        $form = $this->createForm(FileType::class, $fileEntity);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('fileName')->getData();
            $fileOriginalName = $file->getClientOriginalName();

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
            $fileEntity->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($fileEntity);
            $em->flush();
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
