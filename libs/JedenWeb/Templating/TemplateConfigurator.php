<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Templating;

use JedenWeb;
use Nette\DI\Container;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class TemplateConfigurator extends \Nette\Object implements ITemplateConfigurator
{

	/** @var \SystemContainer|Container */
	protected $container;

	/** @var array */
	protected $macroFactories = array();

	/** @var \Nette\Latte\Engine */
	protected $latte;



	/**
	 * @param \Nette\DI\Container $container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}



	/**
	 * @param string $factory
	 */
	public function addFactory($factory)
	{
		$this->macroFactories[] = $factory;
	}



	/**
	 * @param \Nette\Templating\Template $template
	 */
	public function configure(\Nette\Templating\Template $template)
	{
		if (($translator = $this->container->getByType('Nette\Localization\ITranslator', FALSE)) !== NULL) {
			$template->setTranslator($translator);
		}

		$template->registerHelperLoader(callback($this->container->getService('jedenWeb.helpers'), "loader"));
	}



	/**
	 * @param \Nette\Latte\Engine $latte
	 */
	public function prepareFilters(\Nette\Latte\Engine $latte)
	{
		$this->latte = $latte;
		foreach ($this->macroFactories as $factory) {
			if (!$this->container->hasService($factory)) {
				continue;
			}

			$this->container->$factory->invoke($this->latte->getCompiler());
		}
	}



	/**
	 * Returns Latter parser for the last prepareFilters call.
	 *
	 * @return \Nette\Latte\Engine
	 */
	public function getLatte()
	{
		return $this->latte;
	}

}
