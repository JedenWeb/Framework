<?php

use Nette\Forms\Container;

/**
 * Adds an email input control to the form.
 *
 * @param string	control name
 * @param string	label
 * @param int	width of the control
 * @param int	maximum number of characters the user may enter
 * @return \Nette\Forms\Controls\TextInput
 */
Container::extensionMethod('addEmail', function(Container $container, $name, $label = NULL, $cols = NULL, $maxLength = NULL) {
	$item = $container->addText($name, $label, $cols, $maxLength);
	$item->setAttribute('type', 'email')->addCondition(self::FILLED)->addRule(self::EMAIL);
	return $item;
});


/**
 * Adds an url input control to the form.
 *
 * @param string	control name
 * @param string	label
 * @param int	width of the control
 * @param int	maximum number of characters the user may enter
 * @return \Nette\Forms\Controls\TextInput
 */
Container::extensionMethod('addUrl', function(Container $container, $name, $label = NULL, $cols = NULL, $maxLength = NULL) {
	$item = $container->addText($name, $label, $cols, $maxLength);
	$item->setAttribute('type', "url")->addCondition(self::FILLED)->addRule(self::URL);
	return $item;	
});


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
Container::extensionMethod('addNumber', function(Container $container, $name, $label = NULL, $step = 1, $min = NULL, $max = NULL) {
	$item = $this->addText($name, $label);
	$item->setAttribute('step', $step)->setAttribute('type', "number")
		->addCondition(self::FILLED)->addRule(self::NUMERIC);
	$range = array(NULL, NULL);
	if ($min !== NULL) {
		$item->setAttribute('min', $min);
		$range[0] = $min;
	}
	if ($max !== NULL) {
		$item->setAttribute('max', $max);
		$range[1] = $max;
	}
	if ($range != array(NULL, NULL)) {
		$item->addCondition(self::FILLED)->addRule(self::RANGE, NULL, $range);
	}

	return $item;
});


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
Container::extensionMethod('addRange', function(Container $container, $name, $label = NULL, $step = 1, $min = NULL, $max = NULL) {
	$item = $this->addNumber($name, $label, $step, $min, $max);
	return $item->setAttribute('type', "range");
});


/**
 * Adds search input control to the form.
 *
 * @param string	control name
 * @param string	label
 * @param int	width of the control
 * @param int	maximum number of characters the user may enter
 * @return \Nette\Forms\Controls\TextInput
 */
Container::extensionMethod('addSearch', function(Container $container, $name, $label = NULL, $cols = NULL, $maxLength = NULL) {
	$item = $this->addText($name, $label, $cols, $maxLength);
	return $item->setAttribute('type', "search");
});
