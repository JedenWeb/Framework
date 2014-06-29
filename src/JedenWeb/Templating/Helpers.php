<?php

namespace JedenWeb\Templating;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Helpers extends Nette\Object
{

	/** @var \Nette\DI\Container */
	protected $helpers = array();
	


	/**
	 * @param string
	 * @param callable
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
