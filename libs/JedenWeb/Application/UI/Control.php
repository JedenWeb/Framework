<?php

namespace JedenWeb\Application\UI;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
abstract class Control extends Nette\Application\UI\Control
{

	/** @var bool */
	private $startupCheck;
	
	/** @var array */
	private $_flashes = array();



	/**
	 */
	public function __construct()
	{
		parent::__construct();
	}


	/**
	 */
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
	 * 
	 * @param string $message
	 * @param string $type
	 * @param bool $withoutSession
	 * @return \stdClass
	 */
	public function flashMessage($message, $type = 'info', $withoutSession = FALSE)
	{
		if ($withoutSession) {
			$this->_flashes[] = $flash = (object) array(
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
	 * @param string|NULL $class
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
