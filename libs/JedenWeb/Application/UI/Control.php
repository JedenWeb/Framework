<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Application\UI;

use Nette;
use JedenWeb\Templating\ITemplateConfigurator;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
abstract class Control extends Nette\Application\UI\Control
{

	/**
	 * @var ITemplateConfigurator
	 */
	protected $templateConfigurator;

	/**
	 * @var bool
	 */
	private $startupCheck;



	public function __construct()
	{
		parent::__construct();
	}



	protected function startup()
	{
		$this->startupCheck = TRUE;
	}


	
	/**
	 * @param type $obj
	 * @throws \Nette\InvalidStateException
	 */
	protected function attached($obj)
	{
		parent::attached($obj);

		if ($obj instanceof Nette\Application\IPresenter) {
			$this->startup();
			if (!$this->startupCheck) {
				$class = $this->getReflection()->getMethod('startup')->getDeclaringClass()->getName();
				throw new \Nette\InvalidStateException("Method $class::startup() or its descendant doesn't call parent::startup().");
			}
		}
	}



	public function render()
	{
		$this->template->render();
	}



	/**
	 * @param string|null $class
	 *
	 * @return \Nette\Templating\FileTemplate
	 */
	protected function createTemplate($class = NULL)
	{
		$template = parent::createTemplate($class);

		if ($file = $this->getTemplateDefaultFile()) {
			$template->setFile($file);
		}

		if ($this->templateConfigurator !== NULL) {
			$this->templateConfigurator->configure($template);
		}

		return $template;
	}



	/**
	 * @param \Nette\Templating\Template $template
	 *
	 * @return void
	 */
	public function templatePrepareFilters($template)
	{
		$engine = $this->presenter->context->createNette__Latte();

		if ($this->templateConfigurator !== NULL) {
			$this->templateConfigurator->prepareFilters($engine);
		}

		$template->registerFilter($engine);
	}



	/**
	 * Derives template path from class name.
	 *
	 * @return string|NULL
	 */
	protected function getTemplateDefaultFile()
	{
		$class = $this->getReflection();
		do {
			$file = dirname($class->getFileName()) . '/' . $class->getShortName() . '.latte';
			if (file_exists($file)) {
				return $file;

			} elseif (!$class = $class->getParentClass()) {
				break;
			}

		} while (TRUE);
	}



	/**
	 * @param ITemplateConfigurator $configurator
	 */
	public function setTemplateConfigurator(ITemplateConfigurator $configurator = NULL)
	{
		$this->templateConfigurator = $configurator;
	}



	/**
	 * @param \JedenWeb\Templating\ITemplateConfigurator $templateConfigurator
	 * @throws \Nette\InvlidStateException
	 */
	public function injectTemplateConfigurator(ITemplateConfigurator $templateConfigurator)
	{
		if ($this->templateConfigurator) {
			throw new \Nette\InvlidStateException;
		}

		$this->templateConfigurator = $templateConfigurator;
	}

}
