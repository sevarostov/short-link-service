<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class UserIp
 *
 * Represents user IP addresses that have visited short links.
 * Each record contains a unique IP address and timestamps for tracking.
 * Used in conjunction with UserIpLinkLog to track link visit analytics.
 *
 * @property int $id Primary key
 * @property string $ip IP address
 * @property string $created_at Timestamp when the IP was first recorded
 * @property string|null $updated_at Timestamp of last update (automatically updated)
 */
class UserIp extends ActiveRecord
{

	public static function tableName()
	{
		return '{{%user_ips}}';
	}

	public function rules()
	{
		return [
			[['ip'], 'required'],
			[['ip'], 'string', 'max' => 100],
			[['created_at', 'updated_at'], 'safe'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label).
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'ip' => 'IP Address',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
		];
	}

	/**
	 * Gets the related visit logs for this IP address.
	 * @return ActiveQuery
	 */
	public function getVisitLogs(): ActiveQuery
	{
		return $this->hasMany(UserIpLinkLog::class, ['user_ip_id' => 'id']);
	}

	/**
	 * Before saving, ensure IP is properly formatted and validated.
	 * @return bool
	 */
	public function beforeSave($insert): bool
	{
		if (parent::beforeSave($insert)) {
			$this->ip = trim($this->ip);

			// Validate IP format (IPv4/IPv6)
			if (!filter_var($this->ip, FILTER_VALIDATE_IP)) {
				$this->addError('ip', 'Invalid IP address format.');
				return false;
			}

			return true;
		}
		return false;
	}
}
