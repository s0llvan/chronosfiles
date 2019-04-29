<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class UserMessageAuthenticationException extends AuthenticationException
{
	protected $message;

	public function __construct(string $message)
	{
		$this->message = $message;
	}

	public function getMessageKey()
	{
		return $this->message;
	}
}
