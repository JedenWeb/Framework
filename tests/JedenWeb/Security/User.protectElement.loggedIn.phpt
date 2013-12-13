<?php

/**
 * Test: JedenWeb\Security\User
 *
 * @author     Pavel Jurasek
 * @package    JedenWeb\Security
 */

use JedenWeb\Security\User;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/User.protectElement.init.php';


$container = id(new JedenWeb\Configurator)->setTempDirectory(TEMP_DIR)->createContainer();

/* @var $user User */
$user = $container->getService('user');

\Tester\Assert::false($user->isLoggedIn());



$presenter = new LoggedPresenter;
\Tester\Assert::exception(function() use ($user, $presenter) {
	$user->protectElement($presenter->getReflection());
}, 'Nette\Application\ForbiddenRequestException', "User is not logged in.");



$presenter = new LoggedWithCustomMessagePresenter;
\Tester\Assert::exception(function() use ($user, $presenter) {
	$user->protectElement($presenter->getReflection());
}, 'Nette\Application\ForbiddenRequestException', $message = 'You must be logged in');



$user->login(new Nette\Security\Identity('uid', array('user')));
\Tester\Assert::true($user->isLoggedIn());
\Tester\Assert::null($user->protectElement($presenter->getReflection()));



$user->logout();
$presenter = new LoggedPresenter;
\Tester\Assert::exception(function() use ($user, $presenter) {
	$user->protectElement($presenter->getReflection());
}, 'Nette\Application\ForbiddenRequestException', "User 'uid' is not logged in.");
