<?php

namespace JedenWeb\Utils;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Validators extends Nette\Utils\Validators
{

	/**
	 * @param string|int
	 * @return boolean
	 */
	public static function isCreditCard($number)
	{
		if (strlen($s = Nette\Utils\Strings::replace($number, '([ -])', '')) !== 16) {
			return FALSE;
		}

		$s = str_split($s);

		for ($i = 1; $i <= 15; $i+=2) {
			$digits[$i] = ($s[$i-1] * 2);
			$digits[$i+1] = $s[$i];
		}

		$splitAndSum = function($string) {
			$int = 0;
			foreach (str_split($string) as $i) {
				$int += $i;
			}
			return $int;
		};

		return $splitAndSum(implode('', $digits)) % 10 === 0;
	}

}
