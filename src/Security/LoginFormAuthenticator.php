<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
	/**
	 * @var UserRepository
	 */
	private $userRepository;

	/**
	 * @var RouterInterface
	 */
	private $router;

	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $passwordEncoder;

	public function __construct(UserRepository $userRepository, RouterInterface $router, UserPasswordEncoderInterface $passwordEncoder)
	{

		$this->userRepository = $userRepository;
		$this->router = $router;
		$this->passwordEncoder = $passwordEncoder;
	}

	public function supports(Request $request)
	{
		if ($request->attributes->get('_route') === 'login' && $request->isMethod('POST')) {

			$this->checkCaptcha($request);

			return true;
		}

		return false;
	}

	public function getCredentials(Request $request)
	{
		return [
			'username' => $request->request->get('_username'),
			'password' => $request->request->get('_password'),
			'captcha' => $request->request->get('captcha')
		];
	}

	public function getUser($credentials, UserProviderInterface $userProvider)
	{
		return $this->userRepository->findOneBy(['username' => $credentials['username'], 'email_confirmed' => true]);
	}

	public function checkCredentials($credentials, UserInterface $user)
	{
		$password = $credentials['password'];

		if ($this->passwordEncoder->isPasswordValid($user, $password) && $user->getEmailConfirmed()) {
			return true;
		}

		throw new UserMessageAuthenticationException('Wrong password !');
	}

	public function checkCaptcha(Request $request)
	{
		$captcha = $request->request->get('captcha');

		if ($captcha != $request->getSession()->get('phrase')) {
			throw new UserMessageAuthenticationException('Wrong captcha !');
		}
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
	{
		return new RedirectResponse($this->router->generate('index'));
	}

	/**
	 * Return the URL to the login page.
	 *
	 * @return string
	 */
	protected function getLoginUrl()
	{
		return $this->router->generate('login');
	}
}
