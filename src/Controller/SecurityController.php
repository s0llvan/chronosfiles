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
		$captcha = $this->setCaptcha($request);

		return $this->render('login.html.twig', [
			'last_username' => $helper->getLastUsername(),
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

	public function setCaptcha(Request $request)
	{
		$captcha = new CaptchaBuilder();
		$captcha->build();
		$request->getSession()->set('phrase', $captcha->getPhrase());

		return $captcha;
	}
}
