<?php

namespace app\services;

use app\models\Link;
use app\models\UserIp;
use app\models\UserIpLinkLog;
use yii\db\Exception;

class UserIpLinkLogService
{

	/**
	 * Creates visit log entry
	 *
	 * @param UserIp $ip User's IP address
	 * @param Link $link
	 *
	 * @return UserIpLinkLog
	 */
	public function log(UserIp $ip, Link $link): UserIpLinkLog
	{
		$log = new UserIpLinkLog();
		$log->user_ip_id = $ip->id;
		$log->link_id = $link->id;
		$log->created_at = date('Y-m-d H:i:s');

		try {
			$log->save();
		} catch (Exception $exception) {
			//@todo log errors
		}

		return $log;
	}
}
