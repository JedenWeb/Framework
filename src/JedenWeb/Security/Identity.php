<?php

namespace JedenWeb\Security;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Identity extends Nette\Security\Identity
{

	const STATUS_INACTIVE = 'inactive';
	const STATUS_ACTIVE = 'active';
	const STATUS_BLOCKED = 'blocked';

}
