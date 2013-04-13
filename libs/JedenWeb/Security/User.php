<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 */

namespace JedenWeb\Security;

use JedenWeb;
use Nette;
use Nette\Reflection;
use Nette\Application\ForbiddenRequestException;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class User extends Nette\Security\User
{
	
	/**
	 * @param \Reflector|\Nette\Reflection\ClassType|\Nette\Reflection\Method $element
	 * @param string $message
	 *
	 * @throws \Nette\Application\ForbiddenRequestException
	 * @throws \Kdyby\UnexpectedValueException
	 * @return bool
	 */
	public function protectElement(\Reflector $element, $message = NULL)
	{
		if (!$element instanceof Reflection\Method && !$element instanceof Reflection\ClassType) {
			return FALSE;
		}

		$user = (array)$element->getAnnotation('User');
		$message = isset($user['message']) ? $user['message'] : $message;
		if (in_array('loggedIn', $user) && !$this->isLoggedIn()) {
			throw new ForbiddenRequestException($message ?: "User " . $this->getIdentity()->getId() . " is not logged in.");

		} elseif (isset($user['role']) && !$this->isInRole($user['role'])) {
			throw new ForbiddenRequestException($message ? : "User " . $this->getIdentity()->getId() . " is not in role '" . $user['role'] . "'.");

		} elseif ($element->getAnnotation('user')) {
			throw new JedenWeb\UnexpectedValueException("Annotation 'user' in $element should have been 'User'.");
		}

		$allowed = (array)$element->getAnnotation('Allowed');
		$message = isset($allowed['message']) ? $allowed['message'] : $message;
		if ($allowed) {
			$resource = isset($allowed[0]) ? $allowed[0] : IAuthorizator::ALL;
			$privilege = isset($allowed[1]) ? $allowed[1] : IAuthorizator::ALL;
			$this->needAllowed($resource, $privilege, $message);

		} elseif ($element->getAnnotation('allowed')) {
			throw new JedenWeb\UnexpectedValueException("Annotation 'allowed' in $element should have been 'Allowed'.");
		}
	}
	
}
