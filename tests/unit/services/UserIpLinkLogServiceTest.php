<?php

namespace tests\unit\services;

use app\models\Link;
use app\models\UserIp;
use app\models\UserIpLinkLog;
use app\services\UserIpLinkLogService;
use Codeception\Test\Unit;

class UserIpLinkLogServiceTest extends Unit
{
	/**
	 * @var UserIpLinkLogService
	 */
	protected $service;

	protected function _before()
	{
		$this->service = new UserIpLinkLogService();
		UserIpLinkLog::deleteAll();
		UserIp::deleteAll();
		Link::deleteAll();
	}

	protected function _after()
	{
		UserIpLinkLog::deleteAll();
		UserIp::deleteAll();
		Link::deleteAll();
	}

	public function testLogVisitSuccessfully()
	{
		// Arrange
		$ip = new UserIp();
		$ip->ip = '192.168.1.100';
		$ip->created_at = date('Y-m-d H:i:s');
		$ip->save();

		$link = new Link();
		$link->host = 'https://example.com';
		$link->short = 'abc123';
		$link->counter = 0;
		$link->created_at = date('Y-m-d H:i:s');
		$link->save();

		$log = $this->service->log($ip, $link);

		$this->assertInstanceOf(UserIpLinkLog::class, $log);
		$this->assertEquals($ip->id, $log->user_ip_id);
		$this->assertEquals($link->id, $log->link_id);
		$this->assertNotNull($log->created_at);

		$dbLog = UserIpLinkLog::findOne([
			'user_ip_id' => $ip->id,
			'link_id' => $link->id
		]);

		$this->assertNotNull($dbLog);
		$this->assertEquals($log->id, $dbLog->id);
	}
}
