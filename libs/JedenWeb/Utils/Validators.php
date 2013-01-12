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
class Validators extends Nette\Utils\Validators
{

	/**
	 * @param string|int $number
	 * @return boolean
	 */
	public static function isCreditCard($number)
	{
		if (strlen($s = Nette\Utils\Strings::replace($number, '([ -])', '')) !== 16) {
			return false;
		}

		$s = str_split($s);

		$digits = array();
		for ($i = 1; $i <= 15; $i+=2) {
			$digits[$i] = ($s[$i-1] * 2);
			$digits[$i+1] = $s[$i];
		}

		return self::splitAndSum(implode('', $digits)) % 10 === 0;
	}



	/**
	 * @param string $string
	 * @return int
	 */
	private static function splitAndSum($string)
	{
		$int = 0;
		foreach (str_split($string) as $i) {
			$int += $i;
		}
		return $int;
	}

}
