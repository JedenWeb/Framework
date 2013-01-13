<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Tests\Tools;

use JedenWeb;

/**
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 */
class UIFormTestingPresenter extends JedenWeb\Application\PresenterManager
{

	/** @var \Nette\Application\UI\Form */
	private $form;

	/**
	 * @param \Nette\Application\UI\Form $form
	 */
	public function __construct(\Nette\Application\UI\Form $form)
	{
		parent::__construct();
		$this->form = $form;
	}

	/**
	 * Just terminate the rendering
	 */
	public function renderDefault()
	{
		$this->terminate();
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm()
	{
		return $this->form;
	}

}
