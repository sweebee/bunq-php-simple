<?php

namespace Wiebenieuwenhuis;

use bunq\Model\Generated\Object\NotificationFilter;
use bunq\Model\Generated\Endpoint\UserPerson;

Class Callbacks {

	private $bunqApi;

	public function __construct($bunqApi) {
		$this->bunqApi = $bunqApi;
	}

	/**
	 * Get all callbacks
	 *
	 * @param bool $all
	 *
	 * @return array
	 */
	public function get($all = false)
	{
		$notifications = $this->bunqApi->user->getNotificationFilters();

		if($all){
			return $notifications;
		}

		$data = [];
		for ($i = 0; $i < count($notifications); $i++) {
			$filter = $notifications[$i];
			// Remove any URL notification callbacks for the MUTATION category from the array
			if ($filter->getNotificationDeliveryMethod() == 'URL') {
				$data[] = $notifications[$i];
			}
		}
		return $data;
	}

	/**
	 * Create a callback url
	 *
	 * @param $url
	 * @param $category
	 *
	 * @return bool
	 */
	public function create($url, $category = 'MUTATION')
	{
		if($this->exists($url)){
			return true;
		}

		$notifications = $this->get(true);

		$notifications[] = new NotificationFilter('URL', $url, $category);

		UserPerson::update($this->bunqApi->apiContext, [
			UserPerson::FIELD_NOTIFICATION_FILTERS => $notifications],
			$this->bunqApi->user->getId()
		);

		return true;
	}

	/**
	 * Delete a callback url
	 *
	 * @param $url
	 *
	 * @return bool
	 */
	public function delete($url)
	{
		if(!$this->exists($url)){
			return true;
		}

		$notifications = $this->get(true);

		for ($i = 0; $i < count($notifications); $i++) {
			$filter = $notifications[$i];
			// Remove any URL notification callbacks for the MUTATION category from the array
			if ($filter->getNotificationDeliveryMethod() == 'URL' && $notifications[$i]->getNotificationTarget() == $url) {
				unset($notifications[$i]);
			}
		}

		UserPerson::update($this->bunqApi->apiContext, [
			UserPerson::FIELD_NOTIFICATION_FILTERS => $notifications],
			$this->bunqApi->user->getId()
		);

		return true;
	}

	/**
	 * Check if a callback url exists
	 *
	 * @param $url
	 *
	 * @return bool
	 */
	private function exists($url)
	{
		$notifications = $this->bunqApi->user->getNotificationFilters();
		for ($i = 0; $i < count($notifications); $i++) {
			$filter = $notifications[$i];
			// Remove any URL notification callbacks for the MUTATION category from the array
			if ($filter->getNotificationDeliveryMethod() == 'URL' && $notifications[$i]->getNotificationTarget() == $url) {
				return true;
			}
		}
		return false;
	}
}