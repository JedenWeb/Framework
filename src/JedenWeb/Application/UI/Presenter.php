<?php

namespace JedenWeb\Application\UI;

use JedenWeb;
use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Presenter extends Nette\Application\UI\Presenter
{

	/** @var array */
	private $_flashes = array();



	/**
	 * @author  Jiří Šifalda
	 * @param string $name
	 * @return \Nette\Application\UI\Multiplier|\Nette\ComponentModel\IComponent
	 */
	protected function createComponent($name)
	{
		$method = 'createComponent' . ucfirst($name);
		if (method_exists($this, $method)) {
			$this->checkRequirements($this->getReflection()->getMethod($method));
			
			if (\Nette\Reflection\Method::from($this, $method)->hasAnnotation('multiple')) {
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
			$this->getUser()->protectElement($element);
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
		$this->getTemplate()->flashes = array_merge((array) $messages, $this->_flashes);

		return $flash;
	}
	


	/*********************** rendering ***********************/

	

	/**
	 */
	protected function beforeRender()
	{
		parent::beforeRender();

		if ($this->isAjax() && $this->hasFlashSession()) {
			$this->invalidateControl('flash');
		}
	}



	/***********************  ***********************/



	/**
	 * @param string $module
	 * @return boolean
	 */
	public function isModuleCurrent($module)
	{
		if (!$pos = strrpos($this->name, ':')) { # not in module
			return FALSE;
		}

		return ltrim($module, ':') === substr($this->name, 0, $pos);
	}

}
