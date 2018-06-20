<?php

namespace Wiebenieuwenhuis;

use bunq\Model\Generated\Endpoint\Payment as bunqPayment;
use bunq\Model\Generated\Object\Amount;
use bunq\Model\Generated\Object\Pointer;

Class Payments {

	private $bunqApi, $data;

	/**
	 * payment constructor.
	 *
	 * @param $bunqApi
	 */
	public function __construct($bunqApi)
	{
		$this->bunqApi = $bunqApi;
	}

	/**
	 * Get all payments from an account
	 *
	 * @param $account_id
	 */
	public function all($account_id, $params = [], $customHeaders = [])
	{
		return bunqPayment::listing($this->bunqApi->apiContext, $this->bunqApi->user->getId(), $account_id, $params, $customHeaders)->getValue();
	}

	/**
	 * Get a specific payment from an account
	 *
	 * @param $account_id
	 * @param $payment_id
	 *
	 * @return \bunq\Model\Generated\Endpoint\BunqResponsePayment
	 */
	public function get($account_id, $payment_id)
	{
		return bunqPayment::get($this->bunqApi->apiContext, $this->bunqApi->user->getId(), $account_id, $payment_id, $customHeaders = [])->getValue();
	}

	/**
	 * Create a payment
	 *
	 * @return int
	 */
	public function create($from_account, $data, $customHeaders = [])
	{
		$this->map($data);
		$this->validate();

		// Generate the payment
		$paymentMap = [
			bunqPayment::FIELD_AMOUNT               => new Amount($this->data['amount'], $this->data['currency']),
			bunqPayment::FIELD_COUNTERPARTY_ALIAS   => $this->getRecipient(),
			bunqPayment::FIELD_DESCRIPTION          => $this->data['description'],
		];

		// Execute the payment
		return bunqPayment::create($this->bunqApi->apiContext, $paymentMap, $this->bunqApi->user->getId(), $this->getAccount($from_account), $customHeaders)->getValue();
	}

	/**
	 *  Validate the input
	 */
	private function validate()
	{
		if(!is_array($this->data)){
			die('Invalid data, must be an array');
		}
		if(!$this->data['amount']){
			die('No amount provided');
		}
		if(!$this->data['recipient']){
			die('No recipient provided, must MonetaryAccountBank object, account_id or array [type, value, name]');
		}
	}

	/**
	 * @param $data
	 */
	private function map($data)
	{
		$this->data = $data;
		$this->data['amount'] = (string)$this->data['amount'];
		if(!isset($this->data['currency'])){
			$this->data['currency'] = 'EUR';
		}
		if(!isset($this->data['description'])){
			$this->data['description'] = '';
		}
	}

	/**
	 * @return int
	 */
	private function getAccount($account)
	{
		if(is_object($account) && get_class($account) == 'bunq\Model\Generated\Endpoint\MonetaryAccountBank'){
			return $account->getId();
		}
		return $account;
	}

	/**
	 * @return Pointer
	 */
	private function getRecipient()
	{
		$data = $this->data;
		if(is_array($data['recipient'])) {
			$pointer = new Pointer( $data['recipient']['type'], $data['recipient']['value']);
			if($data['recipient']['type'] == 'IBAN'){
				$pointer->setName($data['recipient']['name']);
			}
			return $pointer;
		}

		if(is_object($data['recipient']) && get_class($data['recipient']) == 'bunq\Model\Generated\Endpoint\MonetaryAccountBank'){
			return $data['recipient']->getAlias()[0];
		}
		return $this->bunqApi->account($data['recipient'])->getAlias()[0];
	}
}