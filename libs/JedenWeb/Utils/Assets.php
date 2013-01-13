<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Flame\Utils;

use Nette;

/**
 * @author Jiří Šifalda <sifalda.jiri@gmail.com>
 */
class Assets extends Nette\Object
{

	/**
	 * @param string $path
	 * @return mixed
	 */
	public static function getFileNameFromPath($path)
	{
		if (strpos($path, DIRECTORY_SEPARATOR) === false) {
			return $path;
		} else {
			return str_replace(DIRECTORY_SEPARATOR, '', strrchr($path, DIRECTORY_SEPARATOR));
		}
	}



	/**
	 * @param string $name
	 * @param string $oldType
	 * @param string $newType
	 * @return mixed
	 */
	public static function modifyType($name, $oldType =  'less', $newType = 'css')
	{
		return str_replace('.' . $oldType, '.' . $newType, $name);
	}



	/**
	 * @param string $content
	 * @return mixed
	 */
	public static function minifyCss($content)
	{
		return str_replace('; ',';',str_replace(' }','}',str_replace('{ ','{',str_replace(array("\r\n","\r","\n","\t",'  ','    ','    '),"",preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!','',$content)))));
	}

}
