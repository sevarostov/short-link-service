<?php

namespace app\models;

use app\services\LinkService;
use Exception;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class Links
 *
 * @property int $id
 * @property string $host
 * @property string $short
 * @property string $qr_code
 * @property int $counter
 * @property string $created_at
 * @property string|null $updated_at
 *
 * Related models
 * @property UserIpLinkLog[] $visitLogs
 */
class Link extends ActiveRecord
{
	/**
	 * @return string the name of the table associated with this ActiveRecord class.
	 */
	public static function tableName()
	{
		return '{{%links}}';
	}

	/**
	 * @return array the validation rules.
	 */
	public function rules()
	{
		return [
			// Required fields
			[['host'], 'required'],
			['host', 'checkResourceAvailability'],
			// String length limits
			['host', 'string', 'max' => 255],
			['short', 'string', 'max' => 255],
			['qr_code', 'string', 'max' => 1000],

			// Counter must be integer ≥ 0
			['counter', 'integer', 'min' => 0],

			// URL format validation
			['host', 'url', 'defaultScheme' => 'https'],

			// Ensure uniqueness of host (as per your unique index)
			['host', 'unique'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label).
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'host' => 'Original URL',
			'short' => 'Short Code',
			'counter' => 'Click Count',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
		];
	}

	/**
	 * Increments the click counter by 1.
	 * @return bool whether the save was successful.
	 */
	public function incrementCounter()
	{
		$this->counter++;
		return $this->save(false, ['counter']); // Skip full validation, only update counter
	}

	/**
	 * Gets the full short URL (e.g., http://yourdomain.com/abc123).
	 * @return string
	 */
	public function getFullShortUrl()
	{
		$baseUrl = Yii::$app->request->getHostInfo();
		return $baseUrl . '/' . $this->short;
	}

	/**
	 * Before saving, ensure host is available
	 *
	 * the short code && qr_code generated
	 * @return bool
	 */
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {

			$this->host = trim($this->host);
			$this->short = ($linkService = new LinkService())->generateShortCode();
			$this->qr_code = $linkService->generateQrCode($this->host);

			return true;
		}
		return false;
	}

	/**
	 * @param array $params
	 * @param string $field
	 *
	 * @return Link|null
	 * @throws Exception
	 */
	public function searchBy(array $params, string $field): Link|null
	{
		$query = parent::find();

		match ($field) {
			'host' => $query
				->andFilterWhere([$field => ArrayHelper::getValue($params, $field),])
				->orFilterWhere([$field => 'https://' . ArrayHelper::getValue($params, $field),]),
			'short' => $query
				->andFilterWhere([$field => ArrayHelper::getValue($params, $field)]),
		};

		return $query->one();
	}

	/**
	 * @return void
	 */
	public function checkResourceAvailability(): void
	{
		$response = (new LinkService())
			->checkResourceAvailability($this->host);
		if (!$response['success']) {
			$this->addError('host', 'Данный URL недоступен!');
		}
	}

	public function getVisitLogs(): ActiveQuery
	{
		return $this->hasMany(UserIpLinkLog::class, ['link_id' => 'id']);
	}

	/**
	 * Gets the total number of visits for this link.
	 *
	 * @return int Number of visits
	 */
	public function getVisitCount(): int
	{
		return (int)$this->getVisitLogs()->count();
	}
}
