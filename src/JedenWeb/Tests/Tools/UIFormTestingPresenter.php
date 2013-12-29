<?php

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
