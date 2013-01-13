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
use Nette\Utils\Strings;

/**
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 */
class FileManager extends Nette\Object
{

	/**
	 * @var string
	 */
	protected $baseDirPath;

	/**
	 * @var string
	 */
	protected $filesDirPath;



	/**
	 * @param string $baseDirPath
	 * @param string $filesDirPath
	 */
	public function __construct($baseDirPath, $filesDirPath = '/media/images')
	{
		$this->baseDirPath = (string) $baseDirPath;
		$this->filesDirPath = (string) $filesDirPath;
	}



	/**
	 * @param string $path
	 */
	public function setFilesDir($path)
	{
		$this->filesDirPath = (string) $path;
	}



	/**
	 * Save upladed file and return absolute path
	 * @param \Nette\Http\FileUpload $file
	 * @return string
	 * @throws \Nette\InvalidArgumentException
	 */
	public function saveFile(Nette\Http\FileUpload $file)
	{
		if (!$file->isOk()) {
			throw new \Nette\InvalidArgumentException('File ' . $file->name . ' is not valid.');
		}

		FileSystem::mkDir($this->getAbsolutePath(), true, 0777, false);
		$name = Strings::webalize($this->removeFileType($file->name)) . '.' . $this->getFileType($file->name);
		$filePath = $this->getAbsolutePath() . DIRECTORY_SEPARATOR . $name;

		if (!file_exists($filePath)) {
			$file->move($filePath);
		} else {
			$new_name = Strings::random(5) . '_' . $name;
			$file->move(str_replace($name, $new_name, $filePath));
			$name = $new_name;
		}

		return $this->filesDirPath . DIRECTORY_SEPARATOR . $name;
	}



	/**
	 * Save file from url on server
	 * @param $url
	 * @return bool|int
	 */
	public function downloadFile($url)
	{
		$fileDir = $this->getAbsolutePath() . DIRECTORY_SEPARATOR . $this->getFileName($url);

		if ($file = FileSystem::read($url, false)) {
			return FileSystem::write($fileDir, $file, true, 0777, false);
		}

		return false;
	}



	/**
	 * Return ending of filename
	 * @param string $name
	 * @return mixed
	 */
	protected function getFileType($name)
	{
		return str_replace('.', '', strrchr($name, '.'));
	}



	/**
	 * Return name of file without ending
	 * @param string $name
	 * @return mixed
	 */
	protected function removeFileType($name)
	{
		return str_replace('.' . $this->getFileType($name), '', $name);
	}



	/**
	 * Return name of file from URL or absolute path
	 * @param string $path
	 * @return mixed
	 */
	protected function getFileName($path)
	{
		if (Strings::contains($path, DIRECTORY_SEPARATOR)) {
			return str_replace(DIRECTORY_SEPARATOR, '', strrchr($path, DIRECTORY_SEPARATOR));
		} else {
			return $path;
		}

	}


	
	/**
	 * @return string
	 */
	protected function getAbsolutePath()
	{
		return $this->baseDirPath . $this->filesDirPath;
	}

}
