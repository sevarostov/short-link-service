<?php

namespace app\services;

use app\models\Link;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use yii\db\Exception;

class LinkService
{
	/**
	 * Generate a random short code (e.g., 6 alphanumeric chars)
	 * @return string
	 */
	public function generateShortCode()
	{
		return substr(md5(uniqid()), 0, 6);
	}

	/**
	 *
	 * @param string $url
	 *
	 * @return Link
	 */
	public function save(string $url): Link
	{
		$link = new Link();
		$link->host = $url;
		$link->short = $this->generateShortCode();
		$link->counter = 0;
		$link->qr_code = $this->generateQrCode($url);

		if (!$link->validate()) {
			return $link;
		}

		try {
			$link->save(false);
		} catch (Exception $exception) {
			//@todo loging errors
		}

		return $link;
	}

	/**
	 * Generate a QR code image (base64-encoded PNG)
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public function generateQrCode(string $url)
	{
		$qrCode = new QrCode($url);
		$writer = new PngWriter();
		$result = $writer->write($qrCode);

		return 'data:image/png;base64,' . base64_encode($result->getString());
	}
}
