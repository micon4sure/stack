<?php
namespace stack;

class A {
    public function init() {

    }
}

class B extends A {
    public function init($foo) {

    }
}

return;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

include __DIR__ . '/bootstrap.php';

define('STACK_APPLICATION_ROOT', STACK_ROOT . '/test');

// create anonymous shutdown function
$shutdown = function($error = null) {
    if($error === null) {
        $error = error_get_last();
    }
    if($error) {
        // an uncaught exception has occured
        (new \stack\web\Response_HTML(null, 500, 'Internal Server Error'))->send();
        printf("<h1>%d: %s</h1><h2>%s#%d</h2>",
            $error->getCode(),
            \lean\Text::len($error->getMessage()) ? $error->getMessage() : get_class($error),
            $error->getFile(),
            $error->getLine());
        \lean\util\Dump::create()->flush()->goes($error);
        \lean\util\Dump::create(2)->flush()->goes('trace:');
        $temp = debug_backtrace();
        array_walk($temp, function($item) {
            echo '# ------- FATAL --------- #' . "\n";
            if(isset($item['function']))
                static $i;
            \lean\util\Dump::create()->flush()->goes('#' . ++$i . ' ' . $item['function'] . "\n");
        });
    }
};
// shutdown and error handlers
register_shutdown_function($shutdown);
set_error_handler(function($code, $message, $file, $line) {
    throw new \ErrorException($message, $code, null, $file, $line);
});

// run application, handle uncaught exceptions with shutdown function
try {
    $environment = new Environment('development');
    $context = new Context($environment);
    $application = new \stack\web\Application($context);
    $application->run();
} catch(\Exception $e) {
    $shutdown($e);
}
