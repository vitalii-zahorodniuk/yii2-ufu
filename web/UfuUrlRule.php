<?php
namespace xz1mefx\ufu\web;

use xz1mefx\multilang\web\UrlManager;
use xz1mefx\ufu\models\UfuUrl;
use Yii;
use yii\base\Object;
use yii\web\Request;
use yii\web\UrlRuleInterface;

/**
 * Class UfuUrlRule
 * @package xz1mefx\ufu\web
 */
class UfuUrlRule extends Object implements UrlRuleInterface
{

    /**
     * Creates a URL according to the given route and parameters.
     *
     * @param UrlManager $manager the URL manager
     * @param string     $route   the route. It should not have slashes at the beginning or the end.
     * @param array      $params  the parameters
     *
     * @return string|boolean the created URL, or false if this rule cannot be used for creating this URL.
     */
    public function createUrl($manager, $route, $params)
    {
        return FALSE;
        if (isset($params['id'])) {
            $routesList = array_merge(Yii::$app->ufu->getTypeById($urlModel->type, 'itemRoute'), Yii::$app->ufu->getTypeById($urlModel->type, 'categoryRoute'));
            if (in_array($route, $routesList)) {
            }
        }
    }

    /**
     * Parses the given request and returns the corresponding route and parameters.
     *
     * @param UrlManager $manager the URL manager
     * @param Request    $request the request component
     *
     * @return array|boolean the parsing result. The route and the parameters are returned as an array.
     * If false, it means this rule cannot be used to parse this path info.
     */
    public function parseRequest($manager, $request)
    {
        $urlModel = UfuUrl::findByPathInfo($request->pathInfo);
        if ($urlModel) {
            $typeRoute = Yii::$app->ufu->getTypeById($urlModel->type, $urlModel->is_category ? 'categoryRoute' : 'itemRoute');
            return [$typeRoute, ['id' => $urlModel->item_id]];
        }
        return FALSE;
    }
}