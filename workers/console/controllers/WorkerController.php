<?php

namespace console\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\console\Controller;

/**
 * Yii 2 General Job Worker
 *
 * @author Bizley
 */
class WorkerController extends Controller
{
    
    /**
     * @var string Worker name. Controller ID if empty.
     */
    public $name;
    /**
     * @var integer Worker interval in milliseconds.
     */
    public $interval = 1000;
    
    /**
     * @var boolean Whether to terminate script after $testTime milliseconds.
     */
    public $testRun = false;
    /**
     * @var integer Test termination time in milliseconds.
     */
    public $testTime = 10000;
    
    /**
     * @var boolean Whether to not show any message on screen.
     */
    public $silence = true;
    /**
     * @var boolean Whether to enter debug mode.
     */
    public $debug = false;
    
    private $_testStart;
    private $_nextTick = 0;
    
    /**
     * Init.
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!is_numeric($this->interval) || $this->interval < 0) {
            throw new InvalidConfigException('Interval must be integer greater than 0.');
        }
        if ($this->testRun && (!is_numeric($this->testTime) || $this->testTime < 0)) {
            throw new InvalidConfigException('TestRun must be integer greater than 0.');
        }
        if (empty($this->name)) {
            $this->name = $this->id;
        }
    }

    /**
     * General test worker to run.
     * Logs memory peak usage on finish.
     */
    public function actionRun()
    {
        try {
            $this->_testStart = microtime(true);
            $this->log("STARTED WORKER {$this->name}");
            $this->runWorker();
            $this->log("ENDED WORKER {$this->name}");
            if ($this->debug) {
                $this->log("MEMORY PEAK = " . $this->memoryPeak());
            }
        }
        catch (Exception $e) {
            Yii::error($e->getMessage());
        }
    }
    
    /**
     * Returns memory peak usage.
     * @return string
     */
    public function memoryPeak()
    {
        $usage  = memory_get_peak_usage();
        $memory = $usage;
        $unit   = 'B';
        if ($memory > 1024) {
            $memory = $usage / 1024;
            $unit   = 'kB';
            if ($memory > 1024) {
                $memory = $usage / 1024 / 1024;
                $unit   = 'MB';
            }
            if ($memory > 1024) {
                $memory = $usage / 1024 / 1024 / 1024;
                $unit   = 'GB';
            }
        }
        return round($memory, 2) . $unit . ' (' . $usage . 'B)';
    }

    /**
     * Checks if worker should be terminated on $testRun = true.
     * @return boolean
     */
    public function goOn()
    {
        if ($this->testRun) {
            if ($this->_testStart < microtime(true) - $this->testTime / 1000) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Logs messages.
     * If $silence = true messages are logged using Yii log component 
     * otherwise it sends them to STDOUT.
     * @param string $string
     */
    public function log($string)
    {
        if (!$this->silence) {
            $this->stdout(date('Y-m-d H:i:s') . " $string" . "\n");
        }
        else {
            Yii::info("[{$this->name}] $string", 'worker');
        }
    }

    /**
     * Provides loop for the worker.
     * Handles the $interval.
     */
    public function runWorker()
    {
        while ($this->goOn()) {
            $this->processInterval();
        }
    }
    
    /**
     * Sets next point in time when job shold be started.
     */
    public function setNextTick()
    {
        $this->_nextTick = microtime(true) + $this->interval / 1000;
    }

    /**
     * Checks whether to start next job batch.
     */
    public function processInterval()
    {
        if ($this->_nextTick < microtime(true)) {
            $this->setNextTick();
            $this->job();
        }
    }
    
    /**
     * Runs the job.
     * This method needs to be overridden.
     */
    public function job()
    {
        sleep(rand(0,1));
        $this->log("- tick @ " . microtime(true));
    }
}
