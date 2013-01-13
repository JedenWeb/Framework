<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Tools;

use JedenWeb;
use Nette;

/**
 * @author Filip Procházka <filip.prochazka@kdyby.org>
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
final class Filesystem extends Nette\Object
{

	/**
	 * Static class - cannot be instantiated.
	 *
	 * @throws \JedenWeb\StaticClassException
	 */
	final public function __construct()
	{
		throw new JedenWeb\StaticClassException;
	}



	/**
	 * @param string $file
	 * @param bool $need
	 *
	 * @return bool
	 * @throws \JedenWeb\FileNotWritableException
	 */
	public static function rm($file, $need = TRUE)
	{
		if (is_dir((string)$file)) {
			return static::rmDir($file, FALSE, $need);
		}

		if (FALSE === ($result = @unlink((string)$file)) && $need) {
			throw new JedenWeb\FileNotWritableException("Unable to delete file '$file'");
		}

		return $result;
	}



	/**
	 * @param string $dir
	 * @param bool $recursive
	 * @param bool $need
	 *
	 * @return boolean
	 * @throws \JedenWeb\DirectoryNotWritableException
	 */
	public static function rmDir($dir, $recursive = TRUE, $need = TRUE)
	{
		$recursive && self::cleanDir($dir = (string)$dir, $need);
		if (is_dir($dir) && FALSE === ($result = @rmdir($dir)) && $need) {
			throw new JedenWeb\DirectoryNotWritableException("Unable to delete directory '$dir'.");
		}

		return isset($result) ? $result : TRUE;
	}



	/**
	 * @param string $dir
	 * @param bool $need
	 *
	 * @return bool
	 */
	public static function cleanDir($dir, $need = TRUE)
	{
		if (!file_exists($dir)) {
			return TRUE;
		}

		foreach (Nette\Utils\Finder::find('*')->from($dir)->childFirst() as $file) {
			if (FALSE === static::rm($file, $need)) {
				return FALSE;
			}
		}

		return TRUE;
	}



	/**
	 * @param string $dir
	 * @param bool $recursive
	 * @param int $chmod
	 * @param bool $need
	 *
	 * @throws \JedenWeb\IOException
	 */
	public static function mkDir($dir, $recursive = TRUE, $chmod = 0777, $need = TRUE)
	{
		$parentDir = $dir;
		while (!is_dir($parentDir)) {
			$parentDir = dirname($parentDir);
		}

		@umask(0000);
		if (!is_dir($dir) && FALSE === ($result = @mkdir($dir, $chmod, $recursive)) && $need) {
			throw new JedenWeb\IOException('Unable to create directory ' . $dir);
		}

		if ($dir !== $parentDir) {
			do {
				@umask(0000);
				@chmod($dir, $chmod);
				$dir = dirname($dir);
			} while ($dir !== $parentDir);
		}

		return isset($result) ? $result : TRUE;
	}



	/**
	 * @param string $file
	 * @param string $contents
	 * @param bool $createDirectory
	 * @param int $chmod
	 * @param bool $need
	 *
	 * @return int
	 * @throws \JedenWeb\FileNotWritableException
	 */
	public static function write($file, $contents, $createDirectory = TRUE, $chmod = 0777, $need = TRUE)
	{
		$createDirectory && static::mkDir(dirname($file), TRUE, $chmod);

		if (FALSE === ($result = @file_put_contents($file, $contents)) && $need) {
			throw JedenWeb\FileNotWritableException::fromFile($file);
		}
		@chmod($file, $chmod);

		return $result;
	}



	/**
	 * @param string $file
	 * @param bool $need
	 *
	 * @return string
	 * @throws \JedenWeb\FileNotFoundException
	 */
	public static function read($file, $need = TRUE)
	{
		if (FALSE === ($contents = @file_get_contents($file)) && $need) {
			throw new JedenWeb\FileNotFoundException('File "' . $file . '" is not readable.');
		}

		return $contents;
	}

}
