<?php

namespace JedenWeb;

use Nette;

/**
 * {@inheritDoc}
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Image extends Nette\Image
{
	
	/**
	 * Blur image using Gaussian blur.
	 * @return Image  provides a fluent interface
	 */
	public function blur()
	{		
		for ($i = 1; $i <= 40; $i++) {
//			imagefilter($this->getImageResource(), IMG_FILTER_GAUSSIAN_BLUR);
			$gaussian = array(
				array(1.0, 2.0, 1.0), 
				array(2.0, 4.0, 2.0), 
				array(1.0, 2.0, 1.0)
			);
			$div = array_sum(array_map('array_sum', $gaussian));
			imageconvolution($this->getImageResource(), $gaussian, $div, 0);
		}
		return $this;
	}

}
