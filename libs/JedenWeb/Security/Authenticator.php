<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Security;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
abstract class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator
{

	/**
	 * @var array $credentials
	 */
	public abstract function authenticate(array $credentials);


	
	/**
	 * @param string $password
	 * @param string $salt
	 * @return string
	 */
	public function calculateHash($password, $salt)
	{
		return crypt((string) $password, "$2y$07$$salt$");
	}

}
