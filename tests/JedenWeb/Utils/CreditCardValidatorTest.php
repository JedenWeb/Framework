<?php

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 * @method void setUp()
 */
class CreditCardValidatorTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var string
	 */
	private $s = '4408-0412-3456-7893';



	public function testReplacement()
	{
		$this->assertSame('4408041234567893', Nette\Utils\Strings::replace($this->s, '([ -])', ''));
	}


	
	public function testIsCreditCard()
	{
		$this->assertTrue(\JedenWeb\Utils\Validators::isCreditCard($this->s));
	}

}
