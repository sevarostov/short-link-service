<?php

namespace app\services;

use app\models\UserIp;
use Exception;

class UserIpService
{

	/**
	 * Saves user IP
	 *
	 * @param string $ip User's IP address
	 *
	 * @return UserIp
	 * @throws Exception
	 */
	public function saveUserIp(string $ip): UserIp
	{
		$userIp = UserIp::findOne(['ip' => $ip]);

		if (!$userIp) {

			$userIp = new UserIp();
			$userIp->ip = $ip;
			$userIp->created_at = date('Y-m-d H:i:s');

			try {
				$userIp->save();
			} catch (Exception $exception) {
				//@todo log errors
			}
		}

		return $userIp;
	}
}
