<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Extension\Latte\DI;

use JedenWeb;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class LatteExtension extends JedenWeb\Config\CompilerExtension
{

	public function loadConfiguration()
	{
		$this->addMacro('macros.head', 'JedenWeb\Latte\Macros\HeadMacro::install');
		$this->addMacro('macros.dialog', 'JedenWeb\Latte\Macros\DialogMacro::install');
		$this->addMacro('macros.confirm', 'JedenWeb\Latte\Macros\ConfirmMacro::install');
	}

}
