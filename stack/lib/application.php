<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */


/**
 * Provides an interface for modules in /root/system/run.
 * A custom Application class is expected for projects written on stack.
 * Also holds context.
 */
class Application {
    /**
     * @var Context
     */
    private $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context) {
        $this->context = $context;
        $this->init();
        \lean\Registry::instance()->set('stack.application', $this);
        \lean\Registry::instance()->set('stack.context', $this->context);
    }

    /**
     * Initialize Modules
     * @Overridable
     */
    protected function init() {
        (new \stack\Bundle_Core())->registerModules($this->getShell());
    }

    /**
     * @return Context
     */
    public function getContext() {
        return $this->context;
    }

    /**
     * @return Shell
     */
    public function getShell() {
        return $this->getContext()->getShell();
    }

    /**
     * @return Environment
     */
    public function getEnvironment() {
        return $this->getContext()->getEnvironment();
    }


    /**
     * @param string $path
     * @return string
     */
    protected function getRunPath($path) {
        return Root::ROOT_PATH_SYSTEM_RUN . "/$path";
    }

    /**
     * Execute a file via call_user_func_array
     *
     * @param string $path
     * @return mixed
     */
    protected function runFile($path) {
        $shell = $this->context->getShell();
        $file = $shell->readFile($path);

        $module = $file->getModule();

        $module->init();

        $args = func_get_args();
        array_shift($args); // shift off param $path
        array_unshift($args, $this->context); // unshift application context as new first argument
        return call_user_func_array(array($module, 'run'), $args);
    }
}