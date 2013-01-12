<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Latte;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class Engine extends \Nette\Latte\Engine
{

	/**
	 * @var \Nette\Latte\Parser
	 */
	private $parser;

	/**
	 * @var \Nette\Latte\Compiler
	 */
	private $compiler;


	public function __construct()
	{
		$this->parser = new \Nette\Latte\Parser;
		$this->compiler = new \Nette\Latte\Compiler();
		$this->compiler->defaultContentType = \Nette\Latte\Compiler::CONTENT_XHTML;

		\Nette\Latte\Macros\CoreMacros::install($this->compiler);
		$this->compiler->addMacro('cache', new \Nette\Latte\Macros\CacheMacro($this->compiler));
		\Nette\Latte\Macros\FormMacros::install($this->compiler);
	}



	/**
	 * Invokes filter.
	 * @param string
	 * @return string
	 */
	public function __invoke($s)
	{
		return $this->compiler->compile($this->parser->parse($s));
	}



	/**
	 * @return \Nette\Latte\Parser
	 */
	public function getParser()
	{
		return $this->parser;
	}



	/**
	 * @return \Nette\Latte\Compiler
	 */
	public function getCompiler()
	{
		return $this->compiler;
	}

}
