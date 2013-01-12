<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Localization\Filters;

use JedenWeb\Localization\Dictionary;

/**
 * Latte translation extractor filter
 *
 * @author	Patrik Votoček
 */
class Latte extends \Nette\Object implements \JedenWeb\Localization\IFilter
{

	/**
	 *  @var array
	 */
	public $exts = array("*.latte");

	/**
	 * @var
	 */
	protected $parser;



	/**
	 * @param \Nette\DI\Container $context
	 */
	public function __construct(\Nette\DI\Container $context)
	{
		$this->parser = $context->latteEngine->parser;
	}



	/**
	 * @param \Nella\Localization\Dictionary
	 */
	public function process(Dictionary $dictionary)
	{
		$dictionary->freeze();

		$parser = $this->parser;

		$macros = LatteMacros::install($parser);

		$files = \Nette\Utils\Finder::findFiles($this->exts)->from($dictionary->dir);
		foreach ($files as $file) {
			$parser->parse(file_get_contents($file->getRealpath()));
			foreach ($macros->translations as $message) {
				$translation = (array)$message;
				$message = is_array($message) ? reset($message) : $message;

				if ($dictionary->hasTranslation($message)) {
					continue;
				}

				$dictionary->addTranslation($message, $translation, Dictionary::STATUS_UNTRANSLATED);
			}
		}
	}
	
}
