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
            'segment_level' => $this->smallInteger()->unsigned()->notNull()->defaultValue(1),
            'is_category' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0),
            'type' => $this->smallInteger()->unsigned()->notNull(),
            'item_id' => $this->integer()->unsigned()->notNull(),

            'url' => $this->string()->notNull(),
            'full_path_hash' => $this->string(32)->null(),

            'created_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ], $tableOptions);

//        $this->createIndex('ufu_url_segment_level', '{{%ufu_url}}', 'segment_level');
        $this->createIndex('ufu_url_is_category', '{{%ufu_url}}', 'is_category');
        $this->createIndex('ufu_url_type', '{{%ufu_url}}', 'type');
        $this->createIndex('ufu_url_item_id', '{{%ufu_url}}', 'item_id');
        $this->createIndex('ufu_url_full_path_hash', '{{%ufu_url}}', 'full_path_hash', TRUE);
        $this->createIndex('ufu_url_segment_level_url', '{{%ufu_url}}', ['segment_level', 'url'], TRUE);


        // -------------------------------------------
        // Create category tables
        // -------------------------------------------

        $this->createTable('{{%ufu_category}}', [
            'id' => $this->primaryKey()->unsigned(),
            'parent_id' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'parents_list' => $this->text()->notNull(),
            'children_list' => $this->text()->notNull(),

            'created_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('ufu_category_parent_id', '{{%ufu_category}}', 'parent_id');

        $this->createTable('{{%ufu_category_translate}}', [
            'id' => $this->primaryKey()->unsigned(),
            'category_id' => $this->integer()->unsigned()->notNull(),
            'language_id' => $this->integer()->unsigned()->notNull(),

            'name' => $this->string()->notNull(),

            'created_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('ufu_category_translate_category_id_language_id', '{{%ufu_category_translate}}', ['category_id', 'language_id'], TRUE);
        $this->addForeignKey('ufu_category_translate_category_id_fk', '{{%ufu_category_translate}}', 'category_id', '{{%ufu_category}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('ufu_category_translate_language_id_fk', '{{%ufu_category_translate}}', 'language_id', Language::TABLE_NAME, 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%ufu_category_relation}}', [
            'id' => $this->primaryKey()->unsigned(),
            'category_id' => $this->integer()->unsigned()->notNull(),
            'item_id' => $this->integer()->unsigned()->notNull(),

            'created_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->unsigned()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('ufu_category_relation_category_id_item_id', '{{%ufu_category_relation}}', ['category_id', 'item_id'], TRUE);
        $this->addForeignKey('ufu_category_relation_category_id_fk', '{{%ufu_category_relation}}', 'category_id', '{{%ufu_category}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        if (Yii::$app->db->schema->getTableSchema('{{%ufu_category_relation}}') !== NULL) {
            $this->dropTable('{{%ufu_category_relation}}');
        }
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
