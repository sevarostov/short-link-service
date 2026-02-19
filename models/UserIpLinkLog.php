<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class UserIpLinkLog
 *
 * Represents a log entry for tracking visits to short links by user IP addresses.
 *
 * @property int $id Primary key — unique identifier for the log entry
 * @property int $user_ip_id Foreign key referencing the user's IP address in the user_ips table
 * @property int $link_id Foreign key referencing the short link in the links table
 * @property string $created_at Timestamp when the visit was recorded (automatically set)
 *
 *  Связанные модели
 * @property UserIp $userIp Related UserIp model
 * @property Link $link Related Link model
 */
class UserIpLinkLog extends ActiveRecord
{
	public static function tableName()
	{
		return '{{%user_ip_link_logs}}';
	}

	public function rules()
	{
		return [
			[['user_ip_id', 'link_id'], 'required'],
			[['user_ip_id', 'link_id'], 'integer'],
			[['created_at'], 'safe'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label).
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'Log ID',
			'user_ip_id' => 'User IP ID',
			'link_id' => 'Link ID',
			'created_at' => 'Visit Timestamp',
		];
	}

	public function getUserIp(): ActiveQuery
	{
		return $this->hasOne(UserIp::class, ['id' => 'user_ip_id']);
	}

	public function getLink(): ActiveQuery
	{
		return $this->hasOne(Link::class, ['id' => 'link_id']);
	}
}
