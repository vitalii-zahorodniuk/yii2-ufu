<?php
use xz1mefx\multilang\models\Language;
use yii\db\Migration;

/**
 * Class m161223_113345_ufu_init
 */
class m161223_113345_ufu_init extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->down();

        $tableOptions = NULL;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }


        // -------------------------------------------
        // Create url table
        // -------------------------------------------

        $this->createTable('{{%ufu_url}}', [
            'id' => $this->primaryKey()->unsigned(),
            'type' => $this->smallInteger()->unsigned()->notNull(),
            'item_id' => $this->integer()->unsigned()->notNull(),

            'url' => $this->string()->notNull(),
            'full_path_hash' => $this->string(32)->null(),

            'created_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ], $tableOptions);


        // -------------------------------------------
        // Create category tables
        // -------------------------------------------

        $this->createTable('{{%ufu_category}}', [
            'id' => $this->primaryKey()->unsigned(),
            'parent_id' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'url_id' => $this->integer()->unsigned()->notNull(),

            'created_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('ufu_category_url_id', '{{%ufu_category}}', 'url_id');
        $this->addForeignKey('ufu_category_url_id_fk', '{{%ufu_category}}', 'url_id', '{{%ufu_url}}', 'id', 'RESTRICT', 'RESTRICT');

        $this->createTable('{{%ufu_category_translate}}', [
            'id' => $this->primaryKey()->unsigned(),
            'category_id' => $this->integer()->unsigned()->notNull(),
            'language_id' => $this->integer()->unsigned()->notNull(),

            'name' => $this->string()->notNull(),

            'created_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('ufu_category_translate_category_id', '{{%ufu_category_translate}}', 'category_id');
        $this->createIndex('ufu_category_translate_language_id', '{{%ufu_category_translate}}', 'language_id');
        $this->addForeignKey('ufu_category_translate_category_id_fk', '{{%ufu_category_translate}}', 'category_id', '{{%ufu_category}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('ufu_category_translate_language_id_fk', '{{%ufu_category_translate}}', 'language_id', Language::TABLE_NAME, 'id', 'RESTRICT', 'RESTRICT');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        if (Yii::$app->db->schema->getTableSchema('{{%ufu_category_translate}}') !== NULL) {
            $this->dropTable('{{%ufu_category_translate}}');
        }
        if (Yii::$app->db->schema->getTableSchema('{{%ufu_category}}') !== NULL) {
            $this->dropTable('{{%ufu_category}}');
        }
        if (Yii::$app->db->schema->getTableSchema('{{%ufu_url}}') !== NULL) {
            $this->dropTable('{{%ufu_url}}');
        }
    }

}
