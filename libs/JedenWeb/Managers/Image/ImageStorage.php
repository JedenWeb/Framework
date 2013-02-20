<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Managers\Image;

use JedenWeb;
use Nette;
use Nette\Http\FileUpload;
use Nette\Utils\Finder;
use Nette\Utils\Strings;

/**
 * @author Filip Procházka <filip.prochazka@kdyby.org>
 */
class ImageStorage extends Nette\Object
{

	/**
	 * @var string
	 */
	private $imagesDir;



	/**
	 * @param string $dir temp dir
	 */
	public function __construct($dir)
	{
		$dir .= '/images';
		if (!is_dir($dir)) {
			umask(0);
			mkdir($dir, 0777);
		}

		$this->imagesDir = $dir;
	}



	/**
	 * @param FileUpload $file
	 * @return Image
	 * @throws \Nette\InvalidArgumentException
	 */
	public function upload(FileUpload $file)
	{
		if (!$file->isOk() || !$file->isImage()) {
			throw new Nette\InvalidArgumentException;
		}

		do {
			$name = Strings::random(10) . '.' . $file->getSanitizedName();
		} while (file_exists($path = $this->imagesDir . DIRECTORY_SEPARATOR . $name));

		$file->move($path);
		return new Image($path);
	}



	/**
	 * @param string $content
	 * @param string $filename
	 * @return Image
	 */
	public function save($content, $filename)
	{
		do {
			$name = Strings::random(10) . '.' . $filename;
		} while (file_exists($path = $this->imagesDir . DIRECTORY_SEPARATOR . $name));

		file_put_contents($path, $content);
		return new Image($path);
	}



	/**
	 * @return string
	 */
	public function getImagesDir()
	{
		return $this->imagesDir;
	}



	/**
	 * @param $param
	 * @throws FileNotFoundException
	 * @return string
	 */
	public function find($param)
	{
		foreach (Finder::findFiles($param)->from($this->imagesDir) as $file) {
			/** @var \SplFileInfo $file */
			return $file->getPathname();
		}

		throw new JedenWeb\FileNotFoundException("File $param not found.");
	}

}
