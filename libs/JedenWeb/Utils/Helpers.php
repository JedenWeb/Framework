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
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
final class Helpers extends Nette\Object
{
	
	/**
	 * @throws JedenWeb\StaticClassException
	 */
	final public function __construct()
	{
		throw new JedenWeb\StaticClassException;
	}
	
	
	
	/**
	 * Converts \Nette\Database\Table\ActiveRow[] to array
	 * @param array|\Nette\Database\Table\ActiveRow|FALSE $rows
	 * @return array|bool
	 */
	public static function toArray($rows)
	{
		if (is_bool($rows)) {
			return $rows;
		}
		
		if ($rows instanceof \Nette\Database\Table\ActiveRow) {
			return $rows->toArray();
		}
		
		array_walk_recursive($rows, function(&$row) {
			if ($row instanceof \Nette\Database\Table\ActiveRow) {
				$row = $row->toArray();
			}
		});
		
		return $rows;
	}

}
