<?php

namespace JedenWeb\Module;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class HelpersTest extends \PHPUnit_Framework_TestCase
{

	public function testExpandResource()
	{
		$this->assertEquals('resources/core/js/jquery/jquery.js', Helpers::expandResource('@CoreModule/js/jquery/jquery.js'));
		$this->assertEquals('resources/core/admin/css/bootstrap.min.css', Helpers::expandResource('@CoreModule/admin/css/bootstrap.min.css'));
	}

}
