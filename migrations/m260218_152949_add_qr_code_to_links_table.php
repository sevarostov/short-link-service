<?php

use yii\db\Migration;

class m260218_152949_add_qr_code_to_links_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn(
			'{{%links}}',
			'qr_code',
			$this->string(1000)->notNull()->after('short')
		);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn('{{%links}}', 'qr_code');
    }
}
