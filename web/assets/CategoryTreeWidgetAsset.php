<?php
namespace xz1mefx\ufu\web\assets;

use yii\web\AssetBundle;

/**
 * Class CategoryTreeWidgetAsset
 * @package xz1mefx\ufu\web\assets
 */
class CategoryTreeWidgetAsset extends AssetBundle
{

    public $sourcePath = '@vendor/xz1mefx/yii2-ufu/assets/category-tree-widget';

    public $css = [
//        'styles.css',
        'styles.min.css',
    ];

    public $js = [
//        'scripts.js',
        'scripts.min.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

}
