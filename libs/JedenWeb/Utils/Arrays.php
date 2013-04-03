<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Utils;

use JedenWeb;
use Nette;

/**
 * @author Filip Procházka <filip.prochazka@kdyby.org>
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 *
 * @method mixed get() get(array $arr, $key, $default = NULL)
 * @method mixed getRef() getRef(array & $arr, $key)
 * @method array mergeTree() mergeTree(array $arr1, $arr2)
 * @method int searchKey() searchKey(array $arr, $key)
 * @method void insertBefore() insertBefore(array &$arr, $key, array $inserted)
 * @method void insertAfter() insertAfter(array &$arr, $key, array $inserted)
 * @method void renameKey() renameKey(array &$arr, $oldKey, $newKey)
 * @method array grep() grep(array $arr, $pattern, $flags = 0)
 */
final class Arrays extends Nette\Object
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
		return callback('Nette\Utils\Arrays', $name)->invokeArgs($args);
	}



	/**
	 * Returns array of $column from multi array
	 * @param type $array
	 * @param type $column
	 * @return type
	 */
	public static function fetchColumn($array, $column)
	{
		$ret = array();
		foreach ($array as $id => $row) {
			$ret[$id] = $row[$column];
		}
		return $ret;
	}



	/**
	 * @param array $array
	 * @param array|string|callback $columns
	 * @param bool $append
	 *
	 * @return array
	 */
	public static function groupBy(array $array, $columns, $append = FALSE)
	{
		if (!is_callable($columns)) {
			$columns = is_array($columns)
				? $columns
				: Nette\Utils\Strings::split($columns, '~\s*,\s*~');
		}

		$grouped = array();
		foreach ($array as $item) {
			if (is_callable($columns)) {
				$keys = $columns($item);

			} else {
				$keys = array_map(function ($key) use ($item) {
					return is_object($item) ? $item->$key : $item[$key];
				}, $columns);
			}

			$ref =& Nette\Utils\Arrays::getRef($grouped, $keys);
			if ($append) {
				if (!is_array($ref)) {
					$ref = array();
				}
				$ref[] = $item;

			} else {
				$ref = $item;
			}
			unset($ref);
		}

		return $grouped;
	}



	/**
	 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
	 * @param mixed $array
	 * @param mixed $subkey
	 * @param int $sortType
	 * @return mixed
	 */
	public static function sortBySubkey(&$array, $subkey, $sortType = SORT_ASC) {
		if(count($array)){
			foreach ($array as $subarray) {
				$keys[] = is_object($subarray) ? $subarray->$subkey : $subarray[$subkey];
			}
			array_multisort($keys, $sortType, $array);
		}
		return $array;
	}



	/**
	 * @param array $array
	 * @param callable $callback
	 * @return array
	 */
	public static function flatMap(array $array, $callback = NULL)
	{
		$items = array();
		array_walk_recursive($array, function ($item, $key) use (&$items) {
			$items[] = $item;
		});

		if ($callback === NULL) {
			return $items;
		}

		return array_map(callback($callback), $items);
	}



	/**
	 * @param array $array
	 * @param callable $callback
	 * @return array
	 */
	public static function flatFilter(array $array, $filter = NULL)
	{
		if ($filter === NULL) {
			return self::flatMap($array);
		}

		return array_filter(self::flatMap($array), callback($filter));
	}



	/**
	 * @param array|\Traversable $array
	 * @param callback $callback
	 * @return array
	 */
	public static function flatMapAssoc($array, $callback)
	{
		$callback = callback($callback);
		$result = array();
		$walker = function ($array, $keys = array()) use (&$walker, &$result, $callback) {
			foreach ($array as $key => $value) {
				$currentKeys = $keys + array(count($keys) => $key);
				if (is_array($value)) {
					$walker($value, $currentKeys);
					continue;
				}
				$result[] = $callback($value, $currentKeys);
			}

			return $result;
		};

		return $walker($array);
	}



	/**
	 * @param array $arr
	 * @param bool $strict
	 * @return array
	 * @throws JedenWeb\InvalidStateException
	 */
	public static function flatternKeys(array $arr, $strict = FALSE)
	{
		$res = array();
		array_walk_recursive($arr, function($v, $k) use ($res) {
			if (array_key_exists($k, $res)) {
				if ($strict) {
					throw new JedenWeb\InvalidStateException("There is a conflict in keys in giver array.");
				} else {
					$res[] = $v;
				}
			} else {
				$res[$k] = $v;
			}
		});

		return $res;
	}


	
	/**
	 * @param array $arr
	 * @param array $key
	 * @param callable $callback
	 * @return mixed
	 */
	public static function callOnRef(& $arr, $key, $callback)
	{
		if (!is_callable($callback, TRUE)) {
			throw new JedenWeb\InvalidArgumentException("Invalid callback.");
		}

		return $callback(Nette\Utils\Arrays::getRef($arr, $key));
	}



	/**
	 * @param array $arr
	 * @param string $start
	 * @param string $end
	 * @return array
	 */
	public static function sliceAssoc(array $arr, $start, $end = NULL)
	{
		$sliced = array();
		foreach ($arr as $key => $value) {
			if ($key === $start || ($key === $end && $end !== NULL)) {
				if ($sliced) {
					$sliced[$key] = $value;
					break;
				}
				$sliced[$key] = $value;

			} elseif ($sliced) {
				$sliced[$key] = $value;
			}
		}
		return $sliced;
	}



	/**
	 * @param array $one
	 * @param array $two
	 *
	 * @return array
	 */
	public static function zipper(array $one, array $two)
	{
		$output = array();
		while ($one && $two) {
			$output[] = array_shift($one);
			$output[] = array_shift($two);
		}
		return array_merge($output, $one ?: array(), $two ?: array());
	}

}
