<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Templating;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Helpers extends Nette\Object
{

	/**
	 * @var \Nette\DI\Container
	 */
	protected $helpers = array();
	


	/**
	 * @param string $name
	 * @param callable $factory
	 */
	public function addHelper($name, $factory)
	{
		$this->helpers[$name] = $factory;
	}



	/**
	 * @param string Helper name
	 * @return callable
	 */
	public function loader($helper)
	{
		if (method_exists(__CLASS__, $helper)) {
			return array(__CLASS__, $helper);
		} elseif (isset($this->helpers[$helper])) {
			return $this->helpers[$helper];
		}
	}

}
