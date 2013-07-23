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

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
abstract class Control extends Nette\Application\UI\Control
{
	
	use \Nextras\Application\UI\SecuredLinksControlTrait;

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

		return $template;
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

}
