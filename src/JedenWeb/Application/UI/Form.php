<?php

namespace JedenWeb\Application\UI;

use JedenWeb;
use Kdyby;
use Nette;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\RadioList;
use Nette\Forms\ISubmitterControl;
use Nette\Forms\Rules;
use Nette\Forms\Controls;

require_once __DIR__ . '/../../Forms/extensions.php';

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 * @method \Nette\Forms\Controls\TextInput addEmail($name, $label = NULL)
 * @method \Nette\Forms\Controls\TextInput addUrl($name, $label = NULL)
 * @method \Nette\Forms\Controls\TextInput addNumber($name, $label = NULL, $step = 1, $min = NULL, $max = NULL)
 * @method \Nette\Forms\Controls\TextInput addRange($name, $label = NULL, $step = 1, $min = NULL, $max = NULL)
 * @method \Nette\Forms\Controls\TextInput addSearch($name, $label = NULL)
 */
class Form extends Nette\Application\UI\Form
{

	/**
	 */
	public function __construct()
	{
		parent::__construct();
	}


	
	/**
	 * @param \Nette\ComponentModel\Container $obj
	 */
	protected function attached($obj)
	{
		if ($obj instanceof Nette\Application\IPresenter) {
			if ($this->getComponents()->count() === 0) { # form was created by operator new
				$this->beforeSetup();
				$this->setup();
				$this->afterSetup();
			}
			$this->attachHandlers();
		}

		parent::attached($obj);
	}
	
	
	
	/**
	 * Method called before controls are attached
	 */
	protected function beforeSetup() {}


	
	/**
	 * Method where controls should be attached
	 */
	protected function setup() {}


	
	/**
	 * Method called after controls are attached
	 */
	protected function afterSetup() {}	
	


	/**
	 * Returns a fully-qualified name that uniquely identifies the component
	 * within the presenter hierarchy.
	 * @author Filip Procházka (filip.prochazka@kdyby.org)
	 * @return string
	 */
	public function getUniqueId()
	{
		return $this->lookupPath('Nette\Application\UI\Presenter', TRUE);
	}



	/**
	 * Automatically attach methods
	 */
	protected function attachHandlers()
	{
		if (method_exists($this, 'handleSuccess')) {
			$this->onSuccess[] = callback($this, 'handleSuccess');
		}

		if (method_exists($this, 'handleError')) {
			$this->onError[] = callback($this, 'handleError');
		}

		if (method_exists($this, 'handleValidate')) {
			$this->onValidate[] = callback($this, 'handleValidate');
		}

		foreach ($this->getComponents(TRUE, 'Nette\Forms\ISubmitterControl') as $submitControl) {
			$name = ucfirst((Nette\Utils\Strings::replace(
				$submitControl->lookupPath('Nette\Forms\Form'), '~\-(.)~i', function ($m) {
					return strtoupper($m[1]);
				}
			)));

			if (method_exists($this, 'handle' . $name . 'Click')) {
				$submitControl->onClick[] = callback($this, 'handle' . $name . 'Click');
			}

			if (method_exists($this, 'handle' . $name . 'InvalidClick')) {
				$submitControl->onInvalidClick[] = callback($this, 'handle' . $name . 'InvalidClick');
			}
		}
	}



	/**
	 * Fires send/click events.
	 * @return void
	 */
	public function fireEvents()
	{
		if (!$this->isSubmitted()) {
			return;

		} elseif ($this->isSubmitted() instanceof ISubmitterControl) {
			if ($this->isValid()) {
				$this->dispatchEvent($this->isSubmitted()->onClick, $this->isSubmitted());
				$valid = TRUE;

			} else {
				$this->dispatchEvent($this->isSubmitted()->onInvalidClick, $this->isSubmitted());
			}
		}

		if (isset($valid) || $this->isValid()) {
			$this->dispatchEvent($this->onSuccess, $this);

		} else {
			$this->dispatchEvent($this->onError, $this);
		}
	}

	

	/**
	 * @author Filip Procházka
	 * @param array|\Kdyby\Events\Event|\Traversable $listeners
	 * @param mixed $arg
	 */
	protected function dispatchEvent($listeners, $arg = NULL)
	{
		$args = func_get_args();
		$listeners = array_shift($args);

		if ($listeners instanceof Kdyby\Events\Event) {
			
			return $listeners->dispatch($args);

		}
		
		foreach ((array) $listeners as $handler) {
			
			if ($handler instanceof Nette\Application\UI\Link) {

				/** @var \Nette\Application\UI\Link $handler */
				$refl = $handler->getReflection();
				/** @var \Nette\Reflection\ClassType $refl */
				$compRefl = $refl->getProperty('component');
				$compRefl->accessible = TRUE;
				/** @var \Nette\Application\UI\PresenterComponent $component */
				$component = $compRefl->getValue($handler);
				$component->redirect($handler->getDestination(), $handler->getParameters());

			} else {
				callback($handler)->invokeArgs($args);
			}

		}
	}


	
	/**
	 * @author Jiří Šifalda
	 * @param array $defaults
	 */
	public function restore(array $defaults = array())
	{
		$this->setDefaults($defaults, true);
		$this->setValues($defaults, true);
	}
	
	
	
	/**
	 * @author Jiří Šifalda
	 * @param array|\Nette\Forms\Traversable $values
	 * @param bool $erase
	 * @return \Nette\Forms\Container
	 */
	public function setDefaults($values, $erase = false)
	{
		if (is_array($values)) {
			$values = array_map(function ($value) {

				if (is_object($value) && (method_exists($value, '__toString'))) {

					if (isset($value->id)) {
						return (string) $value->id;
					} else {
						return (string) $value;
					}

				}
				
				return $value;
			}, $values);
		}

		return parent::setDefaults($values, $erase);
	}

}
