<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Localization\Filters;

/**
 * Latte macros for parse translator
 *
 * @author	Patrik Votoček
 *
 * @internal
 *
 * @property-read array $translations
 */
class LatteMacros extends \Nette\Latte\Macros\MacroSet
{

	/**
	 * @var array
	 */
	private $translations = array();



	/**
	 * @param \Nette\Latte\Parser
	 */
	public static function install(\Nette\Latte\Parser $parser)
	{
		$me = new static($parser);

		// _
		$me->addMacro('_', array($me, 'macroTranslate'));

		return $me;
	}



	/**
	 * @param \Nette\Latte\MacroNode
	 * @param mixed
	 * @return string
	 */
	public function macroTranslate(\Nette\Latte\MacroNode $node, $writer)
	{
		$x = $writer->formatArgs();
		$x = "\$this->addTranslation(" . $x . ");";
		eval($x); // please don't slap me
	}



	/**
	 * @param string|array
	 * @param int
	 */
	public function addTranslation($message, $count = NULL)
	{
		$this->translations[] = $message;
	}



	/**
	 * @return array
	 */
	public function getTranslations()
	{
		return $this->translations;
	}
	
}
