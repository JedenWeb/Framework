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

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Form extends Nette\Application\UI\Form
{


	/**
	 */
	public function __construct()
	{
		parent::__construct();

		Rules::$defaultMessages[$this::EQUAL] = 'Please enter %s.';
		Rules::$defaultMessages[$this::FILLED] = 'Field "%label" is required.';
		Rules::$defaultMessages[$this::MIN_LENGTH] = 'Field "%label" must be longer than %d chars.';
		Rules::$defaultMessages[$this::MAX_LENGTH] = 'Field "%label" must be shorter than %d chars.';
		Rules::$defaultMessages[$this::LENGTH] = 'Value of field "%label" must be longer than %d and shorter than %d chars.';
		Rules::$defaultMessages[$this::EMAIL] = 'Field "%label" must be valid email address.';
		Rules::$defaultMessages[$this::URL] = 'Field "%label" must be valid URL address.';
		Rules::$defaultMessages[$this::IMAGE] = 'You can upload only JPEG, GIF or PNG files.';
		Rules::$defaultMessages[$this::MAX_FILE_SIZE] = 'File size must be less than %d KB';
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
	
	
	
	/*********************** controls ***********************/
	
	
	
	/**
	 * @author Jiří Šifalda
	 * @param string $name
	 * @param string $class
	 */
	protected function addExtension($name, $class)
	{
		\Nette\Forms\Container::extensionMethod($name, function (\Nette\Forms\Container $container, $name, $label = null) use ($class){
			return $container[$name] = new $class($label);
		});
	}
	
	
	/**
	 * @param string $name
	 * @return Containers\Container
	 */
	public function addContainer($name)
	{
		$control = new \Nette\Forms\Container;
		$control->currentGroup = $this->currentGroup;
		
		return $this[$name] = $control;
	}


	/**
	 * Adds an email input control to the form.
	 *
	 * @param string	control name
	 * @param string	label
	 * @param int	width of the control
	 * @param int	maximum number of characters the user may enter
	 * @return \Nette\Forms\Controls\TextInput
	 */
	public function addEmail($name, $label = NULL, $maxLength = NULL)
	{
		$control = $this->addText($name, $label, NULL, $maxLength);
		$control->setAttribute('type', 'email')->addCondition(self::FILLED)->addRule(self::EMAIL);
		
		return $control;
	}


	/**
	 * Adds an url input control to the form.
	 *
	 * @param string	control name
	 * @param string	label
	 * @param int	width of the control
	 * @param int	maximum number of characters the user may enter
	 * @return \Nette\Forms\Controls\TextInput
	 */
	public function addUrl($name, $label = NULL, $maxLength = NULL)
	{
		$control = $this->addText($name, $label, NULL, $maxLength);
		$control->setAttribute('type', "url")->addCondition(self::FILLED)->addRule(self::URL);
		
		return $control;
	}


	/**
	 * Adds a number input control to the form.
	 *
	 * @param string	control name
	 * @param string	label
	 * @param int	incremental number
	 * @param int	minimal value
	 * @param int	maximal value
	 * @return \Nette\Forms\Controls\TextInput
	 */
	public function addNumber($name, $label = NULL, $step = 1, $min = NULL, $max = NULL)
	{
		$control = $this->addText($name, $label);
		$control->setAttribute('step', $step)->setAttribute('type', "number")
			->addCondition(self::FILLED)->addRule(self::NUMERIC);
		$range = array(NULL, NULL);
		if ($min !== NULL) {
			$control->setAttribute('min', $min);
			$range[0] = $min;
		}
		if ($max !== NULL) {
			$control->setAttribute('max', $max);
			$range[1] = $max;
		}
		if ($range != array(NULL, NULL)) {
			$control->addCondition(self::FILLED)->addRule(self::RANGE, NULL, $range);
		}

		return $control;
	}

	
	/**
	 * Adds a range input control to the form.
	 *
	 * @param string	control name
	 * @param string	label
	 * @param int	incremental number
	 * @param int	minimal value
	 * @param int	maximal value
	 * @return \Nette\Forms\Controls\TextInput
	 */
	public function addRange($name, $label = NULL, $step = 1, $min = NULL, $max = NULL)
	{
		$control = $this->addNumber($name, $label, $step, $min, $max);
		$control->setAttribute('type', "range");
		
		return $control;
	}


	/**
	 * Adds search input control to the form.
	 *
	 * @param string	control name
	 * @param string	label
	 * @param int	width of the control
	 * @param int	maximum number of characters the user may enter
	 * @return \Nette\Forms\Controls\TextInput
	 */
	public function addSearch($name, $label = NULL, $maxLength = NULL)
	{
		$control = $this->addText($name, $label, NULL, $maxLength);
		$control->setAttribute('type', "search");
		
		return $control;
	}	

}

/*
// extension methods
Kdyby\Forms\Controls\CheckboxList::register();
Kdyby\Forms\Controls\DateTimeInput::register();
Kdyby\Forms\Containers\Replicator::register();

// radio list helper
RadioList::extensionMethod('getItemsOuterLabel', function (RadioList $_this) {
	$items = array();
	foreach ($_this->items as $key => $value) {
		$html = $_this->getControl($key);
		$html[1]->addClass('radio');

		$items[$key] = $html[1] // label
			->add($html[0]); // control
	}

	return $items;
});

// radio list helper
RadioList::extensionMethod('getFirstItemLabel', function (RadioList $_this) {
	$items = $_this->items;
	$first = key($items);

	$html = $_this->getControl($first);
	$html[1]->addClass('control-label');
	$html[1]->setText($_this->caption);

	return $html[1];
});
*/
