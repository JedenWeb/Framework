<?php

namespace JedenWeb\Tools;

use JedenWeb;
use Nette;

/**
 * @author Filip Procházka <filip.prochazka@kdyby.org>
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 *
 * @param string $defaultFormat
 */
class DateTime extends Nette\DateTime
{

	/** @var string */
	protected static $defaultFormat = 'j.n.Y G:i';



	/**
	 * DateTime object factory.
	 * @param  string|int|\DateTime
	 * @return DateTime
	 */
	public static function from($time)
	{
		if ($time === NULL) {
			return NULL;

		} elseif ($time instanceof \DateTime) {
			/** @var \Datetime $time */
			return new static(
				$time->format('Y-m-d H:i:s'),
				$time->getTimezone()
			);

		} elseif ($time === 0) {
			return static::tryFormats('U', 0);

		} elseif ($date = static::tryFormats(array(static::$defaultFormat), $time)) {
			return $date;
		}

		return parent::from($time);
	}



	/**
	 * @param array|string $formats
	 * @param $date
	 *
	 * @return bool|\JedenWeb\Tools\DateTime
	 */
	public static function tryFormats($formats, $date)
	{
		foreach ((array)$formats as $format) {
			if ($valid = static::createFromFormat('!' . $format, $date)) {
				return static::from($valid);
			}
		}

		return FALSE;
	}



	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->format(static::$defaultFormat);
	}

}
