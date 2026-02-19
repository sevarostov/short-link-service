<?php

namespace tests\unit\services;

use app\models\UserIp;
use app\services\UserIpService;
use Codeception\Test\Unit;

class UserIpServiceTest extends Unit {
	protected UserIpService $service;

	protected function _before()
	{
		$this->service = new UserIpService();
		UserIp::deleteAll();
	}

	protected function _after()
	{
		UserIp::deleteAll();
	}

	public function testSaveNewIpSuccessfully()
	{
		$ip = '192.168.1.100';

		$userIp = $this->service->saveUserIp($ip);
		$this->assertInstanceOf(UserIp::class, $userIp);
		$this->assertEquals($ip, $userIp->ip);
		$this->assertNotNull($userIp->created_at);
		$this->assertNotEmpty($userIp->id);

		$dbRecord = UserIp::findOne(['ip' => $ip]);
		$this->assertNotNull($dbRecord);
		$this->assertEquals($ip, $dbRecord->ip);
	}
}
