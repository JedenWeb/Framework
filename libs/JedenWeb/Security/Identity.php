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
class Identity extends Nette\Security\Identity
{

	/**
	 * @param string $password
	 * @param string|NULL $salt
	 * @return bool
	 */
	public function isEqual($password, $salt = NULL)
	{
		return $this->password->isEqual($password, $salt);
	}

}
