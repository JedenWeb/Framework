<?php

namespace JedenWeb\Forms\Controls;

/**
 * Description of EmailInput
 *
 * @author admin
 */
class EmailInput extends \Nette\Forms\Controls\TextInput {
	
	
		public function __construct($label = NULL) {
				parent::__construct($label);
				
				$this->control->type = 'email';
				$this->control->placeholder('Email');
				
				$this->setEmptyValue('@');
		}
}