<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Module;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
final class Helpers extends Nette\Object
{

	/**
	 * Expands @fooModule/path/....
	 * @static
	 * @param $path
	 * @param array $modules
	 * @return string
	 * @throws \Nette\InvalidArgumentException
	 */
	public static function expandPath($path, array $modules)
	{
		if (substr($path, 0, 1) !== '@') {
			return $path;
		}

		$pos = strpos($path, 'Module');
		$module = lcfirst(substr($path, 1, $pos - 1));

		if (!isset($modules[$module])) {
			throw new \Nette\InvalidArgumentException("Module '{$module}' does not exist.");
		}

		return $modules[$module]['path'] . substr($path, $pos + 6);
	}


	/**
	 * Expands @fooModule/path/....
	 * @static
	 * @param $path
	 * @param array $modules
	 * @return string
	 * @throws \Nette\InvalidArgumentException
	 */
	public static function expandResource($path)
	{
		if (substr($path, 0, 1) !== '@') {
			return $path;
		}

		$pos = strpos($path, 'Module');
		$module = lcfirst(substr($path, 1, $pos - 1));

		return 'resources/' . $module . 'Module' . substr($path, $pos + 6);
	}
	
}
