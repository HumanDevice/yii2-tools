<?php

namespace frontend\controllers;

use common\components\web\Controller;
use common\components\web\JsAction;
use common\components\web\View;

/**
 * Controller renders JS via storage sent from View component.
 */
class JsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'ready' => [
                'class' => JsAction::className(),
                'storageKey' => View::STORAGE_JS_READY,
                'template' => "jQuery(document).ready(function () {\n{js}\n});"
            ],
            'load' => [
                'class' => JsAction::className(),
                'storageKey' => View::STORAGE_JS_LOAD,
                'template' => "jQuery(window).load(function () {\n{js}\n});"
            ],
            'ajax' => [
                'class' => JsAction::className(),
                'storageKey' => View::STORAGE_JS_AJAX
            ],
            'begin' => [
                'class' => JsAction::className(),
                'storageKey' => View::STORAGE_JS_BEGIN
            ],
            'end' => [
                'class' => JsAction::className(),
                'storageKey' => View::STORAGE_JS_END
            ],
            'head' => [
                'class' => JsAction::className(),
                'storageKey' => View::STORAGE_JS_HEAD
            ],
        ];
    }
}
