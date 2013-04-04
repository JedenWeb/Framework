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
class User extends Nette\Security\User
{

	/** @var \Nette\Security\IUserStorage */
	private $storage;


	
	/**
	 * @param Nette\Security\IUserStorage $storage
	 * @param Nette\DI\Container $context
	 */
	public function __construct(Nette\Security\IUserStorage $storage, Nette\DI\Container $context)
	{
		parent::__construct($storage, $context);

		$this->storage = $storage;

		$this->onLoggedIn[] = function($user) {
			$identity = $user->identity;

			\Nette\Diagnostics\Debugger::log(
				"User \"$identity->username\" has logged in ($_SERVER[REMOTE_ADDR]).",
				'login'
			);
		};
		$this->onLoggedOut[] = function($user) {
			$identity = $user->identity;

			\Nette\Diagnostics\Debugger::log(
				"User \"$identity->username\" has logged out ($_SERVER[REMOTE_ADDR]).",
				'login'
			);
		};
	}

}
