<?php

namespace Wiebenieuwenhuis;

use bunq\Model\Generated\Endpoint\MonetaryAccount;

Class Accounts {

	private $bunqApi;

	/**
	 * Accounts constructor.
	 *
	 * @param $bunqApi
	 */
	public function __construct($bunqApi)
	{
		$this->bunqApi = $bunqApi;
	}

	/**
	 * return all the accounts
	 *
	 * @return array
	 */
	public function all()
	{
		$list = [];
		$items = MonetaryAccount::listing($this->bunqApi->apiContext, $this->bunqApi->user->getId())->getValue();
		foreach($items as $item){
			$list[] = $item->getMonetaryAccountBank();
		}
		return $list;
	}

	/**
	 * Return a single account by ID
	 *
	 * @param $account_id
	 *
	 * @return \bunq\Model\Generated\Endpoint\MonetaryAccountBank
	 */
	public function get($account_id)
	{
		return MonetaryAccount::get($this->bunqApi->apiContext, $this->bunqApi->user->getId(), $account_id)->getValue()->getMonetaryAccountBank();
	}
}