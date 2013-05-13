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
use JedenWeb\Templating\ITemplateConfigurator;
use Nette;
use Nette\Application\UI\PresenterComponent;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Presenter extends Nette\Application\UI\Presenter
{

	/**
	 * @var ITemplateConfigurator
	 */
	protected $templateConfigurator;

	/**
	 * @var array
	 */
	private $_flashes = array();	


	/*********************** components ***********************/



	/**
	 * @author  Jiří Šifalda
	 * @param string $name
	 * @return \Nette\Application\UI\Multiplier|\Nette\ComponentModel\IComponent
	 */
	protected function createComponent($name)
	{
		$method = 'createComponent' . ucfirst($name);
		if (method_exists($this, $method)) {
			$this->checkRequirements($this->getReflection()->getMethod($method));
			
			if (\Nette\Reflection\Method::from($this, $method)->hasAnnotation('multiple')) {
				$presenter = $this;
				return new \Nette\Application\UI\Multiplier(function ($id) use ($presenter, $method) {
					$defaultArgs = array($presenter, $id);
					return call_user_func_array(array($presenter, $method), $defaultArgs);
				});
				# in PHP 5.4 factory for multiplied component can be protected
				# return new UI\Multiplier(function ($id) use ($name) {
				#	return $this->$method($this, $id, $this->getDataset($name));
				# });
			}
		}

		return parent::createComponent($name);
	}
	
	
	/**
	 * Checks for requirements such as authorization.
	 *
	 * @param \Reflector $element
	 *
	 * @return void
	 */
	public function checkRequirements($element)
	{
		if ($element instanceof \Reflector) {
			$this->getUser()->protectElement($element);
		}
	}	
	
	
	/*********************** secured links ***********************/
	
	
	/**
	 * @author Jan Skrasek
	 * {@inheritdoc}
	 */
	public function link($destination, $args = array())
	{
		if (!is_array($args)) {
			$args = func_get_args();
			array_shift($args);
		}

		$link = parent::link($destination, $args);
		return $this->getPresenter()->createSecuredLink($this, $link, $destination);
	}


	/**
	 * For @secured annotated signal handler methods checks if URL parameters has not been changed
	 *
	 * @author Jan Skrasek
	 * @throws Nette\Application\UI\BadSignalException if there is no handler method or the security token does not match
	 * @throws \LogicException if there is no redirect in a secured signal
	 */
	public function signalReceived($signal)
	{
		$method = $this->formatSignalMethod($signal);
		$secured = FALSE;

		if (method_exists($this, $method)) {
			$reflection = new Nette\Reflection\Method($this, $method);
			$secured = $reflection->hasAnnotation('secured');
			if ($secured) {
				$params = array($this->getUniqueId());
				if ($this->params) {
					foreach ($reflection->getParameters() as $param) {
						if ($param->isOptional()) {
							continue;
						}
						if (isset($this->params[$param->name])) {
							$params[$param->name] = $this->params[$param->name];
						}
					}
				}

				if (!isset($this->params['_sec']) || $this->params['_sec'] !== $this->getPresenter()->getCsrfToken(get_class($this), $method, $params)) {
					throw new Nette\Application\UI\BadSignalException("Invalid security token for signal '$signal' in class {$this->reflection->name}.");
				}
			}
		}

		parent::signalReceived($signal);

		if ($secured && !$this->getPresenter()->isAjax()) {
			throw new \LogicException("Secured signal '$signal' did not redirect. Possible csrf-token reveal by http referer header.");
		}
	}
	
	
	/**
	 * @author Jan Skrasek
	 * @param  PresenterComponent
	 * @param  string created URL
	 * @param  string
	 * @return string
	 * @throws Nette\Application\UI\InvalidLinkException
	 */
	public function createSecuredLink(PresenterComponent $component, $link, $destination)
	{
		/** @var $lastRequest Nette\Application\Request */
		$lastRequest = $this->lastCreatedRequest;

		do {
			if ($lastRequest === NULL) {
				break;
			}

			$params = $lastRequest->getParameters();
			if (!isset($params[Nette\Application\UI\Presenter::SIGNAL_KEY])) {
				break;
			}

			if (($pos = strpos($destination, '#')) !== FALSE) {
				$destination = substr($destination, 0, $pos);
			}

			$signal = strtr(rtrim($destination, '!'), ':', '-');
			$a = strrpos($signal, '-');
			if ($a !== FALSE) {
				$component = $component->getComponent(substr($signal, 0, $a));
				$signal = (string) substr($signal, $a + 1);
			}
			if ($signal == NULL) { // intentionally ==
				throw new Nette\Application\UI\InvalidLinkException("Signal must be non-empty string.");
			}

			// only PresenterComponent
			if (!$component instanceof PresenterComponent) {
				break;
			}

			$reflection = $component->getReflection();
			$method = $component->formatSignalMethod($signal);
			$signalReflection = $reflection->getMethod($method);

			if (!$signalReflection->hasAnnotation('secured')) {
				break;
			}

			$origParams = $lastRequest->getParameters();
			$protectedParams = array($component->getUniqueId());
			foreach ($signalReflection->getParameters() as $param) {
				if ($param->isOptional()) {
					continue;
				}
				if (isset($origParams[$component->getParameterId($param->name)])) {
					$protectedParams[$param->name] = $origParams[$component->getParameterId($param->name)];
				}
			}

			$protectedParam = $this->getCsrfToken(get_class($component), $method, $protectedParams);

			if (($pos = strpos($link, '#')) === FALSE) {
				$fragment = '';
			} else {
				$fragment = substr($link, $pos);
				$link = substr($link, 0, $pos);
			}

			$link .= (strpos($link, '?') !== FALSE ? '&' : '?') . $component->getParameterId('_sec') . '=' . $protectedParam . $fragment;
		} while (FALSE);

		return $link;
	}


	/**
	 * Returns unique token for method and params
	 * @author Jan Skrasek
	 * @param string
	 * @param string
	 * @param array
	 * @return string
	 */
	public function getCsrfToken($control, $method, $params)
	{
		$session = $this->getSession('Nextras.Application.UI.SecuredLinksPresenterTrait');
		if (!isset($session->token)) {
			$session->token = Nette\Utils\Strings::random();
		}

		$params = Nette\Utils\Arrays::flatten($params);
		$params = implode('|', array_keys($params)) . '|' . implode('|', array_values($params));
		return substr(md5($control . $method . $params . $session->token), 0, 8);
	}
	
	
	/**
	 * Saves the message to template, that can be displayed after redirect.
	 * @param  string
	 * @param  string
	 * @return \stdClass
	 */
	public function flashMessage($message, $type = 'info', $withoutSession = FALSE)
	{
		if ($withoutSession) {
			$this->_flashes[] = $flash = (object)array(
				'message' => $message,
				'type' => $type,
			);
		} else {
			$flash = parent::flashMessage($message, $type);
		}

		$id = $this->getParameterId('flash');
		$messages = $this->getPresenter()->getFlashSession()->$id;
		$this->getTemplate()->flashes = array_merge((array)$messages, $this->_flashes);

		return $flash;
	}	


	/*********************** templating ***********************/


	protected function beforeRender()
	{
		parent::beforeRender();

		if ($this->isAjax() && $this->hasFlashSession()) {
			$this->invalidateControl('flash');
		}
	}



	/**
	 * @param ITemplateConfigurator $configurator
	 */
	public function setTemplateConfigurator(ITemplateConfigurator $configurator = NULL)
	{
		$this->templateConfigurator = $configurator;
	}



	/**
	 * @param string|null $class
	 *
	 * @return \Nette\Templating\Template
	 */
	protected function createTemplate($class = NULL)
	{
		$template = parent::createTemplate($class);

		if ($this->templateConfigurator !== NULL) {
			$this->templateConfigurator->configure($template);
		}

		return $template;
	}



	/**
	 * @param \Nette\Templating\Template $template
	 *
	 * @return void
	 */
	public function templatePrepareFilters($template)
	{
		$engine = $this->context->createNette__Latte();

		if ($this->templateConfigurator !== NULL) {
			$this->templateConfigurator->prepareFilters($engine);
		}

		$template->registerFilter($engine);
	}



	/***********************  ***********************/



	/**
	 * @param string $module
	 * @return boolean
	 */
	public function isModuleCurrent($module)
	{
		if (!$a = strrpos($this->name, ':')) { // not in module
			return false;
		}

		return ltrim($module, ':') === substr($this->name, 0, $a);
	}



	/**
	 * @return string
	 */
	protected function getBaseUrl()
	{
		return $this->getHttpRequest()->url->baseUrl;
	}



	/*********************** inject ***********************/

	

	/**
	 * @param \JedenWeb\Templating\ITemplateConfigurator $templateConfigurator
	 * @throws \Nette\InvlidStateException
	 */
	final public function injectTemplateConfigurator(ITemplateConfigurator $templateConfigurator)
	{
		if ($this->templateConfigurator) {
			throw new \Nette\InvlidStateException;
		}

		$this->templateConfigurator = $templateConfigurator;
	}

}
