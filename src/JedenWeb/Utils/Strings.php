<?php

namespace JedenWeb\Utils;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
final class Strings extends Nette\Object
{
	
	/**
	 * Static class - cannot be instantiated.
	 *
	 * @throws \JedenWeb\StaticClassException
	 */
	final public function __construct()
	{
		throw new JedenWeb\StaticClassException;
	}



	/**
	 * Mirror of Nette\Utils\Arrays
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 */
	public static function __callStatic($name, $args)
	{
		return callback('Nette\Utils\Strings', $name)->invokeArgs($args);
	}
	
	
	
	/**
	 * @param string $needle
	 * @param string $haystack
	 * @param int $offset
	 * @return int|FALSE
	 */
	public static function strpos($needle, $haystack, $offset = 1)
	{
		$arr = explode($needle, $haystack);

		switch($offset) {
			case $offset === 0:
				return FALSE;
			case $offset > max(array_keys($arr)):
				return FALSE;
			default:
				return strlen(implode($needle, array_slice($arr, 0, $offset)));
		}
	}
	
	
	
	/**
	 * 
	 * @param string $needle
	 * @param string $haystack
	 * @param int $offset
	 * @return int|FALSE
	 */
	public static function strrpos($needle, $haystack, $offset = 1)
	{
		$arr = array_reverse(explode($needle, $haystack));
		
		switch($offset) {
			case $offset === 0:
				return FALSE;
			case $offset > max(array_keys($arr)):
				return FALSE;
			default:
				$inverted = strlen(implode($needle, array_slice($arr, 0, $offset)));
				$actual = (strlen($haystack) - 1) - $inverted;
				return $actual;
		}
	}
	
}
