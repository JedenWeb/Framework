<?php

namespace Kdyby\Tools;

use Kdyby;
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
	 * @return Kdyby\InvalidArgumentException
	 */
	public static function invalidArgument($argument, $type, $value = NULL)
	{
		$stack = debug_backtrace(FALSE);

		return new Kdyby\InvalidArgumentException(
			sprintf('Argument #%d%sof %s::%s() must be a %s',
				$argument,
				$value !== NULL ? ' (' . $value . ') ' : ' ',
				$stack[1]['class'],
				$stack[1]['function'],
				$type
			)
		);
	}

}
