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

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class HeadMacro extends Nette\Latte\Macros\MacroSet
{

	/**
	 * @param \Nette\Latte\Compiler $compiler
	 * @return self
	 */
	public static function install(\Nette\Latte\Compiler $compiler)
	{
		$me = new static($compiler);
		$me->addMacro('head', array($me, "headBegin"), array($me, "headEnd"));
		$me->addMacro('body', array($me, "bodyBegin"), array($me, "bodyEnd"));
		$me->addMacro('content', array($me, 'contentBegin'));
		$me->addMacro('extensions', array($me, 'extensionsBegin'));
		return $me;
	}



	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return string
	 */
	public function headBegin(Nette\Latte\MacroNode $node, Nette\Latte\PhpWriter $writer)
	{
		return $writer->write('ob_start();');
	}



	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return string
	 */
	public function headEnd(Nette\Latte\MacroNode $node, Nette\Latte\PhpWriter $writer)
	{
		return $writer->write('$_headMacroData = ob_get_clean();');
	}



	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return string
	 */
	public function bodyBegin(Nette\Latte\MacroNode $node, Nette\Latte\PhpWriter $writer)
	{
		return $writer->write('ob_start();');
	}



	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return string
	 */
	public function bodyEnd(Nette\Latte\MacroNode $node, Nette\Latte\PhpWriter $writer)
	{
		return $writer->write('$_bodyMacroData = ob_get_clean();?><head>
			<?php echo $presenter["head"]->render(); echo $_headMacroData;?>
			</head>

			<body<?php if($basePath){?> data-basepath="<?php echo $basePath;?>"<?php } ?>>
			<?php if ($presenter instanceof \CmsModule\Presenters\FrontPresenter && $presenter->getUser()->isLoggedIn() && $presenter->isAuthorized(":Cms:Admin:Panel:") ) { echo \'<div id="venne-panel-container" style="position: fixed; top: 0; left: 0; z-index: 9999999; width: 100%; height: 43px; overflow: hidden;"><iframe src="\'.$basePath.\'/admin/en/panel?mode=1" scrolling="no" allowtransparency="true" style="width: 100%; height: 100%; overflow: hidden;" frameborder="0" id="venne-panel"></iframe></div>\'; } ?>
			<?php echo $_bodyMacroData;?>
			</body>
			<?php
		');
	}



	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return string
	 */
//	public function contentBegin(Nette\Latte\MacroNode $node, Nette\Latte\PhpWriter $writer)
//	{
//		return $writer->write('Nette\Latte\Macros\UIMacros::callBlock' . "(\$_l, 'content', " . '$template->getParameters())');
//	}



	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return string
	 */
//	public function extensionsBegin(Nette\Latte\MacroNode $node, Nette\Latte\PhpWriter $writer)
//	{
//		return $writer->write('$presenter->context->eventManager->dispatchEvent(\Venne\ContentExtension\Events::onContentExtensionRender);');
//	}

}
