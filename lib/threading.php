<?php
/**
 * This file ir part of php-threading library.
 *
 * @link https://github.com/laacz/php-threading
 * @author Kaspars Foigts <laacz@laacz.lv>
 * @license The MIT License
 * @copyright Copyright(c) 2011 - present Kaspars Foigts <laacz@laacz.lv>
 */


/**
 * Implement transparent interprocess communication.
 *
 * Requires PCNTL extension. Requires PHP 5.[something].
 * Does not work on Windows. Or DOS. Or ZX Spectrum.
 */

declare(ticks = 1);

/**
 * Static class. Handles threads internally. Provides some global methods for threads concept.
 */
class Threading {
    static $threads = Array();
    static $debug = false;
    
    /**
     * Sets or retreives debug setting.
     */
    function debug($debug = null) {
        if ($debug === null) {
            return self::$debug;
        } else {
            self::$debug = $debug;
        }
    }
    
    /**
     * Adds new child to the kindergarden.
     */
    function addThread($thread) {
        self::$threads[$thread->pid] = $thread;
    }
    
    /**
     * Returns count of active children.
     */
    function countThreads() {
        return count(self::$threads);
    }
    
    /**
     * Loop until all children are finished.
     */
    function join() {
        $status = -1;
        while (self::countThreads() > 0) {
            foreach (self::$threads as $thread) {
                usleep(100);
                if (pcntl_waitpid($thread->pid, $status, WNOHANG) > 0) {
                    unset(self::$threads[$thread->pid]);
                    if (self::debug()) {
                        echo "{$thread} exited. Status: {$status}. Now we have " . self::countThreads() . " threads\n";
                    }
                    break;
                }
            }
        }
    }
}

/**
 * Base class for Thread. Forks into child, runs run(), and finish(). MUST be extended.
 */
class Thread {
    
    var $pid = -1;
    var $name = null;
    var $is_child = false;
    
    /**
     * Constructor should not be overrinde. Override run() method and/or use setters/getters.
     */
    function __construct() {
        $pid = pcntl_fork();
        if ($pid === -1) {
            throw Exception('No fork for you.');
        } elseif ($pid === 0) {
            /**
             * This is child. Go to work.
             */
            $this->pid = getmypid();
            $this->name = 'Thread-' . $this->pid;
            $this->is_child = true;
            
            $this->run();
            $this->finish();
            
            /* Exiting child */
            exit;
        } else {
            /**
             * We're still in parent. Child forked OK.
             */
            echo "{$pid} launched\n";
            $this->pid = $pid;
            $this->name = 'Thread-' . $this->pid;
            Threading::addThread($this);
        }
    }
    
    /**
     * Dummy run() method.
     */
    function run() {}
    
    /**
     * Dummy finish() method.
     */
    function finish() {}
    
    /**
     * Sets thread's variable.
     */
    function __set($key, $value) {
        if ($this->is_child) {
            $this->$key = $value;
        } else {
            // Send $key:$value to child
        }
    }
    
    /**
     * Retreives variable from thread.
     */
    function __get($key) {
        if ($this->is_child) {
            return $this->$key;
        } else {
            // Receive $key from child
        }
    }

    /**
     * String representation of thread.
     */
    function __toString() {
        return "Thread: {$this->name}";
    }
}

