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

$authorizator = new Nette\Security\Permission;
$authorizator->addRole('user');

$authorizator->addResource('comments');

$authorizator->allow('user', array('comments'), 'view');
$authorizator->deny('user', array('comments'), 'edit');

/* @var $user User */
$user = $container->getService('user');
$user->login(new Nette\Security\Identity('uid', array('user')));
$user->setAuthorizator($authorizator);


$presenter = new PermissionPresenter;
\Tester\Assert::null($user->protectElement($presenter->getReflection()));



$presenter = new InvalidPermissionPresenter;
\Tester\Assert::exception(function() use ($user, $presenter) {
	$user->protectElement($presenter->getReflection());
}, 'Nette\Application\ForbiddenRequestException', "User is not allowed to edit the resource 'comments'.");



$presenter = new InvalidPermissionWithCustomMessagePresenter;
\Tester\Assert::exception(function() use ($user, $presenter) {
	$user->protectElement($presenter->getReflection());
}, 'Nette\Application\ForbiddenRequestException', "You are not allowed to edit comments.");
