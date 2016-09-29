<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * Controller renders JS via session sent from View component.
 */
class JsController extends Controller
{
    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'application/javascript; charset=UTF-8');
        return parent::beforeAction($action);
    }
    
    public function actionReady()
    {
        $src = Yii::$app->session->get('js-ready', []);
        $js = "jQuery(document).ready(function () {\n" . implode("\n", $src) . "\n});";
        return $js;
    }
    
    public function actionLoad()
    {
        $src = Yii::$app->session->get('js-load', []);
        $js = "jQuery(window).load(function () {\n" . implode("\n", $src) . "\n});";
        return $js;
    }
    
    public function actionEnd()
    {
        $src = Yii::$app->session->get('js-end', []);
        $js = implode("\n", $src);
        return $js;
    }
    
    public function actionBegin()
    {
        $src = Yii::$app->session->get('js-begin', []);
        $js = implode("\n", $src);
        return $js;
    }
    
    public function actionAjax()
    {
        $src = Yii::$app->session->get('js-ajax', []);
        $js = implode("\n", $src);
        return $js;
    }
    
    public function actionHead()
    {
        $src = Yii::$app->session->get('js-head', []);
        $js = implode("\n", $src);
        return $js;
    }
}
