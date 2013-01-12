<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Image extends Nette\Image
{

	/** {@link cropPrecise()} preserves top of image */
	const TOP = 11;
	/** {@link cropPrecise()} preserves left of image */
	const LEFT = 11;


	/** {@link cropPrecise()} preserves center of image */
	const CENTER = 12;


	/** {@link cropPrecise()} preserves bottom of image */
	const BOTTOM = 13;
	/** {@link cropPrecise()} preserves bottom of image */
	const RIGHT = 13;


	/**
	 *
	 * |||||||||||||||||||||||||
	 * |-----------------------|
	 * |-----------------------|
	 * |-----/////////////-----|
	 * |-----/////////////-----|
	 * |-----/////////////-----|
	 * |-----------------------|
	 * |-----------------------|
	 * |||||||||||||||||||||||||
	 *
	 * @param int $width
	 * @param int $height
	 */
	public function cropImage($width, $height)
	{
		$original_w = $this->getWidth();
		$original_h = $this->getHeight();

		if ($original_w > $original_h) {
			$this->resize(null, $height);
			$resized_w = $this->getWidth();

			if ($resized_w > $width) {
				$x = round(($resized_w - $width) / 2);
				$this->crop($x, 0, $width, $height);
			}
		} elseif ($original_h > $original_w) {
			$this->resize($width, null);
			$resized_h = $this->getHeight();

			if ($resized_h > $height) {
				$y = ($resized_h - $height) / 2;
				$this->crop(0, $y, $width, $height);
			}
		} else { // ===
			$this->resize($width, $height);
		}

		return $this;
	}



	/**
	 * @param int $width
	 * @param int $height
	 * @param const $how
	 */
	public function cropPrecise($width, $height, $how = self::CENTER)
	{
		$w0 = (int) $this->getWidth();
		$h0 = (int) $this->getHeight();

		if ($width >= $height) {
			$this->resize($width, NULL);

			$wk = $width / $w0; // ratio of width
			$hs = $h0 * $wk; // height in ratio
		} else {
			$this->resize(NULL, $height);

			$hk = $height / $h0; // ratio of height
			$ws = $w0 * $hk; // width in ratio
		}

		switch ($how) {
			case self::TOP:
					$top = 0;
				break;
			default:
			case self::CENTER:
					if ($width >= $height) {
						$top = (int) round(($this->getHeight() - $height) / 2);
						$left = 0;
					} else {
						$top = 0;
						$left = (int) round(($this->getWidth() - $width) / 2);
					}
				break;
			case self::BOTTOM:
					if ($width >= $height) {
						$top = (int) round(($this->getHeight() - $height));
						$left = 0;
					} else {
						$top = 0;
						$left = (int) round(($this->getWidth() - $width));
					}
				break;
		}

		$this->crop($left, $top, $width, $height);
	}

}
