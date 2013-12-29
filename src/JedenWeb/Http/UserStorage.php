<?php

namespace JedenWeb\Http;

use JedenWeb;
use Nette;

/**
 * @author Jáchym Toušek
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class UserStorage extends Nette\Http\UserStorage
{

	/** Log-out reason */
	const IDENTITY_CHANGED = 16;
	
	
	
	/**
	 * Checks if the identity is still valid.
	 * @param \Nette\Security\IIdentity $identity
	 * @return bool
	 */
	protected function isIdentityValid(Nette\Security\IIdentity $identity)
	{
		return TRUE; // dummy implementation of Identity validation
	}	
 
	
	
	/**
	 * Returns and initializes $this->sessionSection.
	 * @param bool $need
	 * @return Nette\Http\SessionSection
	 */
	protected function getSessionSection($need)
	{
		$section = parent::getSessionSection($need);
 
		if ($section->authenticated && !$this->isIdentityValid($section->identity)) {
			$section->authenticated = FALSE;
			$section->reason = self::IDENTITY_CHANGED;
			if ($section->expireIdentity) {
				unset($section->identity);
			}
			unset($section->expireTime, $section->expireDelta, $section->expireIdentity,
				$section->expireBrowser, $section->browserCheck, $section->authTime);
		}
 
		return $section;
	}

}
