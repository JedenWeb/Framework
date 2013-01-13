<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Tools;

use Nette;
use Nette\Utils\Finder;
use JedenWeb\Tools\Filesystem;
use JedenWeb\Utils\Assets;

/**
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 */
class AssetsManager extends Nette\Object
{

	/**
	 * @var string
	 */
	protected $wwwDir;



	/**
	 * @param string $wwwDir
	 */
	public function __construct($wwwDir)
	{
		$this->wwwDir = (string) $wwwDir;
	}



	/**
	 * @param string $filesPattern
	 * @param string $directory
	 * @param string $publicDir
	 * @return array
	 */
	public function minify($filesPattern, $directory, $publicDir = '/css')
	{
		$failed = array();
		foreach (Finder::findFiles($filesPattern)->in($directory) as $filePath => $file) {
			$fileName = Assets::getFileNameFromPath($filePath);
			$content = FileSystem::read($filePath);
			$minifyContent = Assets::minifyCss($content);
			$path = $this->wwwDir . $publicDir . DIRECTORY_SEPARATOR . $fileName;
			if(!FileSystem::write($path, $minifyContent))
				$failed[] = array($filePath, $path);
		}
		return $failed;
	}



	/**
	 * @param string $directory
	 * @param string $publicDir
	 * @return string
	 */
	public function minifyJS($directory, $publicDir)
	{
		$failed = array();
		foreach (Finder::findFiles('*.js')->in($directory) as $filePath => $file) {
			$fileName = Assets::getFileNameFromPath($filePath);
			$content = FileSystem::read($filePath);
			$path = $this->wwwDir . $publicDir . DIRECTORY_SEPARATOR . $fileName;
			try {
				$minifyContent = \JsMin\Minify::minify($content);
				FileSystem::write($path, $minifyContent);
			}catch (\Exception $ex){
				$failed[] = array($filePath, $path);
			}
		}

		return $failed;

	}



	/**
	 * @param string $runScriptPath
	 * @param string $directory
	 * @param string $filesPattern
	 * @return array
	 */
	public function copy($runScriptPath, $directory, $filesPattern = '*')
	{
		$failed = array();
		foreach (Finder::findFiles($filesPattern)->from($runScriptPath . $directory) as $filePath => $file) {
			$path = $this->wwwDir . str_replace($runScriptPath, '', $filePath);
			if(!FileSystem::copy($filePath, $path))
				$failed[] = array($filePath, $path);
		}
		return $failed;
	}

}
