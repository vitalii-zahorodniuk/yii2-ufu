<?php
namespace xz1mefx\ufu\widgets;

use yii\bootstrap\Html;
use yii\widgets\InputWidget;

/**
 * Class CategoryTreeWidget
 * @package xz1mefx\ufu\widgets
 */
class UrlInputWidget extends InputWidget
{

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->renderWidget();
    }

    /**
     * @return mixed
     */
    public function renderWidget()
    {
        Html::addCssClass($this->options, 'form-control');
        $options = array_merge(['autocomplete' => 'off'], $this->options);
        $name = isset($options['name']) ? $options['name'] : Html::getInputName($this->model, $this->attribute);
        $value = isset($options['value']) ? $options['value'] : Html::getAttributeValue($this->model, $this->attribute);
        if (!array_key_exists('id', $options)) {
            $options['id'] = Html::getInputId($this->model, $this->attribute);
        }
        $this->view->registerJs(<<<JS
function trans(elVal) {
    if (elVal.length > 0) {
        elVal = elVal.toLowerCase();
    }
    var replacements = {
        'а': 'a',
        'б': 'b',
        'в': 'v',
        'г': 'g',
        'ґ': 'g',
        'д': 'd',
        'е': 'e',
        'є': 'e',
        'ё': 'yo',
        'ж': 'zh',
        'з': 'z',
        'и': 'i',
        'і': 'i',
        'ї': 'i',
        'й': 'j',
        'к': 'k',
        'л': 'l',
        'м': 'm',
        'н': 'n',
        'о': 'o',
        'п': 'p',
        'р': 'r',
        'с': 's',
        'т': 't',
        'у': 'u',
        'ф': 'f',
        'х': 'h',
        'ц': 'c',
        'ч': 'ch',
        'ш': 'sh',
        'щ': 'sch',
        'ъ': '',
        'ы': 'i',
        'ь': '',
        'э': 'e',
        'ю': 'yu',
        'я': 'ya'
    };
    var result = '';
    for (var i = 0; i < elVal.length; i++) {
        if (replacements[elVal[i]] === undefined) {
            result += (/[a-z0-9-]/.test(elVal[i])) ? elVal[i] : '-';
        } else {
            result += replacements[elVal[i]];
        }
    }
    return result.replace(/-{2,}/ig, '-');
}
$('input[name="{$name}"]').on('keyup blur', function () {
    var el = $(this);
    if (el.val().match(/[^0-9a-z-]|-{2,}/g)) {
        el.val(trans(el.val()));
        el.trigger('change');
    }
});
JS
        );
        return Html::input('text', $name, $value, $options);
    }
}
