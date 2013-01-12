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
class JsMacro extends \Nette\Latte\Macros\MacroSet
{

	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return string
	 */
	public static function filter(Nette\Latte\MacroNode $node, Nette\Latte\PhpWriter $writer)
	{
		$path = $node->tokenizer->fetchWord();
		$params = $writer->formatArray();

		if (!$node->args) {
			return 'ob_start();';
		}

		return ('$control->getPresenter()->getContext()->getService("jedenWeb.assetManager")->addJavascript("' . $path . '", ' . $params . '); ');
	}



	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return string
	 */
	public static function end(Nette\Latte\MacroNode $node, Nette\Latte\PhpWriter $writer)
	{
		$path = $node->tokenizer->fetchWord();
		$params = $writer->formatArray();

		if (!$node->args) {
			return '<?php $control->getPresenter()->getAssetManager()->addRawJavascript(ob_get_clean()); ?>';
		}

		return ('$control->getPresenter()->getContext()->getService("jedenWeb.assetManager")->addJavascript("' . $path . '", ' . $params . '); ');
	}



	/**
	 * @param \Nette\Latte\Compiler $compiler
	 * @return self
	 */
	public static function install(Nette\Latte\Compiler $compiler)
	{
		$me = new static($compiler);
		$me->addMacro('js', array($me, "filter"));
		$me->addMacro('@external', array($me, "filter"), array($me, "end"));

		return $me;
	}

}
