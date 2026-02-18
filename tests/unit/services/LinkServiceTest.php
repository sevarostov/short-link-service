<?php

namespace tests\unit\services;

use app\models\Link;
use app\services\LinkService;
use Codeception\Test\Unit;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Yii;

class LinkServiceTest extends Unit
{
	/**
	 * @var LinkService
	 */
	protected $linkService;

	protected function _before()
	{
		$this->linkService = new LinkService();
		$this->url = 'https://svetlana-kartysh.ru';
	}

	/**
	 * Test that generateShortCode() returns a string of length 6
	 */
	public function testGenerateShortCodeReturnsStringOfLengthSix()
	{
		$shortCode = $this->linkService->generateShortCode();

		$this->assertIsString($shortCode);
		$this->assertEquals(6, strlen($shortCode));
	}

	/**
	 * Test that generateShortCode() returns only alphanumeric characters
	 */
	public function testGenerateShortCodeReturnsAlphanumericCharacters()
	{
		$shortCode = $this->linkService->generateShortCode();

		// Check if all characters are alphanumeric (a-z, A-Z, 0-9)
		$this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $shortCode);
	}

	/**
	 * Test that generateShortCode() produces different values on multiple calls
	 * (i.e., not static/predictable)
	 */
	public function testGenerateShortCodeProducesUniqueValues()
	{
		$codes = [];
		$iterations = 10;

		for ($i = 0; $i < $iterations; $i++) {
			$codes[] = $this->linkService->generateShortCode();
		}
		// Ensure all generated codes are unique
		$uniqueCodes = array_unique($codes);
		$this->assertEquals($iterations, count($uniqueCodes));
	}

	/**
	 * Test performance/consistency: generate 100 codes and check length/alphanumeric
	 */
	public function testGenerateShortCodeBulkConsistency()
	{
		for ($i = 0; $i < 100; $i++) {
			$shortCode = $this->linkService->generateShortCode();

			$this->assertIsString($shortCode);
			$this->assertEquals(6, strlen($shortCode));
			$this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $shortCode);
		}
	}

	/**
	 * Test generateQrCode() returns valid base64-encoded PNG data URL
	 */
	public function testGenerateQrCodeReturnsValidDataUrl()
	{

		$result = $this->linkService->generateQrCode($this->url);

		$this->assertStringStartsWith('data:image/png;base64,', $result);
		$base64Part = substr($result, strlen('data:image/png;base64,'));
		$decoded = base64_decode($base64Part, true);
		$this->assertNotFalse($decoded);
	}

	/**
	 * Test saving model data
	 */
	public function testSaveLinkInDatabase()
	{
		$link = Link::findOne(['host' => $this->url]);
		if ($link) {
			$link->delete();
		}

		$link = $this->linkService->save($this->url);
		$this->assertTrue(!$link->getErrors());
	}

	/**
	 * Test saving model data
	 */
	public function testCheckResourceAvailability()
	{
		$response = $this->linkService->checkResourceAvailability($this->url);
		$this->assertTrue($response['success']);
	}
}
