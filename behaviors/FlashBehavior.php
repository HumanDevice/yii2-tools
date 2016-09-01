<?php

/**
 * ###############################################################################
 * @author Pawel Brzozowski <pb@human-device.com>
 * @copyright Copyright (c) 2016, Human Device Sp. z o.o. https://human-device.com
 * ###############################################################################
 */
 
namespace behaviors;

use Yii;
use yii\base\Behavior;

/**
 * Flash Behavior
 * Simplifies flash messages adding. Every message is automatically translated.
 * Prepares messages for Alert widget.
 */
class FlashBehavior extends Behavior
{

    /**
     * Default message category.
     */
    const DEFAULT_CATEGORY = 'app';
    
    /**
     * Alias for [[warning()]].
     * @param string $message the flash message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $category the message category.
     * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
     * [[\yii\base\Application::language|application language]] will be used.
     */
    public function alert($message, $params = [], $category = self::DEFAULT_CATEGORY, $language = null)
    {
        $this->warning($message, $params, $category, $language);
    }
    
    /**
     * Adds flash message of 'danger' type.
     * @param string $message the flash message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $category the message category.
     * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
     * [[\yii\base\Application::language|application language]] will be used.
     */
    public function danger($message, $params = [], $category = self::DEFAULT_CATEGORY, $language = null)
    {
        $this->goFlash('danger', $category, $message, $params, $language);
    }
    
    /**
     * Alias for [[danger()]].
     * @param string $message the flash message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $category the message category.
     * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
     * [[\yii\base\Application::language|application language]] will be used.
     */
    public function error($message, $params = [], $category = self::DEFAULT_CATEGORY, $language = null)
    {
        $this->danger($message, $params, $category, $language);
    }
    
    /**
     * Adds flash message of given type.
     * @param string $type the type of flash message.
     * @param string $category the message category.
     * @param string $message the flash message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
     * [[\yii\base\Application::language|application language]] will be used.
     */
    public function goFlash($type, $category, $message, $params, $language)
    {
        Yii::$app->session->addFlash($type, Yii::t($category, $message, $params, $language));
    }
    
    /**
     * Adds flash message of 'info' type.
     * @param string $message the flash message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $category the message category.
     * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
     * [[\yii\base\Application::language|application language]] will be used.
     */
    public function info($message, $params = [], $category = self::DEFAULT_CATEGORY, $language = null)
    {
        $this->goFlash('info', $category, $message, $params, $language);
    }
    
    /**
     * Alias for [[success()]].
     * @param string $message the flash message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $category the message category.
     * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
     * [[\yii\base\Application::language|application language]] will be used.
     */
    public function ok($message, $params = [], $category = self::DEFAULT_CATEGORY, $language = null)
    {
        $this->success($message, $params, $category, $language);
    }
    
    /**
     * Adds flash message of 'success' type.
     * @param string $message the flash message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $category the message category.
     * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
     * [[\yii\base\Application::language|application language]] will be used.
     */
    public function success($message, $params = [], $category = self::DEFAULT_CATEGORY, $language = null)
    {
        $this->goFlash('success', $category, $message, $params, $language);
    }
    
    /**
     * Adds flash message of 'warning' type.
     * @param string $message the flash message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $category the message category.
     * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
     * [[\yii\base\Application::language|application language]] will be used.
     */
    public function warning($message, $params = [], $category = self::DEFAULT_CATEGORY, $language = null)
    {
        $this->goFlash('warning', $category, $message, $params, $language);
    } 
}
