<?php

namespace JedenWeb\Forms\Controls;

use Nette\Utils\Html;

/**
 * Description of Anchor
 *
 * @author admin
 */
class DateTime extends BaseDateTime {
	
		public function __construct($caption = NULL) {
				parent::__construct($caption);
				
				
				$this->control->type = 'datetime';
//				$this->control->class('btn btn-primary');
		
		}		
}