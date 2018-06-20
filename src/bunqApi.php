<?php

namespace Wiebenieuwenhuis;

use bunq\Context\ApiContext;
use bunq\Model\Generated\Endpoint\User;

Class bunqApi {

	public $apiContext, $user, $accounts, $payments, $callbacks;

	/**
	 * bunqApi constructor.
	 */
	public function __construct($config_file) {
		// Set the api
		$this->apiContext = ApiContext::restore($config_file);

		// Get the userid
		$users = User::listing($this->apiContext)->getValue();
		$this->user = $users[0]->getUserPerson();

		$this->accounts = new Accounts($this);
		$this->payments = new Payments($this);
		$this->callbacks = new Callbacks($this);
	}

}