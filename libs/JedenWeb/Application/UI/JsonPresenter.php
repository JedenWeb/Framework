<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Application\UI;

use JedenWeb;
use Nette;

/**
 * @author Pavel Kučera
 * @property \Nette\Application\Responses\JsonResponse $response
 */
abstract class JsonPresenter extends Presenter
{

	/**
	 * @var \Nette\Application\Responses\JsonResponse
	 */
	private $response;



	/**
	 * @return \Nette\Application\Responses\JsonResponse
	 */
	public function getResponse()
	{
		if (!$this->response) {
			$this->response = new Nette\Application\Responses\JsonResponse(array());
		}

		return $this->response;
	}



	/**
	 * @param string $class
	 * @throws \Nette\InvalidStateException
	 */
	protected function createTemplate($class = null)
	{
		throw new Nette\InvalidStateException("Json presenter does not support access to \$template use \$response instead.");
	}



	/**
	 */
	public function sendTemplate()
	{
		$this->sendResponse($this->response);
	}

}
