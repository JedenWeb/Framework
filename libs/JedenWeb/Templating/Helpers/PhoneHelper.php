<?php

namespace JedenWeb\Templating\Helpers;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class PhoneHelper extends Nette\Object
{
	
	/**
	 * @var array
	 */
	private static $codes = array(
		'cz' => '+420', // ^(+420 ?)?[0-9]{3} [0-9]{3} [0-9]{3}$
		'sk' => '+421'
	);
	
	
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
