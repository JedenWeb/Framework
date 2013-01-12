<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\DI;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class ContainerBuilder extends \Nette\DI\ContainerBuilder
{

	public function findByTag($tag)
	{
		\Nette\Diagnostics\Debugger::dump('called');die;
		$found = array();
		foreach ($this->definitions as $name => $def) {
			if (isset($def->tags[$tag])) {
				$found[$name] = $def->tags[$tag];
			}
		}
		return $found;
	}

}
