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
        // registering module factories from the outside
        $this->context->getShell()->registerModule(Root_Modules::MODULE_PLAIN, function($data) {
            return new \stack\module\Plain($data);
        });
        $this->context->getShell()->registerModule(Root_Modules::MODULE_USER, function($data) {
            return \stack\module\User::create($data);
        });
        $this->context->getShell()->registerModule(Root_Modules::MODULE_GROUP, function($data) {
            return \stack\module\Group::create($data);
        });
        $this->context->getShell()->registerModule(Root_Modules::MODULE_ADDUSER, function($data) {
            return \stack\module\run\AddUser::create($data);
        });
        $this->context->getShell()->registerModule(Root_Modules::MODULE_DELUSER, function($data) {
            return \stack\module\run\DelUser::create($data);
        });
        $this->context->getShell()->registerModule(Root_Modules::MODULE_ADDGROUP, function($data) {
            return \stack\module\run\AddGroup::create($data);
        });
        $this->context->getShell()->registerModule(Root_Modules::MODULE_DELGROUP, function($data) {
            return \stack\module\run\DelGroup::create($data);
        });
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
     * @return TestEnvironment
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

        $args = func_get_args();
        array_shift($args); // shift off @param $path
        array_unshift($args, $this->context); // unshift application context as new first argument
        return call_user_func_array(array($file->getModule(), 'run'), $args);
    }

    /**
     * @param string $uname
     * @return \stack\module\User
     */
    public function getUser($uname) {
        $user = $this->context->getShell()->readUser($uname);
        return $user->getModule();
    }

    /**
     * @param string $uname
     * @param string $password
     */
    public function addUser($uname, $password) {
        $this->runFile($this->getRunPath('adduser'), $uname, $password);
    }

    /**
     * @param $uname
     */
    public function delUser($uname) {
        $this->runFile($this->getRunPath('deluser'), $uname);
    }

    /**
     * @param string $gname
     * @param string $founder
     */
    public function addGroup($gname, $founder) {
        $this->runFile($this->getRunPath('addgroup'), $gname, $founder);
    }

    /**
     * @param string $gname
     * @return \stack\module\Group
     */
    public function getGroup($gname) {
        $group = $this->context->getShell()->readGroup($gname);
        return $group->getModule();
    }

    /**
     * @param string $gname
     */
    public function delGroup($gname) {
        $this->runFile($this->getRunPath('delgroup'), $gname);
    }
}