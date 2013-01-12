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
class DialogMacro extends Nette\Latte\Macros\MacroSet
{

	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return string
	 */
	public function start(Nette\Latte\MacroNode $node, Nette\Latte\PhpWriter $writer)
	{
		return $writer->write('echo \CoreModule\Macros\DialogMacro::make(%node.word, %node.array?)');
	}



	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return string
	 */
	public function stop(Nette\Latte\MacroNode $node, Nette\Latte\PhpWriter $writer)
	{
		return $writer->write('?></div><?php');
	}



	/**
	 * @param \Nette\Latte\Compiler $compiler
	 * @return self
	 */
	public static function install(\Nette\Latte\Compiler $compiler)
	{
		$me = new static($compiler);
		$me->addMacro('dialog', array($me, "start"), array($me, "stop"));
		return $me;
	}

}
