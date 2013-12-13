<?php

namespace JedenWeb\Application\UI;

/**
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 */
class TemplateForm extends Form
{

	/** @var \Nette\Templating\ITemplate */
	protected $template;



	public function __construct()
	{
		parent::__construct();
	}



	/**
	 * @return string
	 */
	protected function getTemplateFile()
	{
		$reflection = $this->getReflection();
		return dirname($reflection->getFileName()) . "/" . $reflection->getShortName() . ".latte";
	}



	/**
	 * @return \Nette\Templating\ITemplate
	 */
	protected function createTemplate()
	{
		$template = clone $this->getPresenter()->getTemplate();
		return $template->setFile($this->getTemplateFile());
	}



	/**
	 * @return \Nette\Templating\ITemplate
	 */
	public function getTemplate()
	{
		if (empty($this->template)) {
			$this->template = $this->createTemplate();
		}

		return $this->template;
	}



	public function render()
	{
		// render("begin") or render("end")
		$args = func_get_args();
		if ($args) {
			parent::render($args[0]);
			return;
		}

		$this->getTemplate()->_form = $this->getTemplate()->form = $this;
		$this->getTemplate()->render();
	}

}
