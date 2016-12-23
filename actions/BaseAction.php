<?php
namespace xz1mefx\hfu\actions;

use yii\base\Action;

/**
 * Class BaseAction
 * @package xz1mefx\hfu\actions
 */
class BaseAction extends Action
{

    const THEME_BOOTSTRAP = 'bootstrap';
    const THEME_ADMINLTE = 'adminlte';

    public $theme = self::THEME_BOOTSTRAP;
    public $view = NULL;

}
