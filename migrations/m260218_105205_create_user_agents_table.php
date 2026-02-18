<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_agents}}`.
 */
class m260218_105205_create_user_agents_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_agents}}', [
            'id' => $this->primaryKey()->unsigned(),
			'ip' => $this->string(100)->notNull()->comment('Юзер агент пользователя'),
			'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
			'updated_at' => $this->timestamp()->null()->defaultExpression('NULL ON UPDATE CURRENT_TIMESTAMP'),
        ]);

		$this->createIndex('idx_ip', '{{%user_agents}}', 'ip');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_agents}}');
    }
}
