<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%links}}`.
 */
class m260218_105030_create_links_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%links}}', [
			'id' => $this->primaryKey()->unsigned(),
			'host' => $this->string(255)->notNull(),
			'short' => $this->string(255)->notNull(),
			'counter' => $this->integer()->notNull()->defaultValue(0),
			'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
			'updated_at' => $this->timestamp()->null()->defaultExpression('NULL ON UPDATE CURRENT_TIMESTAMP')
        ]);

		$this->createIndex('idx_host', '{{%links}}', 'host');
		$this->createIndex('unique_key_host', '{{%links}}', 'host', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%links}}');
    }
}
