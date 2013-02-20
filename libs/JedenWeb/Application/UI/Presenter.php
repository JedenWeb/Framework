<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Application\UI;

use JedenWeb;
use JedenWeb\Templating\ITemplateConfigurator;
use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Presenter extends Nette\Application\UI\Presenter
{

	/**
	 * @var \JedenWeb\Managers\Image\ImageManager
	 */
	private $imgPipe;

	/**
	 * @var ITemplateConfigurator
	 */
	protected $templateConfigurator;



	/*********************** components ***********************/



	/**
	 * @author  Jiří Šifalda
	 * @param string $name
	 * @return \Nette\Application\UI\Multiplier|\Nette\ComponentModel\IComponent
	 */
	protected function createComponent($name)
	{
		$method = 'createComponent' . ucfirst($name);
		if (method_exists($this, $method) && \Nette\Reflection\Method::from($this, $method)->hasAnnotation('multiple')) {
			$presenter = $this;
			return new \Nette\Application\UI\Multiplier(function ($id) use ($presenter, $method) {
				$defaultArgs = array($presenter, $id);
				return call_user_func_array(array($presenter, $method), $defaultArgs);
			});
			# in PHP 5.4 factory for multiplied component can be protected
			# return new UI\Multiplier(function ($id) use ($name) {
			#	return $this->$method($this, $id, $this->getDataset($name));
			# });
		}

		return parent::createComponent($name);
	}



	/*********************** templating ***********************/



	public function beforeRender()
	{
		parent::beforeRender();

		if ($this->isAjax()) {
			$this->invalidateControl('flash');
		}
	}



	/**
	 * @param ITemplateConfigurator $configurator
	 */
	public function setTemplateConfigurator(ITemplateConfigurator $configurator = NULL)
	{
		$this->templateConfigurator = $configurator;
	}



	/**
	 * @param string|null $class
	 *
	 * @return \Nette\Templating\Template
	 */
	public function createTemplate($class = NULL)
	{
		$template = parent::createTemplate($class);

		if ($this->templateConfigurator !== NULL) {
			$this->templateConfigurator->configure($template);
		}
		/** @var \Nette\Templating\FileTemplate|\stdClass $tmp */
		$template->_imagePipe = $this->imgPipe;

		return $template;
	}



	/**
	 * @param \Nette\Templating\Template $template
	 *
	 * @return void
	 */
	public function templatePrepareFilters($template)
	{
		$engine = $this->context->createNette__Latte();

		if ($this->templateConfigurator !== NULL) {
			$this->templateConfigurator->prepareFilters($engine);
		}

		$template->registerFilter($engine);
	}



	/***********************  ***********************/



	/**
	 * @param string $module
	 * @return boolean
	 */
	public function isModuleCurrent($module)
	{
		if (!$a = strrpos($this->name, ':')) { // not in module
			return false;
		}

		return ltrim($module, ':') === substr($this->name, 0, $a);
	}



	/**
	 * @return string
	 */
	protected function getBaseUrl()
	{
		return $this->getHttpRequest()->url->baseUrl;
	}



	/*********************** inject ***********************/



	/**
	 * @param \JedenWeb\Managers\Image\ImageManager $imgPipe
	 */
	public function injectImgPipe(\JedenWeb\Managers\Image\ImageManager $imgPipe)
	{
		$this->imgPipe = $imgPipe;
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
