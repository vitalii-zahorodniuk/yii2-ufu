<?php
use xz1mefx\multilang\models\Language;
use yii\db\Migration;

/**
 * Class m161223_113345_hfu_init
 */
class m161223_113345_hfu_init extends Migration
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

        $this->createTable('{{%hfu_url}}', [
            'id' => $this->primaryKey()->unsigned(),
            'type' => $this->smallInteger()->unsigned()->notNull()->defaultValue(0),

            'url' => $this->string()->notNull(),

            'cache_md5_full_path' => $this->string(32)->null()->comment('md5 encrypted full path'),
            'cache_item_id' => $this->integer()->unsigned()->null()->comment('Right item (last segment) id'),
            'cache_items_list' => $this->string()->null()->comment('List of all path ids'),

            'created_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ], $tableOptions);


        // -------------------------------------------
        // Create category tables
        // -------------------------------------------

        $this->createTable('{{%hfu_category}}', [
            'id' => $this->primaryKey()->unsigned(),
            'parent_id' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'url_id' => $this->integer()->unsigned()->notNull(),

            'created_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('hfu_category_url_id', '{{%hfu_category}}', 'url_id');
        $this->addForeignKey('hfu_category_url_id_fk', '{{%hfu_category}}', 'url_id', '{{%hfu_url}}', 'id', 'RESTRICT', 'RESTRICT');

        $this->createTable('{{%hfu_category_translate}}', [
            'id' => $this->primaryKey()->unsigned(),
            'category_id' => $this->integer()->unsigned()->notNull(),
            'language_id' => $this->integer()->unsigned()->notNull(),

            'name' => $this->string()->notNull(),

            'created_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('hfu_category_translate_category_id', '{{%hfu_category_translate}}', 'category_id');
        $this->createIndex('hfu_category_translate_language_id', '{{%hfu_category_translate}}', 'language_id');
        $this->addForeignKey('hfu_category_translate_category_id_fk', '{{%hfu_category_translate}}', 'category_id', '{{%hfu_category}}', 'id', 'RESTRICT', 'RESTRICT');
        $this->addForeignKey('hfu_category_translate_language_id_fk', '{{%hfu_category_translate}}', 'language_id', Language::TABLE_NAME, 'id', 'RESTRICT', 'RESTRICT');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        if (Yii::$app->db->schema->getTableSchema('{{%hfu_category_translate}}') !== NULL) {
            $this->dropTable('{{%hfu_category_translate}}');
        }
        if (Yii::$app->db->schema->getTableSchema('{{%hfu_category}}') !== NULL) {
            $this->dropTable('{{%hfu_category}}');
        }
        if (Yii::$app->db->schema->getTableSchema('{{%hfu_url}}') !== NULL) {
            $this->dropTable('{{%hfu_url}}');
        }
    }

}
