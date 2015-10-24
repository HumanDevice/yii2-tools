<?php

namespace console\log;

use yii\log\FileTarget;

/**
 * WorkerFileTarget
 *
 * @author Bizley
 */
class WorkerFileTarget extends FileTarget
{
    public $logFile        = '@runtime/logs/worker.log';
    public $maxLogFiles    = 100;
    public $categories     = ['worker'];
    public $logVars        = [];
    public $exportInterval = 1;
    
    /**
     * Formats a log message for display as a string.
     * @param array $message the log message to be formatted.
     * The message structure follows that in [[Logger::messages]].
     * @return string the formatted message
     */
    public function formatMessage($message)
    {
        list($text, , , $timestamp) = $message;
        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof \Exception) {
                $text = (string) $text;
            } else {
                $text = VarDumper::export($text);
            }
        }

        return date('Y-m-d H:i:s', $timestamp) . " $text";
    }
}
