<?php

namespace JedenWeb\Events;

use Nette;
use Nette\Application;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class OnRequestErrorListener extends Nette\Object implements \Kdyby\Events\Subscriber
{
	
	/**
	 * @param \Nette\Application\Application
	 * @param \Nette\Application\Request
	 */
	public function onRequest(Application\Application $application, Application\Request $request)
	{
		$presenter = $request->presenterName;
		$errorPresenter = $application->errorPresenter;

		if(($pos = strrpos($presenter, ':')) !== FALSE) {
			$module = substr($presenter, 0, $pos);
			
			try {
				$errorPresenter =  "$module:Error";
				$errorPresenterClass = $application->presenterFactory->createPresenter($errorPresenter);
			} catch (Nette\Application\InvalidPresenterException $e) {
				$errorPresenter = $application->errorPresenter;
			}
		}

		$application->errorPresenter = $errorPresenter;
	}
	
	
	
	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return array('onRequest');
	}
	
}
