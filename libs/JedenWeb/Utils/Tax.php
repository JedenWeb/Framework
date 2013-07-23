<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Utils;

use JedenWeb;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
final class Tax
{

	const BASE_RATE = 21;

	const REDUCED_RATE = 15;

	
	
	final public function __construct()
	{
		throw new JedenWeb\StaticClassException;
	}
	
	
	/**
	 * Returns price without added value
	 * 
	 * @param int $price
	 * @return int
	 */
	public static function priceWithoutTax($price, $rate = self::BASE_RATE)
	{
		return $price - self::tax($price, $rate);
	}


	/**
	 * Returns price with added value
	 * 
	 * @param int $price
	 * @return int
	 */
	public static function priceWithTax($price, $rate = self::BASE_RATE)
	{
		return $price * ((100 + $rate) / 100);
	}


	/**
	 * Returns added value
	 * 
	 * @param int $price
	 * @return int
	 */
	public static function tax($price, $rate = self::BASE_RATE)
	{
		return $price * self::getCoefficient($rate);
	}
	
	
	/**
	 * Returns tax rate
	 * 
	 * @param int $full
	 * @param int $base
	 * @return int
	 */
	public static function rate($full, $base)
	{
		return ($full / $base * 100) - 100;
	}


	/**
	 * @return int Coefficient counted from tax base
	 */
	private static function getCoefficient($rate)
	{
		return $rate / (100 + $rate);
	}

}
