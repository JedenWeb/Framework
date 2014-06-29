<?php

namespace JedenWeb\Templating\Helpers;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class PluralHelper extends Nette\Object
{	
	
	/**
	 * @param string
	 * @return string
	 */
	public static function plural($n) {
		$args = func_get_args();
        return $args[($n == 1) ? 1 : (($n >= 2 && $n <= 4) ? 2 : 3)];
	}	
	
}
