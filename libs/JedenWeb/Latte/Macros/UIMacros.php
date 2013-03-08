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

	/**
	 * @var array
	 */
	protected $modules;

	/** @var array */
	private $namedBlocks = array();

	/** @var bool */
	private $extends;


	/**
	 * @param \Nette\Latte\Compiler $compiler
	 * @return self
	 */
	public static function install(Latte\Compiler $compiler)
	{
		$me = new static($compiler);

		$me->addMacro('include', array($me, 'macroInclude'));
		$me->addMacro('includeblock', array($me, 'macroIncludeBlock'));
		$me->addMacro('extends', array($me, 'macroExtends'));
		$me->addMacro('layout', array($me, 'macroExtends'));
		$me->addMacro('block', array($me, 'macroBlock'), array($me, 'macroBlockEnd'));
		$me->addMacro('define', array($me, 'macroBlock'), array($me, 'macroBlockEnd'));
		$me->addMacro('snippet', array($me, 'macroBlock'), array($me, 'macroBlockEnd'));
		$me->addMacro('ifset', array($me, 'macroIfset'), 'endif');

		$me->addMacro('widget', array($me, 'macroControl')); // deprecated - use control
		$me->addMacro('control', array($me, 'macroControl'));

		$me->addMacro('href', NULL, NULL, function(MacroNode $node, PhpWriter $writer) use ($me) {
			return ' ?> href="<?php ' . $me->macroLink($node, $writer) . ' ?>"<?php ';
		});
		$me->addMacro('plink', array($me, 'macroLink'));
		$me->addMacro('link', array($me, 'macroLink'));
		$me->addMacro('ifCurrent', array($me, 'macroIfCurrent'), 'endif'); // deprecated; use n:class="$presenter->linkCurrent ? ..."

		$me->addMacro('contentType', array($me, 'macroContentType'));
		$me->addMacro('status', array($me, 'macroStatus'));

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
