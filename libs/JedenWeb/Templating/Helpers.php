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
class Helpers extends \Nette\Object
{

	/**
	 * @var \Nette\DI\Container
	 */
	protected $container;

	/**
	 * @var \Nette\DI\Container
	 */
	protected $helpers = array();
	
	
	
	/**
	 * @var array
	 */
	private static $codes = array(
		'cz' => '+420', // ^(+420 ?)?[0-9]{3} [0-9]{3} [0-9]{3}$
		'sk' => '+421'
	);	



	/**
	 * @param \Nette\DI\Container $container
	 */
	function __construct(\Nette\DI\Container $container)
	{
		$this->container = $container;
	}



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
		if (isset($this->helpers[$helper])) {
			return callback($this->helpers[$helper], "filter");
		}

		if (method_exists(__CLASS__, $helper)) {
			return callback(__CLASS__, $helper);
		}
	}
	
	
	
	/**
	 * @param string $s
	 * @param string $country
	 * @return string
	 */
	public static function phone($s, $country = 'cz') {
		if (in_array($country, array('cz', 'sk'))) {
			$code = static::$codes[$country];

			if (preg_match('/^[0-9]{3} [0-9]{3} [0-9]{3}$/', $s)) {
				return '+'. $code .' ' . $s;
			}
			if (preg_match('/^[0-9]{3}[0-9]{3}[0-9]{3}$/', $s)) {
				return '+'. $code .' ' . implode(' ', str_split($s, 3));
			}

			if (preg_match('/^\+'.$code.' [0-9]{3} [0-9]{3} [0-9]{3}$/', $s)) {
				return $s;
			}
			if (preg_match('/^\+'.$code.'[0-9]{3}[0-9]{3}[0-9]{3}$/', $s)) {
				return '+' . implode(' ', str_split(substr($s, 1), 3));
			}

			return $s;
		}
	}	

}
