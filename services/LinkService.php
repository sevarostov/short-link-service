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

	/**
	 * Check if a remote resource (URL) is accessible.
	 *
	 * @param string $url The full URL to check (must include protocol, e.g., https://)
	 * @param int $timeout Timeout in seconds (default: 10)
	 * @return array Returns ['success' => bool, 'httpCode' => int|null, 'error' => string|null]
	 */
	public function checkResourceAvailability(string $url, int $timeout = 10): array
	{
		// Normalize URL (ensure protocol)
		if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
			$url = 'https://' . $url;
		}

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_NOBODY, true);        // HEAD request (faster)
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL cert verification (for dev/testing)
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		// Execute request
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);

		curl_close($ch);

		// Evaluate result
		if ($response === false) {
			return [
				'success' => false,
				'httpCode' => null,
				'error' => $error ?: 'Request failed (no response)'
			];
		}

		// Consider HTTP 2xx/3xx as success
		$isSuccess = ($httpCode >= 200 && $httpCode < 400);

		return [
			'success' => $isSuccess,
			'httpCode' => $httpCode,
			'error' => $isSuccess ? null : ($error ?: "HTTP $httpCode")
		];
	}

}
