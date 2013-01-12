<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Config\Extensions;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class LoaderExtension extends \Nette\Config\CompilerExtension
{

	/**
	 * @var array
	 */
	private $defaults = array();


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);
	}

}
