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
	
	/**
	 * @var array
	 */
	private $_flashes = array();



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
	
	
	/**
	 * @param string $name
	 * @return \Nette\ComponentModel\IComponent
	 */
	protected function createComponent($name)
	{
		$method = 'createComponent' . ucfirst($name);
		if (method_exists($this, $method)) {
			$this->checkRequirements($this->getReflection()->getMethod($method));
		}

		return parent::createComponent($name);
	}
	
	
	/**
	 * Checks for requirements such as authorization.
	 *
	 * @param \Reflector $element
	 *
	 * @return void
	 */
	public function checkRequirements($element)
	{
		if ($element instanceof \Reflector) {
			$this->getPresenter()->getUser()->protectElement($element);
		}
	}	

	
	/**
	 * @author Jan Skrasek
	 * {@inheritdoc}
	 */
	public function link($destination, $args = array())
	{
		if (!is_array($args)) {
			$args = func_get_args();
			array_shift($args);
		}

		$link = parent::link($destination, $args);
		return $this->getPresenter()->createSecuredLink($this, $link, $destination);
	}


	/**
	 * For @secured annotated signal handler methods checks if URL parameters has not been changed
	 *
	 * @author Jan Skrasek
	 * @throws Nette\Application\UI\BadSignalException if there is no handler method or the security token does not match
	 * @throws \LogicException if there is no redirect in a secured signal
	 */
	public function signalReceived($signal)
	{
		$method = $this->formatSignalMethod($signal);
		$secured = FALSE;

		if (method_exists($this, $method)) {
			$reflection = new Nette\Reflection\Method($this, $method);
			$secured = $reflection->hasAnnotation('secured');
			if ($secured) {
				$params = array($this->getUniqueId());
				if ($this->params) {
					foreach ($reflection->getParameters() as $param) {
						if ($param->isOptional()) {
							continue;
						}
						if (isset($this->params[$param->name])) {
							$params[$param->name] = $this->params[$param->name];
						}
					}
				}

				if (!isset($this->params['_sec']) || $this->params['_sec'] !== $this->getPresenter()->getCsrfToken(get_class($this), $method, $params)) {
					throw new Nette\Application\UI\BadSignalException("Invalid security token for signal '$signal' in class {$this->reflection->name}.");
				}
			}
		}

		parent::signalReceived($signal);

		if ($secured && !$this->getPresenter()->isAjax()) {
			throw new \LogicException("Secured signal '$signal' did not redirect. Possible csrf-token reveal by http referer header.");
		}
	}	
	
	
	/**
	 * Saves the message to template, that can be displayed after redirect.
	 * @param  string
	 * @param  string
	 * @return \stdClass
	 */
	public function flashMessage($message, $type = 'info', $withoutSession = FALSE)
	{
		if ($withoutSession) {
			$this->_flashes[] = $flash = (object)array(
				'message' => $message,
				'type' => $type,
			);
		} else {
			$flash = parent::flashMessage($message, $type);
		}

		$id = $this->getParameterId('flash');
		$messages = $this->getPresenter()->getFlashSession()->$id;
		$this->getTemplate()->flashes = array_merge((array)$messages, $this->_flashes);

		return $flash;
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


	/*********************** inject ***********************/


	/**
	 * @param \JedenWeb\Templating\ITemplateConfigurator $templateConfigurator
	 * @throws \Nette\InvlidStateException
	 */
	final public function injectTemplateConfigurator(ITemplateConfigurator $templateConfigurator)
	{
		if ($this->templateConfigurator) {
			throw new \Nette\InvlidStateException;
		}

		$this->templateConfigurator = $templateConfigurator;
	}

}
