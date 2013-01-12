<?php

namespace JedenWeb\Forms\Controls;

use Nette\Utils\Html;

/**
 * Description of Anchor
 *
 * @author admin
 */
abstract class BaseDateTime extends \Nette\Forms\Controls\BaseControl {
	
	
		/** @var string */
		public static $format = 'j.n.Y';
		
		
		
		
		/**
		 * @return \Nette\DateTime|NULL
		 */
		public function getValue() {
				$value = parent::getValue();
				
				$value = \Nette\DateTime::createFromFormat(self::$format, $value);
				$err = \Nette\DateTime::getLastErrors();
				
				if ($err['error_count']) {
						$value = FALSE;
				}
				
				return $value ?: NULL;
		}
		
		
		
		/**
		* @param \DateTime
		* @return BaseDateTime
		*/
		public function setValue($value = NULL) {
			
				try {
						if ($value instanceof \DateTime) {
							
								return parent::setValue($value->format(static::$format));
								
						} else {
							
								return parent::setValue($value);
								
						}
						
				} catch (\Exception $e) {
					
						return parent::setValue(NULL);
						
				}
				
		}

		
		
		/**
		* @param BaseDateTime
		* @return bool
		*/
		public static function validateValid(\Nette\Forms\IControl $control) {
			
				$value = $this->getValue();
				return (is_null($value) || $value instanceof \DateTime);
				
		}

		
		
		/**
		* @return bool
		*/
		public function isFilled() {
			
				return (bool) $this->getValue();
				
		}
				
}