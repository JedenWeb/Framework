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
use Nette\Http\Request;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class ImageManager extends Nette\Object
{

	/**
	 * @var string
	 */
	private $wwwDir;

	/**
	 * @var string
	 */
	private $baseUrl;

	/**
	 * @var string
	 */
	private $assetsDir;



	/**
	 * @param string $wwwDir
	 * @param \Nette\Http\Request $httpRequest
	 * @param string $assetsDir
	 *
	 * @throws \Nette\IOException
	 */
	public function __construct($wwwDir, Request $httpRequest, $assetsDir = 'media')
	{
		// public assets dir
		$assetsDir = $wwwDir . '/' . $assetsDir;
		if (!is_dir($assetsDir) || !is_writable($assetsDir)) {
			throw new Nette\IOException("Directory $assetsDir is not writable.");
		}
		$this->wwwDir = $wwwDir;
		$this->assetsDir = $assetsDir;

		// for temporary files
		if (!is_dir($tempDir = ($this->assetsDir . '/temp'))) {
			self::mkdir($tempDir);
		}

		// base of public url
		$this->baseUrl = rtrim($httpRequest->url->baseUrl, '/');
	}



	/**
	 * @param string $assetsDir
	 */
	public function setAssetsDir($assetsDir)
	{
		$search = str_replace($this->wwwDir.'/', '', $this->assetsDir);
		$assetsDir = str_replace($search, $assetsDir, $this->assetsDir);
		if (!is_dir($assetsDir) || !is_writable($assetsDir)) {
			throw new Nette\IOException("Directory '$assetsDir' is not writable.");
		}
		$this->assetsDir = $assetsDir;
	}



	/**
	 * @return string
	 */
	public function getAssetsDir()
	{
		return $this->assetsDir;
	}



	/**
	 * @param string|Image $sourceImage
	 * @param array $size
	 * @param int $flags
	 *
	 * @return string
	 * @throws \Nette\IOException
	 */
	public function request($sourceImage, array $size = array(), $flags = 0)
	{
		if (!$sourceImage instanceof Image) {
			$sourceImage = new Image($sourceImage);
		}

		$image = NULL;
		/** @var Nette\Image $image */
		if ($sourceImage->size->width > 500 || $sourceImage->size->height > 500) { // w & h
			$image = Nette\Image::fromFile($sourceImage->file)->resize(500, 500);
		} // elseif($size) todo: zpracování velikosti


		// figure out public directory
		$dir = $flags & self::TEMP ? $this->assetsDir . '/temp' : $this->assetsDir;
		$dir .= '/' . (implode('x', $size) ? : 'original');
		if (!is_dir($dir)) {
			self::mkdir($dir);
		}

		// filename
		$targetPath = $dir . '/' . basename($sourceImage->file);

		// continue processing only if newer version is available
		if (file_exists($targetPath) && filectime($targetPath) < filectime($sourceImage->file)) {
			return $this->publicPath($targetPath);
		}

		// copy source file
		if ($image) {
			if (!$image->save($targetPath)) {
				throw new Nette\IOException("Cannot resize $sourceImage->file to $targetPath");
			}

		} else {
			if (!@copy($sourceImage->file, $targetPath)) {
				throw new Nette\IOException("Cannot copy $sourceImage->file to $targetPath");
			}
		}

		return $this->publicPath($targetPath);
	}



	/**
	 * @param string $file
	 * @return string
	 */
	private function publicPath($file)
	{
		return $this->baseUrl . str_replace($this->wwwDir, '', $file);
	}



	/**
	 * @param string $dir
	 *
	 * @throws \Nette\IOException
	 * @return void
	 */
	private static function mkdir($dir)
	{
		$oldMask = umask(0);
		@mkdir($dir, 0777);
		@chmod($dir, 0777);
		umask($oldMask);

		if (!is_dir($dir) || !is_writable($dir)) {
			throw new Nette\IOException("Please create writable directory $dir.");
		}
	}

}
