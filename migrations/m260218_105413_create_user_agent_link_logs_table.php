<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_agent_link_logs}}`.
 */
class m260218_105413_create_user_agent_link_logs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_agent_link_logs}}', [
            'id' => $this->primaryKey()->unsigned(),
			'user_agent_id' => $this->integer()->unsigned()->notNull()->comment('Идентификатор агента пользователя'),
			'link_id' => $this->integer()->unsigned()->notNull()->comment('Идентификатор ссылки'),
			'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

		$this->createIndex('idx_user_ag_id', '{{%user_agent_link_logs}}', 'user_agent_id');
		$this->createIndex('idx_link_id', '{{%user_agent_link_logs}}', 'link_id');
		$this->createIndex('idx_ua_link', '{{%user_agent_link_logs}}', ['user_agent_id', 'link_id']);

		$this->addForeignKey(
			'fk_user_ag',
			'{{%user_agent_link_logs}}',
			'user_agent_id',
			'{{%user_agents}}',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKey(
			'fk_link',
			'{{%user_agent_link_logs}}',
			'link_id',
			'{{%links}}',
			'id',
			'CASCADE',
			'CASCADE'
		);
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropForeignKey('fk_user_agent', '{{%user_agent_link_logs}}');
		$this->dropForeignKey('fk_link', '{{%user_agent_link_logs}}');

        $this->dropTable('{{%user_agent_link_logs}}');
    }
}
