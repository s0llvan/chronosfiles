<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\LoginType;
use Symfony\Component\HttpFoundation\Request;
use Gregwar\Captcha\CaptchaBuilder;

class SecurityController extends AbstractController
{
	/**
	 * @Route("/login", name="login")
	 */
	public function login(AuthenticationUtils $helper, Request $request): Response
	{
		$captcha = $this->setCaptcha();

		return $this->render('login.html.twig', [
			// dernier username saisi (si il y en a un)
			'last_username' => $helper->getLastUsername(),
			// La derniere erreur de connexion (si il y en a une)
			'error' => $helper->getLastAuthenticationError(),
			'captcha' => $captcha
		]);
	}

	/**
	 * @Route("/logout", name="logout")
	 */
	public function logout(): void
	{
		throw new \Exception('This should never be reached!');
	}

	public function setCaptcha()
	{
		$captcha = new CaptchaBuilder();
		$captcha->build();
		$_SESSION['phrase'] = $captcha->getPhrase();

		return $captcha;
	}
}
