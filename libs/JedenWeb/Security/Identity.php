<?php

namespace JedenWeb\Security;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Identity extends Nette\Security\Identity
{
	
	public function __construct()
	{
		throw new \JedenWeb\NotImplementedException;
	}
	
}
