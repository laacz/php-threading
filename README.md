Implementation of PHP Threading
===============================

This is PHP-only implementation of threading (using `pcntl_fork()`).

Requirements
------------

* Decent PHP version (5.something, I believe).
* PCNTL extension compiled in.

Example
-------

``` php
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

for ($i = 0 ; $i < 4; $i++) {
    new myThread();
}

Threading::join();
```

TODO
----

* Implement transparent inter-process communication.
* Add proper error handling.
* Write tests.