<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Latte\Macros;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class ConfirmMacro extends Nette\Latte\Macros\MacroSet
{

	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return string
	 */
	public static function filter(Nette\Latte\MacroNode $node, Nette\Latte\PhpWriter $writer)
	{
		$content = $node->args;
		return $writer->write('echo " data-confirm=\"' . $content . '\""');
	}



	/**
	 * @param \Nette\Latte\Compiler $compiler
	 * @return self
	 */
	public static function install(\Nette\Latte\Compiler $compiler)
	{
		$me = new static($compiler);
		$me->addMacro('confirm', NULL, NULL, array($me, "filter"));
		return $me;
	}

}
