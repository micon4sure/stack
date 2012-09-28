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
     * @var array
     */
    private $bundles = array();

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
        $this->registerBundle(new Bundle_Core($this));
    }

    /**
     * @param Bundle $bundle
     */
    public function registerBundle(Bundle $bundle) {
        $this->bundles[$bundle->getName()] = $bundle;
        $bundle->registerModules($this->getShell());
    }

    /**
     * @param $name
     *
     * @return Bundle
     */
    public function getBundle($name) {
        if(!array_key_exists($name, $this->bundles)) {
            throw new Exception_BundleNotFound("The bundle with the name '$name' was not found. Maybe it is not registered?");
        }
        return $this->bundles[$name];
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
        return Root::ROOT_PATH_RUN . "/$path";
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

        $module->init($this);

        $args = func_get_args();
        array_shift($args); // shift off param $path
        array_unshift($args, $this->context); // unshift application context as new first argument
        return call_user_func_array(array($module, 'run'), $args);
    }
}