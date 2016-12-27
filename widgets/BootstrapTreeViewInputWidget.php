<?php
namespace xz1mefx\ufu\widgets;

use xz1mefx\widgets\assets\BootstrapTreeViewAsset;
use yii\base\InvalidValueException;
use yii\bootstrap\Html;
use yii\bootstrap\InputWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class BootstrapTreeViewInputWidget
 * @package xz1mefx\ufu\widgets
 */
class BootstrapTreeViewInputWidget extends \xz1mefx\widgets\BootstrapTreeViewInputWidget
{

    /**
     * @var string Custom id field
     */
    public $nodeCustomIdField = 'item-id';

    /**
     * @var array Nodes list
     *            In every item you must send '{$this->nodeCustomIdField}' with item id
     */
    public $pluginData = [];

    /**
     * @var array Bootstrap tree view plugin options.
     *            (see https://github.com/jonmiles/bootstrap-treeview/blob/master/README.md for more details)
     */
    public $pluginOptions = [];

    /**
     * @var array Default bootstrap tree view plugin options
     */
    private $defaultPluginOptions = [ // valid in v1.2.0
        'data' => [],
//        'backColor' => NULL,
//        'borderColor' => NULL,
        'checkedIcon' => "glyphicon glyphicon-check",
        'collapseIcon' => "glyphicon glyphicon-minus",
//        'color' => NULL,
        'emptyIcon' => "glyphicon",
        'enableLinks' => FALSE,
        'expandIcon' => "glyphicon glyphicon-plus",
        'highlightSearchResults' => TRUE,
        'highlightSelected' => TRUE,
        'levels' => 2,
        'multiSelect' => FALSE,
        'nodeIcon' => "glyphicon glyphicon-stop",
        'onhoverColor' => '#F5F5F5',
        'selectedIcon' => "glyphicon glyphicon-stop",
//        'searchResultBackColor' => NULL,
        'searchResultColor' => '#D9534F',
        'selectedBackColor' => '#428bca',
        'selectedColor' => '#FFFFFF',
        'showBorder' => TRUE,
        'showCheckbox' => FALSE,
        'showIcon' => FALSE,
        'showTags' => FALSE,
        'uncheckedIcon' => "glyphicon glyphicon-unchecked",
    ];

    /**
     * @var string Treeview unique element id
     */
    private $_id;

    /**
     * @var string
     */
    private $_name;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->initVariables();
        BootstrapTreeViewAsset::register($this->view);
        return $this->renderWidget();
    }

    /**
     * Init widget variables
     */
    private function initVariables()
    {
        if (!(isset($this->pluginData) && is_array($this->pluginData))) {
            throw new InvalidValueException("Incorrect pluginData value");
        }
        if (!(isset($this->pluginOptions) && is_array($this->pluginOptions))) {
            throw new InvalidValueException("Incorrect pluginOptions value");
        }

        $this->_id = $this->options['id'] . '-btv';
        $this->pluginOptions = ArrayHelper::merge($this->defaultPluginOptions, $this->pluginOptions);
        $this->pluginOptions['data'] = ArrayHelper::merge($this->pluginOptions['data'], $this->pluginData);

        if ($this->hasModel()) {
            $this->_name = Html::getInputName($this->model, $this->attribute) . ($this->pluginOptions['multiSelect'] ? '[]' : '');
        } else {
            $this->_name = $this->name . ($this->pluginOptions['multiSelect'] ? '[]' : '');
        }
    }

    /**
     * @return mixed
     */
    public function renderWidget()
    {
        $content = '<div>';
        if ($this->hasModel()) {
            $this->options['name'] = $this->_name;
            $content .= Html::activeHiddenInput($this->model, $this->attribute, ArrayHelper::merge($this->options, ['id' => FALSE]));
        } else {
            $content .= Html::hiddenInput($this->_name, $this->attribute, ArrayHelper::merge($this->options, ['id' => FALSE]));
        }
        $content .= Html::tag('div', '', ['id' => $this->_id]);

        $options = Json::encode($this->pluginOptions);
        $counter = self::$counter;
        $this->view->registerJs(<<<JS
var pluginEl{$counter} = $("#{$this->_id}");

// Init treeview
pluginEl{$counter}.treeview({$options});

// Init treeview events
function setInputs{$counter}() {
    $('input[name="{$this->_name}"]').remove();
    pluginEl{$counter}.treeview('getSelected').forEach(function (item, i, arr) {
        pluginEl{$counter}.parent().prepend('<input type="hidden" name="{$this->_name}" value="' + item['{$this->nodeCustomIdField}'] + '">');
    });
}
pluginEl{$counter}.on('nodeChecked', setInputs{$counter});
pluginEl{$counter}.on('nodeUnchecked', setInputs{$counter});
pluginEl{$counter}.on('nodeSelected', setInputs{$counter});
pluginEl{$counter}.on('nodeUnselected', setInputs{$counter});
JS
        );

        $content .= '</div>';

        return $content;
    }

}
