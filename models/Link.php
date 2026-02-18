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
	 * Before saving, ensure the short code is properly formatted.
	 * @return bool
	 */
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {

			$this->host = trim($this->host);
			$this->short = ($linkService = (new LinkService()))->generateShortCode();
			$this->qr_code = $linkService->generateQrCode($this->host);

			return true;
		}
		return false;
	}

	/**
	 * @param $params
	 *
	 * @return Link|null
	 * @throws Exception
	 */
	public function searchByHost($params): Link|null
	{
		$query = parent::find();
		$query->andFilterWhere([
			'host' => ArrayHelper::getValue($params, 'host'),
		]);
		return $query->one();
	}

}
