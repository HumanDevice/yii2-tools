<?php

namespace console\controllers;

/**
 * Test A Worker
 *
 * @author Bizley
 */
class TestAController extends WorkerController
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
    
    /**
     * Test A job.
     */
    public function job()
    {
        $this->log("Test A");
    }
}
