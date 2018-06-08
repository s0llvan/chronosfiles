<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

class IndexController extends Controller
{
    /**
    * @Route("/", name="index")
    */
    public function index()
    {
        $user_key_encoded = $this->get('session')->get('encryption_key');

        $user_key = Key::loadFromAsciiSafeString($user_key_encoded);

        $credit_card_number = "Hey !";
        $encrypted_card_number = Crypto::encrypt($credit_card_number, $user_key);

        dump($encrypted_card_number);

        $user_key = Key::loadFromAsciiSafeString($user_key_encoded);

        $encrypted_card_number = $encrypted_card_number;
        try {
            $credit_card_number = Crypto::decrypt($encrypted_card_number, $user_key);
            dump($credit_card_number);
        } catch (Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) {
            dump($ex);
        }

        return $this->render('index.html.twig');
    }
}
