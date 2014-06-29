<?php

/**
 * Overview of possible annotations
 * 
 * @secured([message])
 * 
 * @logged([message]) -> user is logged in
 * @role(role1[,role2, ..., [message = <message>]]) -> logged in and in role
 * @allowed(resource, privilege[, message = <message>]) -> logged in and with permission
 */

namespace JedenWeb\Security;

use JedenWeb;
use Nette;
use Nette\Reflection;
use Nette\Application\ForbiddenRequestException;
use Nette\Security\IAuthorizator;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 * @author Filip Procházka <filip.prochazka@kdyby.org>
 */
class User extends Nette\Security\User
{

	/**
	 * @param \Nette\Reflection\ClassType|\Nette\Reflection\Method
	 * @param string
	 * @throws \Nette\Application\ForbiddenRequestException
	 * @throws \JedenWeb\UnexpectedValueException
	 * @return bool
	 */
	public function protectElement(\Reflector $element, $message = NULL)
	{
		if (!$element instanceof Reflection\Method && !$element instanceof Reflection\ClassType) {
			return FALSE;
		}

		if (!$element->hasAnnotation('secured')) {
			return FALSE;
		}
		$message = is_string($element->getAnnotation('secured')) ? $element->getAnnotation('secured') : NULL;

		$this->checkLogged($element, $message);
		$this->checkRole($element, $message);
		$this->checkPermission($element, $message);
	}


	/**
	 * @param \Reflector
	 * @throws ForbiddenRequestException
	 */
	private function checkLogged(\Reflector $element, $message = NULL)
	{
		if ($element->hasAnnotation($annotation = 'logged') || $element->hasAnnotation($annotation = 'loggedIn')) {
			if (is_string($element->getAnnotation($annotation))) {
				$message = $element->getAnnotation($annotation);
			}

			if (!$this->isLoggedIn()) {
				throw new ForbiddenRequestException($message ?: ($this->getId() ? "User ". "'$this->id'" : 'User') . " is not logged in.");
			}
		}
	}


	/**
	 * @param \Reflector
	 * @throws ForbiddenRequestException
	 */
	private function checkRole(\Reflector $element, $message = NULL)
	{
		if ($element->hasAnnotation($annotation = 'roles') || $element->hasAnnotation($annotation = 'role')) {
			$roles = (array) $element->getAnnotation($annotation);
			if (isset($roles['message'])) {
				$message = $roles['message'];
				unset($roles['message']);
			}

			$success = FALSE;
			foreach ($roles as $role) {
				if ($this->isInRole($role)) {
					$success = TRUE;
					break;
				}
			}

			if (!$success) {
				throw new ForbiddenRequestException($message ?: 
					($this->getId() ? "User ". "'$this->id'" : 'User') . " is not in any of these roles - ".implode(', ', $roles)."."
				);
			}
		}
	}


	/**
	 * @param \Reflector
	 * @throws JedenWeb\UnexpectedValueException
	 */
	private function checkPermission(\Reflector $element, $message = NULL)
	{
		if ($element->hasAnnotation($annotation = 'allowed') || $element->hasAnnotation($annotation = 'Allowed')) {
			$permission = (array) $element->getAnnotation($annotation);
			if (isset($permission['message'])) {
				$message = $permission['message'];
				unset($permission['message']);
			}

			$resource = array_shift($permission) ?: IAuthorizator::ALL;
			$privilege = array_shift($permission) ?: IAuthorizator::ALL;
			$this->needAllowed($resource, $privilege, $message);
		}
	}


	/**
	 * @param string
	 * @param string
	 * @param string
	 *
	 * @throws \Nette\Application\ForbiddenRequestException
	 */
	public function needAllowed($resource = IAuthorizator::ALL, $privilege = IAuthorizator::ALL, $message = NULL)
	{
		if (!$this->isAllowed($resource, $privilege)) {
			throw new ForbiddenRequestException($message ?: "User is not allowed to " . ($privilege ? $privilege : "access") . " the resource" . ($resource ? " '$resource'" : NULL) . ".");
		}
	}

}
