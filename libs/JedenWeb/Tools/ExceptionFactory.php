<?php

namespace JedenWeb\Tools;

use JedenWeb;
use Nette;

/**
 * @author Filip Procházka <filip.prochazka@kdyby.org>
 */
class ExceptionFactory extends Nette\Object
{

	/**
	 * @param integer $argument number of argument
	 * @param string $type required type of argument
	 * @param mixed|NULL $value the given value
	 * @return JedenWeb\InvalidArgumentException
	 */
	public static function invalidArgument($argument, $type, $value = NULL)
	{
		$stack = debug_backtrace(FALSE);

		return new JedenWeb\InvalidArgumentException(
			sprintf('Argument #%d %s of %s::%s() must be a %s',
				$argument,
				$value !== NULL ? ' (' . $value . ') ' : ' ',
				$stack[1]['class'],
				$stack[1]['function'],
				$type
			)
		);
	}

}
