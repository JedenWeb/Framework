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
$user->login(new Nette\Security\Identity('uid', array('user')));



$presenter = new RolePresenter;
\Tester\Assert::null($user->protectElement($presenter->getReflection()));



$presenter = new InvalidRolePresenter;
\Tester\Assert::exception(function() use ($user, $presenter) {
	$user->protectElement($presenter->getReflection());
}, 'Nette\Application\ForbiddenRequestException', "User 'uid' is not in any of these roles - one, two, three.");



$presenter = new InvalidRoleWithCustomMessagePresenter;
\Tester\Assert::exception(function() use ($user, $presenter) {
	$user->protectElement($presenter->getReflection());
}, 'Nette\Application\ForbiddenRequestException', "You must be in role 'user'.");
