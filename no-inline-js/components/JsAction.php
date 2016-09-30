<?php

namespace common\components\web;

use ArrayAccess;
use Yii;
use yii\base\Action;
use yii\di\Instance;
use yii\web\Response;

/**
 * JsAction.
 * Renders actions for JsController.
 */
class JsAction extends Action
{
    /**
     * @var string storage component key
     */
    public $storageKey;
    /**
     * @var string|array|ArrayAccess storage component
     */
    public $storage = 'session';
    /**
     * @var string output template where {js} is the code of the storage's storageKey item.
     */
    public $template = '{js}';
    
    /**
     * Ensures storage component is set.
     */
    public function init()
    {
        parent::init();
        $this->storage = Instance::ensure($this->storage, '\ArrayAccess');
    }
    
    /**
     * Renders output
     * @return string
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'application/javascript; charset=UTF-8');
        $src = [];
        if ($this->storage->offsetExists($this->storageKey)) {
            $src = $this->storage->offsetGet($this->storageKey);
        }
        return strtr($this->template, ['{js}' => implode("\n", $src)]);
    }
}
