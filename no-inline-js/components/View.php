<?php

namespace common\components\web;

use Yii;
use yii\helpers\Html;
use yii\web\View as YiiView;

/**
 * View component rendering inline JS in separate file.
 *
 * @author Bizley
 */
class View extends YiiView
{
    /**
     * Renders the content to be inserted in the head section.
     * The content is rendered using the registered meta tags, link tags, CSS/JS code blocks and files.
     * @return string the rendered content
     */
    protected function renderHeadHtml()
    {
        $lines = [];
        if (!empty($this->metaTags)) {
            $lines[] = implode("\n", $this->metaTags);
        }

        if (!empty($this->linkTags)) {
            $lines[] = implode("\n", $this->linkTags);
        }
        if (!empty($this->cssFiles)) {
            $lines[] = implode("\n", $this->cssFiles);
        }
        if (!empty($this->css)) {
            $lines[] = implode("\n", $this->css);
        }
        if (!empty($this->jsFiles[self::POS_HEAD])) {
            $lines[] = implode("\n", $this->jsFiles[self::POS_HEAD]);
        }
        if (!empty($this->js[self::POS_HEAD])) {
            Yii::$app->session->set('js-head', $this->js[self::POS_HEAD]);
            $lines[] = Html::jsFile(['js/head']);
        }

        return empty($lines) ? '' : implode("\n", $lines);
    }
    
    /**
     * Renders the content to be inserted at the beginning of the body section.
     * The content is rendered using the registered JS code blocks and files.
     * @return string the rendered content
     */
    protected function renderBodyBeginHtml()
    {
        $lines = [];
        if (!empty($this->jsFiles[self::POS_BEGIN])) {
            $lines[] = implode("\n", $this->jsFiles[self::POS_BEGIN]);
        }
        if (!empty($this->js[self::POS_BEGIN])) {
            Yii::$app->session->set('js-begin', $this->js[self::POS_BEGIN]);
            $lines[] = Html::jsFile(['js/begin']);
        }

        return empty($lines) ? '' : implode("\n", $lines);
    }
    
    /**
     * Renders the content to be inserted at the end of the body section.
     * The content is rendered using the registered JS code blocks and files.
     * @param boolean $ajaxMode whether the view is rendering in AJAX mode.
     * If true, the JS scripts registered at [[POS_READY]] and [[POS_LOAD]] positions
     * will be rendered at the end of the view like normal scripts.
     * @return string the rendered content
     */
    protected function renderBodyEndHtml($ajaxMode)
    {
        $lines = [];

        if (!empty($this->jsFiles[self::POS_END])) {
            $lines[] = implode("\n", $this->jsFiles[self::POS_END]);
        }

        if ($ajaxMode) {
            $scripts = [];
            if (!empty($this->js[self::POS_END])) {
                $scripts[] = implode("\n", $this->js[self::POS_END]);
            }
            if (!empty($this->js[self::POS_READY])) {
                $scripts[] = implode("\n", $this->js[self::POS_READY]);
            }
            if (!empty($this->js[self::POS_LOAD])) {
                $scripts[] = implode("\n", $this->js[self::POS_LOAD]);
            }
            if (!empty($scripts)) {
                Yii::$app->session->set('js-ajax', $scripts);
                $lines[] = Html::jsFile(['js/ajax']);
            }
        } else {
            if (!empty($this->js[self::POS_END])) {
                Yii::$app->session->set('js-end', $this->js[self::POS_END]);
                $lines[] = Html::jsFile(['js/end']);
            }
            if (!empty($this->js[self::POS_READY])) {
                Yii::$app->session->set('js-ready', $this->js[self::POS_READY]);
                $lines[] = Html::jsFile(['js/ready']);
            }
            if (!empty($this->js[self::POS_LOAD])) {
                Yii::$app->session->set('js-load', $this->js[self::POS_LOAD]);
                $lines[] = Html::jsFile(['js/load']);
            }
        }

        return empty($lines) ? '' : implode("\n", $lines);
    }
}
