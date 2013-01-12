<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Localization;

/**
 * Translation extractor
 *
 * @author	Patrik Votoček
 */
class Extractor extends \JedenWeb\FreezableObject
{

	/**
	 * @var Translator
	 */
	protected $translator;

	/**
	 * @var array
	 */
	protected $filters = array();

	/**
	 * @var \Nette\DI\Container
	 */
	protected $context;



	/**
	 * @param Translator
	 */
	public function __construct(Translator $translator, \Nette\DI\Container $context)
	{
		$this->context = $context;
		$this->translator = $translator;
		$this->addFilter(new Filters\Latte($this->context));
	}



	/**
	 * @param IFilter
	 * @return Extractor
	 * @throws \Nette\InvalidStateException
	 */
	public function addFilter(IFilter $filter)
	{
		$this->updating();
		$this->filters[] = $filter;
		return $this;
	}



	/**
	 * @internal
	 */
	public function run()
	{
		$this->updating();
		$this->freeze();

		foreach ($this->translator->dictionaries as $dictionary) {
			if (!$dictionary->frozen) {
				$dictionary->init($this->translator->lang);
			}
			foreach ($this->filters as $filter) {
				$filter->process($dictionary);
			}
		}
	}

}
