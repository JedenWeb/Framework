<?php

/**
 * This file is part of the www.jedenweb.cz webpage (http://www.jedenweb.cz/)
 *
 * Copyright (c) 2012 Pavel Jurásek (jurasekpavel@ctyrimedia.cz), Vojtěch Jurásek (jurasek@ctyrimedia.cz)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace JedenWeb\Database\Repository;

use JedenWeb;
use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class UserRepository extends Repository
{

	/**
	 * @param Nette\Database\Table\ActiveRow|FALSE $row
	 * @return Nette\Database\Table\ActiveRow|bool
	 */
	private function create($row)
	{
		if ($row === FALSE) {
			return FALSE;
		}

		$row = $row->toArray();
		$row['password'] = new JedenWeb\Types\Password($row['password'], $row['salt']);

		return new \JedenWeb\Security\Identity($row['id'], array('authenticated'), $row);
	}



	/**
	 * @param string $username
	 * @return Nette\Database\Table\ActiveRow
	 */
	public function findByUsernameOrEmail($username)
	{
		if (strpos($username, '@') === FALSE) {
			$row = $this->findByUsername($username);
		} else {
			$row = $this->findByEmail($username);
		}

		return $this->create($row);
	}



	/**
	 * @param string $username
	 * @return Nette\Database\Table\ActiveRow
	 */
	public function findByUsername($username)
	{
		return $this->getTable()->where(array(
			'username' => $username
		))->fetch();
	}



	/**
	 * @param string $email
	 * @return Nette\Database\Table\ActiveRow
	 */
	public function findByEmail($email)
	{
		return $this->getTable()->where(array(
			'email' => $email
		))->fetch();
	}

}
