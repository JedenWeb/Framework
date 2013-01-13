<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Utils;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Tax
{

	/**
	 * Base percentage of tax
	 */
	const TAX_BASE = 20;

	/**
	 * @param int $price With tax
	 * @return int Price without tax
	 */
	public static function priceWithoutTax($price)
	{
		return $price - self::getTax($price);
	}



	/**
	 * @param int $price Without tax
	 * @return type Price with tax
	 */
	public static function priceWithTax($price)
	{
		return $price * ((100 + self::TAX_BASE) / 100);
	}



	/**
	 * @param int $price With tax
	 * @return int Tax
	 */
	public static function getTax($price)
	{
		return round($price * self::getCoefficient());
	}



	/**
	 * @return int Coefficient counted from tax base
	 */
	private static function getCoefficient()
	{
		return round(self::TAX_BASE / (100 + self::TAX_BASE), 4);
	}

}
