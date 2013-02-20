<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Managers\Image;

use JedenWeb;
use Nette;
use Nette\Latte\PhpWriter;
use Nette\Latte\MacroNode;
use Nette\Latte\Compiler;

/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class ImageMacro extends Nette\Latte\Macros\MacroSet
{

	/**
	 * @var bool
	 */
	private $isUsed = FALSE;



	/**
	 * @param \Nette\Latte\Compiler $compiler
	 *
	 * @return ImgMacro|\Nette\Latte\Macros\MacroSet
	 */
	public static function install(Compiler $compiler)
	{
		$me = new static($compiler);
		// todo: předání parametrů s velikostí

		/**
		 * {img} je obecně pro jakýkoliv obrázek, který je veřejný na webu.
		 */
		$me->addMacro('img', array($me, 'macroImg'), NULL, array($me, 'macroAttrImg'));

		/**
		 * {tempImg} je kvůli obrázkům, které nejsou "schválené" a je pravděpodobné,
		 * že se budou mazat. Tak aby byly ve zvláštní složce, kterou může promazávat robot.
		 * Pokud nic takového nepotřebujete, používejte prostě {img}.
		 */
		$me->addMacro('tempImg', array($me, 'macroImgTemp'), NULL, array($me, 'macroAttrImgTemp'));

		return $me;
	}



	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroImg(MacroNode $node, PhpWriter $writer)
	{
		$this->isUsed = TRUE;
		return $writer->write('echo %escape($_imagePipe->request(%node.word))');
	}



	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroAttrImg(MacroNode $node, PhpWriter $writer)
	{
		$this->isUsed = TRUE;
		return $writer->write('?>src="<?php echo %escape($_imagePipe->request(%node.word))?>" <?php');
	}



	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroImgTemp(MacroNode $node, PhpWriter $writer)
	{
		$this->isUsed = TRUE;
		return $writer->write('echo %escape($_imagePipe->request(%node.word, array(), $_imagePipe::TEMP))');
	}



	/**
	 * @param \Nette\Latte\MacroNode $node
	 * @param \Nette\Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroAttrImgTemp(MacroNode $node, PhpWriter $writer)
	{
		$this->isUsed = TRUE;
		return $writer->write('?>src="<?php echo %escape($_imagePipe->request(%node.word, array(), $_imagePipe::TEMP))?>" <?php');
	}



	/**
	 */
	public function initialize()
	{
		$this->isUsed = FALSE;
	}



	/**
	 * Finishes template parsing.
	 * @return array(prolog, epilog)
	 */
	public function finalize()
	{
		if (!$this->isUsed) {
			return array();
		}

		return array(
			get_called_class() . '::validateTemplateParams($template);',
			NULL
		);
	}



	/**
	 * @param \Nette\Templating\Template $template
	 * @throws \Nette\InvalidStateException
	 */
	public static function validateTemplateParams(Nette\Templating\Template $template)
	{
		$params = $template->getParameters();
		if (!isset($params['_imagePipe']) || !$params['_imagePipe'] instanceof ImagePipe) {
			$where = isset($params['control']) ?
				" of component " . get_class($params['control']) . '(' . $params['control']->getName() . ')'
				: NULL;

			throw new Nette\InvalidStateException(
				'Please provide an instanceof Img\\ImagePipe ' .
				'as a parameter $_imagePipe to template' . $where
			);
		}
	}

}
