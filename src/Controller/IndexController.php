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
        return $this->render('index.html.twig');
    }
}
