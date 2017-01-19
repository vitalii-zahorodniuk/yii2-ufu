<?php
namespace xz1mefx\ufu\models\traits;

use xz1mefx\ufu\models\UfuCategory;
use yii\helpers\ArrayHelper;

/**
 * Class CategoryTreeTrait
 * @package xz1mefx\ufu\models\traits
 */
trait CategoryTreeTrait
{

    private static $_cachedItemsTree = NULL;
    private static $_cachedItemsTreeFlat = NULL;

    /**
     * @param      $id
     * @param bool $resetCache
     *
     * @return null
     */
    public static function getTreeItem($id, $resetCache = FALSE)
    {
        self::resetItemsIdTreeCache();
        $res = self::collectItemsIdTree(TRUE);
        return empty($res[$id]) ? NULL : $res[$id];
    }

    /**
     *
     */
    public static function resetItemsIdTreeCache()
    {
        self::$_cachedItemsTree = NULL;
        self::$_cachedItemsTreeFlat = NULL;
    }

    /**
     * @param bool $flat
     * @param int  $parent_id
     *
     * @return array|null
     */
    public static function collectItemsIdTree($flat = FALSE, $parent_id = 0)
    {
        if (self::$_cachedItemsTree === NULL || self::$_cachedItemsTreeFlat === NULL) {
            $preparedData = ArrayHelper::map(
                UfuCategory::find()
                    ->joinWith('ufuUrl')
                    ->select([
                        UfuCategory::tableName() . '.id',
                        UfuCategory::tableName() . '.parent_id',
                        UfuCategory::TABLE_ALIAS_UFU_URL . '.url',
                    ])
                    ->asArray()
                    ->all(),
                'id',
                function ($element) {
                    /* @var self $element */
                    return [
                        'id' => (int)$element['id'],
                        'parent_id' => (int)$element['parent_id'],
                        'url' => $element['url'],
                    ];
                },
                'parent_id'
            );
            self::$_cachedItemsTree = self::_collectItemsRecursive($preparedData, self::$_cachedItemsTreeFlat, $parent_id);
            unset($preparedData);
        }

        return $flat ? self::$_cachedItemsTreeFlat : self::$_cachedItemsTree;
    }

    /**
     * @param        $data
     * @param array  $flatData
     * @param int    $parent_id
     * @param array  $parentsList
     * @param int    $level
     * @param string $path
     *
     * @return array|mixed
     */
    private static function _collectItemsRecursive(&$data, &$flatData = [], $parent_id = 0, $parentsList = [], $level = 1, $path = '')
    {
        $res = [
            'items' => [],
            'childs' => [],
        ];

        if (isset($data[$parent_id])) {
            foreach ($data[$parent_id] as $category) {
                $preparedParentsList = $parentsList;
                $preparedFullPath = trim($path . '/' . $category['url'], '/');
                if ($parent_id > 0) {
                    $preparedParentsList[] = $category['parent_id'];
                }

                $recursiveData = self::_collectItemsRecursive($data, $flatData, $category['id'], $preparedParentsList, $level + 1, $preparedFullPath);

                $flatData[$category['id']] = $res['items'][$category['id']] = [
                    'id' => $category['id'],
                    'parent_id' => $category['parent_id'],
                    'level' => $level,
                    'parents_id_list' => $preparedParentsList,
                    'children_id_list' => $recursiveData['childs'],
                    'url' => $category['url'],
                    'full_path' => $preparedFullPath,
                ];
                $res['items'][$category['id']]['children_data_list'] = $recursiveData['items'];

                $res['childs'] = array_merge($res['childs'], $recursiveData['childs']);
                $res['childs'][] = $category['id'];
            }
        }

        return $parent_id == 0 ? $res['items'] : $res;
    }

}
