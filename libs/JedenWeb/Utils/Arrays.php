<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Utils;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Arrays extends Nette\Object
{

	/**
	 * Returns array of $column from multi array
	 * @param type $array
	 * @param type $column
	 * @return type
	 */
	public static function fetchColumn($array, $column)
	{
		foreach ($array as $id => $row) {
			$ret[$id] = $row[$column];
		}
		return $ret;
	}



	/**
	 * @param array $arr
	 * @param mixed $oldKey
	 * @param mixed $newKey
	 */
	public static function renameKey(&$arr, $oldKey, $newKey)
	{
		$offset = self::searchKey($arr, $oldKey);
		if ($offset !== false) {
			$keys = array_keys($arr);
			$keys[$offset] = $newKey;
			$arr = array_combine($keys, $arr);
		}
	}



	/**
	 * Mirror of Nette\Utils\Arrays
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 */
	public static function __callStatic($name, $args)
	{
		return callback('Nette\Utils\Arrays', $name)->invokeArgs($args);
	}

}
