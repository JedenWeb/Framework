<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 */

namespace JedenWeb\Forms\Controls;

use JedenWeb;
use Nette;

/**
 * @todo Refractor!
 * Renderuje kazdy radio input jako radek tabulky, volitelne s rozsirenymi popisky
 */
class ExtendedRadioList extends Nette\Forms\Controls\BaseControl
{

	/** @var Html  separator element template */
	protected $separator;

	/** @var Html  container element template */
	protected $container;

	/** @var array */
	protected $items = array();

	/** @var rozsirene popisky inputu */
	private $descriptions = array();
   
   
	
   /**
    * Oproti RadioList je konstruktor rozsireny o treti parametr - pole objektu s popisky
    * TODO: zavest pro popisky rozhrani?
    * @param mixed $label [optional]
    * @param array $items [optional]
    * @param array $descriptions [optional] pole poli s popisky
    * @return 
    */
	public function __construct($label = NULL, array $items = NULL, array $descriptions = NULL)
	{
		parent::__construct($label);
		
		$this->control->type = 'radio';
		$this->container = Html::el('');
		$this->separator = Html::el('br');
		
		if ($items !== NULL) $this->setItems($items);
		
		if ($descriptions !== NULL) $this->setDescriptions($descriptions);
	}
   
	
	/**
     * Setter pro $descriptions
     * @param array $desc
     * @return void
     */
	public function setDescriptions(array $desc)
	{
		$this->descriptions = $desc;
	}

   
	/**
	 * Returns selected radio value.
	 * @param  bool
	 * @return mixed
	 */
	public function getValue($raw = FALSE)
	{
		return is_scalar($this->value) && ($raw || isset($this->items[$this->value])) ? $this->value : NULL;
	}


	/**
	 * Sets options from which to choose.
	 * @param  array
	 * @return RadioList  provides a fluent interface
	 */
	public function setItems(array $items)
	{
		$this->items = $items;
		return $this;
	}


	/**
	 * Returns options from which to choose.
	 * @return array
	 */
	final public function getItems()
	{
		return $this->items;
	}


	/**
	 * Returns separator HTML element template.
	 * @return Html
	 */
	final public function getSeparatorPrototype()
	{
		return $this->separator;
	}


	/**
	 * Returns container HTML element template.
	 * @return Html
	 */
	final public function getContainerPrototype()
	{
		return $this->container;
	}
	

	/**
     * Vraci strukturu controlu k vykresleni
     * @param object $key [optional] vraci pouze control s danym keyem
     * @return Html
     */
	public function getControl($key = NULL)
	{
		$container = clone $this->container;

		if (!isset($this->items[$key])) {
			  return NULL;
		  }

		  $control = parent::getControl($key);
		  $id = $control->id;
		  $counter = -1;
		  $value = $this->value === NULL ? NULL : (string) $this->getValue();

		  foreach ($this->items as $k => $val) {
			  $counter++;
			  if ($key !== NULL && $key != $k) continue; // intentionally ==

		   $control->id = $id . '-' . $counter;
			  $control->checked = (string) $k === $value;
			  $control->value = $k;


		   /*  /----------------------------------------------\
			*  |     |         desc[0]            |           |
			*  |  o  |----------------------------|  desc[1]  |
			*  |     |         desc[2]            |           |
			*  \----------------------------------------------/
			*/
			  if ($this->descriptions !== NULL && isset($this->descriptions[$k])) {            
			  $desc = $this->descriptions[$k];
			  $row = Html::el('tr');

			  $td1 = Html::el('td')->setHtml( (string)$control );
			  if (count($desc)>2) $td1->rowspan(2);
			  $row->add($td1);                      

			  $td2 = Html::el('td')->setHtml($desc[0]);
			  $row->add($td2);

			  if (isset($desc[1])) {
				 $td3 = Html::el('td')->setHtml($desc[1]);
				 if (count($desc)>2) $td3->rowspan(2);
				 $row->add($td3);
			  }

			  $container->add($row);

			  if (isset($desc[2])) {
				 $row = Html::el('tr');
				 $td4 = Html::el('td')->setHtml($desc[2]);
				 $row->add($td4);
				 $container->add($row);
			  }

			  } else {			   
			  $row = Html::el('tr');

			  $td1 = Html::el('td')->setHtml( (string)$control );
			  $row->add($td1);			   

			  $td2 = Html::el('td');
			  if ($val instanceof Html) {
				  $td2->setHtml($val);
			  } else {
				  $td2->setText($this->translate($val));
			  }
			  $row->add($td2);

				 $container->add($row);   
			  }


		  }

		  return $container;   
	}

   
	/**
	 * Generates label's HTML element.
	 * @return void
	 */
	public function getLabel()
	{
		$label = parent::getLabel();
		$label->for = NULL;
		return $label;
	}


	/**
	 * Filled validator: has been any radio button selected?
	 * @param  IFormControl
	 * @return bool
	 */
	public static function validateFilled(IFormControl $control)
	{
		return $control->getValue() !== NULL;
	}

}
