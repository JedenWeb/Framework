<?php

namespace JedenWeb\Security;

use Nette;
use Nette\Utils\Strings;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
abstract class Authenticator extends Nette\Object
{

	/**
	 * @var array $credentials
	 */
	public abstract function authenticate(array $credentials);

	
	/**
	 * Computes salted password hash.
	 * @param  string
	 * @return string
	 */
	public static function calculateHash($password, $salt = NULL)
	{
		if ($password === Strings::upper($password)) { // perhaps caps lock is on
			$password = Strings::lower($password);
		}
		return crypt($password, $salt ?: '$2y$07$' . Strings::random(22));
	}

}
