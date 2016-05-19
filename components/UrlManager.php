<?php

namespace common\components;

use Yii;
use yii\web\UrlManager as YiiUrlManager;

/**
 * UrlManager
 * Allows to translate urls dynamically.
 */
class UrlManager extends YiiUrlManager
{
    
    public $enablePrettyUrl = true;
    public $showScriptName  = false;
    
    public $language;
    
    /**
     * Translated controllers names.
     * @var array 
     */
    public $languageControllers = [
        'pl' => [
            'site'                 => 'serwis',
            'account'              => 'konto',
            'articles'             => 'artykuly',
            'addresses'            => 'adresy',
            'basket'               => 'koszyk',
            'credit-card'          => 'karta-kredytowa',
            'favourite-categories' => 'ulubione-kategorie',
            'favourite-shop'       => 'ulubiony-sklep',
            'notifications'        => 'powiadomienia',
            'orders'               => 'zamowienia',
            'payments'             => 'platnosci',
            'profile'              => 'profil',
            'shop'                 => 'sklep',
        ],
    ];
    
    /**
     * Translated actions names.
     * @var array 
     */
    public $languageActions = [
        'pl' => [
            'contact'    => 'kontakt',
            'about'      => 'o-nas',
            'login'      => 'logowanie',
            'activate'   => 'aktywacja',
            'reactivate' => 'reaktywacja',
            'password'   => 'haslo',
            'signup'     => 'rejestracja',
            'read'       => 'czytaj',
        ],
    ];
    
    /**
     * Initializes UrlManager.
     */
    public function init()
    {
        parent::init();
        
        if (empty($this->language)) {
            $this->language = Yii::$app->language;
        }
    }
    
    /**
     * Creates translated url.
     * @param array $params
     * @return string the created URL
     */
    public function createUrl($params)
    {
        if (!empty($params[0])) {
            $route = explode('/', $params[0]);
            if (isset($route[0]) && !empty($this->languageControllers[$this->language][$route[0]])) {
                $route[0] = $this->languageControllers[$this->language][$route[0]];
            }
            if (isset($route[1]) && !empty($this->languageActions[$this->language][$route[1]])) {
                $route[1] = $this->languageActions[$this->language][$route[1]];
            }
            $params[0] = implode('/', $route);
        }
        
        return parent::createUrl($params);
    }
    
    public function parseRequest($request)
    {
        if ($this->enablePrettyUrl) {
            /* @var $rule UrlRule */
            foreach ($this->rules as $rule) {
                if (($result = $rule->parseRequest($this, $request)) !== false) {
                    $routeParts = explode('/', $result[0]);
                    if (isset($routeParts[0]) && !empty($this->languageControllers[$this->language])) {
                        foreach ($this->languageControllers[$this->language] as $default => $localized) {
                            if ($localized == $routeParts[0]) {
                                $routeParts[0] = $default;
                                break;
                            }
                        }
                    }
                    if (isset($routeParts[1]) && !empty($this->languageActions[$this->language])) {
                        foreach ($this->languageActions[$this->language] as $default => $localized) {
                            if ($localized == $routeParts[1]) {
                                $routeParts[1] = $default;
                                break;
                            }
                        }
                    }
                    $result[0] = implode('/', $routeParts);
                    return $result;
                }
            }

            if ($this->enableStrictParsing) {
                return false;
            }

            Yii::trace('No matching URL rules. Using default URL parsing logic.', __METHOD__);

            $suffix = (string) $this->suffix;
            $pathInfo = $request->getPathInfo();
            if ($suffix !== '' && $pathInfo !== '') {
                $n = strlen($this->suffix);
                if (substr_compare($pathInfo, $this->suffix, -$n, $n) === 0) {
                    $pathInfo = substr($pathInfo, 0, -$n);
                    if ($pathInfo === '') {
                        // suffix alone is not allowed
                        return false;
                    }
                } else {
                    // suffix doesn't match
                    return false;
                }
            }

            return [$pathInfo, []];
        } else {
            Yii::trace('Pretty URL not enabled. Using default URL parsing logic.', __METHOD__);
            $route = $request->getQueryParam($this->routeParam, '');
            if (is_array($route)) {
                $route = '';
            }

            return [(string) $route, []];
        }
    }
}
