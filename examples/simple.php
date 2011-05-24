<?php
/**
 * This file ir part of php-threading library.
 *
 * @link https://github.com/laacz/php-threading
 * @author Kaspars Foigts <laacz@laacz.lv>
 * @license The MIT License
 * @copyright Copyright(c) 2011 - present Kaspars Foigts <laacz@laacz.lv>
 */

require('../lib/threading.php');

class myThread extends Thread {
    
    function run() {
        $sleep = rand(1, 10);
        echo "{$this->name} Sleeping for {$sleep}s\n";
        sleep($sleep);
    }
    
    function finish() {
        echo "{$this->name} Done\n";
    }
    
    function stop() {
        $this->running = false;
    }
    
}

Threading::debug(true);

for ($i = 0 ; $i < 4; $i++) {
    new myThread();
}

Threading::join();
