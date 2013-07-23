<?php

/**
 * Test: JedenWeb\Utils\Arrays
 *
 * @author     Pavel Jurasek
 * @package    JedenWeb\Utils
 */

use JedenWeb\Utils\Arrays;


require __DIR__ . '/../bootstrap.php';

//

$input = array(
	'a' => 1,
	'b' => 2,
	array(
		'c' => 3,
		'd' => 4
	),
);
$output = array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4);

\Tester\Assert::same($output, Arrays::flattenKeys($input));

//

$input = array(
	1,
	array(2)
);

\Tester\Assert::exception(function() use ($input) {
	Arrays::flattenKeys($input);
}, 'JedenWeb\InvalidStateException');

//

$input = array(
	1 => 'a',
	2 => 'b',
	array('c', 'd', 'e'),
);
$output = array(1 => 'a', 2 => 'b', 0 => 'c', 3 => 'd', 4 => 'e');

\Tester\Assert::same($output, Arrays::flattenKeys($input, FALSE));
