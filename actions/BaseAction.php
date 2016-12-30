<?php
namespace xz1mefx\ufu\actions;

use yii\base\Action;

/**
 * Class BaseAction
 * @package xz1mefx\ufu\actions
 */
class BaseAction extends Action
{

    const THEME_BOOTSTRAP = 'bootstrap';
    const THEME_ADMINLTE = 'adminlte';

    public $theme = self::THEME_BOOTSTRAP;
    public $view = NULL;

    /**
     * @var int
     */
    public $type = NULL;

}
