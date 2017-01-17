<?php
namespace xz1mefx\ufu\web;

use yii\base\Object;
use yii\web\UrlRuleInterface;

/**
 * Class UfuUrlRule
 * @package xz1mefx\ufu\web
 */
class UfuUrlRule extends Object implements UrlRuleInterface
{

    /**
     * @inheritdoc
     */
    public function createUrl($manager, $route, $params)
    {
        return FALSE;
        if ($route === 'car/index') {
            if (isset($params['manufacturer'], $params['model'])) {
                return $params['manufacturer'] . '/' . $params['model'];
            } elseif (isset($params['manufacturer'])) {
                return $params['manufacturer'];
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function parseRequest($manager, $request)
    {
        return FALSE;
        $pathInfo = $request->getPathInfo();
        if (preg_match('%^(\w+)(/(\w+))?$%', $pathInfo, $matches)) {
            // Ищем совпадения $matches[1] и $matches[3]
            // с данными manufacturer и model в базе данных
            // Если нашли, устанавливаем $params['manufacturer'] и/или $params['model']
            // и возвращаем ['car/index', $params]
        }
    }
}