<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Latte\Macros;

use Nette;
use Nette\Latte;
use Nette\Latte\MacroNode;
use Nette\Latte\PhpWriter;
use Nette\Latte\CompileException;
use Nette\Utils\Strings;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class UIMacros extends \Nette\Latte\Macros\UIMacros
{

	/** @var array */
	protected $modules;

	/** @var array */
	private $namedBlocks = array();

	/** @var bool */
	private $extends;

	

	/**
	 * @param \Nette\Latte\Compiler $compiler
	 * @return UIMacros
	 */
	public static function install(Latte\Compiler $compiler)
	{
		$me = new static($compiler);

		$me->addMacro('extends', array($me, 'macroExtends'));
		$me->addMacro('layout', array($me, 'macroExtends'));
		
		$me->addMacro('path', array($me, 'macroPath'));
		return $me;
	}


	/**
	 * @param array $modules
	 */
	public function setModules(array $modules)
	{
		$this->modules = $modules;
	}


	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return type
	 */
	public function macroExtends(MacroNode $node, PhpWriter $writer)
	{
		$node->args = \JedenWeb\Module\Helpers::expandPath($node->args, $this->modules);
		$node->tokenizer = new \Nette\Latte\MacroTokenizer($node->args);
		$writer = new PhpWriter($node->tokenizer);
		return parent::macroExtends($node, $writer);
	}


	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return type
	 */
	public function macroPath(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write("echo \$basePath . '/' . \Venne\Module\Helpers::expandResource(%node.word)");
	}

}
