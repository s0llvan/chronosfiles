<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Defuse\Crypto\File as CryptoFile;
use Defuse\Crypto\Key;
use App\Entity\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Defuse\Crypto\Crypto;

class FileController extends Controller
{
    /**
    * @Route("/files", name="files")
    */
    public function index()
    {
        $files = $this->getUser()->getFiles();

        $user_key_encoded = $this->get('session')->get('encryption_key');
        $user_key = Key::loadFromAsciiSafeString($user_key_encoded);

        foreach ($files as $file) {
            $fileName = $file->getFileName();
            $fileNameLocation = $file->getFileNameLocation();

            try {
                $fileName = Crypto::decrypt($fileName, $user_key);
                $fileNameLocation = Crypto::encrypt($fileNameLocation, $user_key);
            } catch (Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) {

            }

            $file->setFileName($fileName);
            $file->setFileNameLocation($fileNameLocation);
        }

        return $this->render('file/index.html.twig', [
            'files' => $files
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
}
